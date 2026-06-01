<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Supplier;
use App\Models\User;
use App\Models\PurchaseOrder;
use App\Models\ReturnRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FullWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_full_purchase_order_to_credit_note_workflow()
    {
        Mail::fake();
        Storage::fake('public');

        // Create owner user
        $owner = User::factory()->create([
            'role' => 'owner',
            'email' => 'owner@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->actingAs($owner);

        // Create supplier through owner interface
        $supplierResponse = $this->post('/owner/suppliers', [
            'name' => 'Test Supplier',
            'contact_email' => 'supplier@example.com',
            'contact_phone' => '0123456789',
            'portal_enabled' => true,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);
        $supplierResponse->assertRedirect(route('owner.suppliers.index'));

        $supplier = Supplier::where('contact_email', 'supplier@example.com')->first();
        $this->assertNotNull($supplier);
        $this->assertNotNull($supplier->user);
        $this->assertEquals('supplier', $supplier->user->role);

        // Create an item
        $itemResponse = $this->post('/owner/items', [
            'name' => 'Test Item',
            'category' => 'Snacks',
            'quantity' => 10,
            'expiry_date' => now()->addDays(60)->toDateString(),
            'supplier_id' => $supplier->id,
            'reorder_point' => 5,
            'unit_price' => 2.50,
        ]);
        $itemResponse->assertRedirect(route('owner.items.index'));

        $item = Item::where('name', 'Test Item')->first();
        $this->assertNotNull($item);

        // Create purchase order
        $poResponse = $this->post('/owner/purchase-orders', [
            'item_id' => $item->id,
            'supplier_id' => $supplier->id,
            'quantity' => 20,
            'notes' => 'Order for restock',
        ]);
        $poResponse->assertRedirect(route('owner.purchase-orders.index'));

        $purchaseOrder = PurchaseOrder::where('supplier_id', $supplier->id)->first();
        $this->assertNotNull($purchaseOrder);
        $this->assertEquals('Pending', $purchaseOrder->status);
        $this->assertEquals(20, $purchaseOrder->quantity);

        // Supplier logs in and approves the order with delivery date
        $supplierUser = $supplier->user;
        $this->actingAs($supplierUser);

        $confirmResponse = $this->post("/supplier/purchase-orders/{$purchaseOrder->id}/confirm", [
            'status' => 'Approved',
            'delivery_date' => now()->addDays(1)->toDateString(),
            'delivery_time' => '09:00',
            'notes' => 'Delivery scheduled',
        ]);
        $confirmResponse->assertSessionHas('success');

        $purchaseOrder->refresh();
        $this->assertEquals('Approved', $purchaseOrder->status);
        $this->assertNotNull($purchaseOrder->delivery);

        // Owner reviews the approved purchase order and the invoice is auto-generated
        $this->actingAs($owner);

        $purchaseOrder->refresh();
        $this->assertEquals('Approved', $purchaseOrder->status);
        $this->assertNotNull($purchaseOrder->invoice);
        $this->assertEquals('INV-' . $purchaseOrder->po_number, $purchaseOrder->invoice->invoice_number);
        $this->assertEquals($purchaseOrder->final_amount, $purchaseOrder->invoice->total_amount);

        $invoiceViewResponse = $this->get(route('owner.invoices.show', $purchaseOrder->invoice));
        $invoiceViewResponse->assertStatus(200);

        $invoicePdfResponse = $this->get(route('owner.invoices.export-pdf', $purchaseOrder->invoice));
        $invoicePdfResponse->assertStatus(200);
        $invoicePdfResponse->assertHeader('Content-Type', 'application/pdf');

        // Create a return request against the approved purchase order
        $returnResponse = $this->post('/owner/return-requests', [
            'item_id' => $item->id,
            'supplier_id' => $supplier->id,
            'quantity' => 2,
            'reason' => 'Damaged',
            'po_id' => $purchaseOrder->id,
        ]);
        $returnResponse->assertSessionHas('success');

        $returnRequest = ReturnRequest::where('supplier_id', $supplier->id)->first();
        $this->assertNotNull($returnRequest);
        $this->assertEquals('Pending', $returnRequest->status);

        // Supplier approves the return request and credit note should be generated
        $this->actingAs($supplierUser);
        $approveResponse = $this->post("/supplier/return-requests/{$returnRequest->id}/status", [
            'status' => 'Approved',
        ]);
        $approveResponse->assertSessionHas('success');

        $returnRequest->refresh();
        $this->assertEquals('Approved', $returnRequest->status);
        $this->assertNotNull($returnRequest->creditNote);
        $this->assertEquals('Unused', $returnRequest->creditNote->status);
        $this->assertEquals(5.00, $returnRequest->creditNote->amount);

        // Owner can view credit note and export it to PDF
        $this->actingAs($owner);
        $pdfResponse = $this->get('/owner/credit-notes/export-pdf');
        $pdfResponse->assertStatus(200);
        $pdfResponse->assertHeader('Content-Type', 'application/pdf');

        // Check notifications were recorded for the workflow
        $this->assertDatabaseHas('notifications', [
            'user_id' => $supplierUser->id,
            'type' => 'purchase_order_created',
        ]);
        $this->assertDatabaseHas('notifications', [
            'user_id' => $owner->id,
            'type' => 'purchase_order_approved',
        ]);
        $this->assertDatabaseHas('notifications', [
            'user_id' => $owner->id,
            'type' => 'purchase_order_delivered',
        ]);
        $this->assertDatabaseHas('notifications', [
            'user_id' => $supplierUser->id,
            'type' => 'return_request_created',
        ]);
        $this->assertDatabaseHas('notifications', [
            'user_id' => $owner->id,
            'type' => 'return_request_approved',
        ]);
    }

    public function test_damaged_quantities_are_cleared_after_return_submission()
    {
        $owner = User::factory()->create([
            'role' => 'owner',
            'email' => 'owner-damage@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->actingAs($owner);

        $supplier = Supplier::create([
            'name' => 'Damaged Supplier',
            'contact_email' => 'damaged-supplier@example.com',
            'contact_phone' => '0123456789',
            'portal_enabled' => true,
        ]);

        $item = Item::create([
            'name' => 'Damaged Test Item',
            'category' => 'General',
            'quantity' => 10,
            'expiry_date' => now()->addDays(30)->toDateString(),
            'supplier_id' => $supplier->id,
            'reorder_point' => 5,
            'unit_price' => 4.00,
            'markup_percentage' => 20,
            'uom' => 'unit',
        ]);

        $this->patch("/owner/items/{$item->id}/mark-damaged", [
            'damage_reason' => 'Broken packaging',
            'damaged_quantity' => 3,
        ])->assertSessionHas('success');

        $item->refresh();
        $this->assertTrue($item->is_damaged);
        $this->assertEquals(3, $item->damaged_quantity);
        $this->assertEquals(10, $item->quantity);

        $purchaseOrder = PurchaseOrder::create([
            'po_number' => 'PO-TEST-001',
            'item_id' => $item->id,
            'quantity' => 10,
            'supplier_id' => $supplier->id,
            'order_date' => now()->toDateString(),
            'status' => 'Approved',
            'unit_price' => 4.00,
            'total_amount' => 40.00,
            'final_amount' => 40.00,
        ]);

        $invoice = \App\Models\Invoice::create([
            'invoice_number' => 'INV-TEST-001',
            'purchase_order_id' => $purchaseOrder->id,
            'supplier_id' => $supplier->id,
            'invoice_date' => now()->toDateString(),
            'total_amount' => 40.00,
            'payment_due_date' => now()->addDays(30)->toDateString(),
            'status' => 'Active',
            'source' => 'manual',
        ]);

        $invoiceLine = \App\Models\InvoiceLine::create([
            'invoice_id' => $invoice->id,
            'item_id' => $item->id,
            'uom' => 'unit',
            'quantity' => 10,
            'invoice_line_total' => 40.00,
            'unit_price' => 4.00,
            'selling_price' => 5.50,
        ]);

        $response = $this->post('/owner/return-requests', [
            'invoice_id' => $invoice->id,
            'notes' => 'Damaged items returned',
            'items' => [
                [
                    'invoice_line_id' => $invoiceLine->id,
                    'quantity' => 3,
                    'reason' => 'damaged',
                ],
            ],
        ]);

        $response->assertRedirect(route('owner.return-requests.index'));
        $response->assertSessionHas('success');

        $item->refresh();
        $this->assertFalse($item->is_damaged);
        $this->assertEquals(0, $item->damaged_quantity);
        $this->assertEquals(10, $item->quantity);
    }

    public function test_owner_can_register_supplier_with_extended_profile_fields()
    {
        Mail::fake();

        $owner = User::factory()->create([
            'role' => 'owner',
            'email' => 'owner2@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->actingAs($owner);

        $response = $this->post('/owner/suppliers', [
            'name' => 'Extended Supplier',
            'contact_person' => 'Jane Supplier',
            'contact_email' => 'extended@example.com',
            'contact_phone' => '+60123456789',
            'address_line_1' => '123 Supply Street',
            'city' => 'Kuala Lumpur',
            'state' => 'WP Kuala Lumpur',
            'postal_code' => '50000',
            'country' => 'Malaysia',
            'portal_enabled' => true,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect(route('owner.suppliers.index'));

        $this->assertDatabaseHas('suppliers', [
            'contact_email' => 'extended@example.com',
            'contact_person' => 'Jane Supplier',
            'address_line_1' => '123 Supply Street',
            'city' => 'Kuala Lumpur',
            'state' => 'WP Kuala Lumpur',
            'postal_code' => '50000',
            'country' => 'Malaysia',
            'portal_enabled' => true,
        ]);

        $this->assertDatabaseHas('notifications', [
            'type' => 'supplier_invited',
            'user_id' => User::where('email', 'extended@example.com')->first()->id,
        ]);
    }

    public function test_owner_can_register_supplier_with_portal_access_and_generated_password()
    {
        Mail::fake();

        $owner = User::factory()->create([
            'role' => 'owner',
            'email' => 'owner4@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->actingAs($owner);

        $response = $this->post('/owner/suppliers', [
            'name' => 'Generated Password Supplier',
            'contact_email' => 'generated@example.com',
            'portal_enabled' => true,
        ]);

        $response->assertRedirect(route('owner.suppliers.index'));

        $supplier = Supplier::where('contact_email', 'generated@example.com')->first();
        $this->assertNotNull($supplier);
        $this->assertTrue($supplier->portal_enabled);
        $this->assertNotNull($supplier->user_id);
        $this->assertNotNull($supplier->temporary_password);
        $this->assertEquals(url('/login'), $supplier->portal_link);
        $this->assertEquals('sent', $supplier->invite_email_status);
        $this->assertEquals('missing', $supplier->invite_whatsapp_status);

        Mail::assertSent(\App\Mail\SupplierInviteMail::class);
    }

    public function test_owner_can_disable_supplier_portal_access_on_registration()
    {
        Mail::fake();

        $owner = User::factory()->create([
            'role' => 'owner',
            'email' => 'owner3@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->actingAs($owner);

        $response = $this->post('/owner/suppliers', [
            'name' => 'Disabled Portal Supplier',
            'contact_email' => 'disabled@example.com',
            'contact_phone' => '0123456790',
            'portal_enabled' => false,
        ]);

        $response->assertRedirect(route('owner.suppliers.index'));

        $supplier = Supplier::where('contact_email', 'disabled@example.com')->first();
        $this->assertNotNull($supplier);
        $this->assertFalse((bool) $supplier->portal_enabled);
        $this->assertNull($supplier->user_id);
        $this->assertNull(User::where('email', 'disabled@example.com')->first());
    }
}
