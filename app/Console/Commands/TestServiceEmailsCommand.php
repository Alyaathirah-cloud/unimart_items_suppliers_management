<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\EmailNotificationService;

class TestServiceEmailsCommand extends Command
{
    protected $signature   = 'email:test-services {to?}';
    protected $description = 'Test EmailNotificationService methods';

    public function handle()
    {
        $to = $this->argument('to') ?? config('mail.from.address');
        $service = new EmailNotificationService();

        $this->info("Sending staff approval email to {$to}...");
        $staffResult = $service->sendStaffApproved($to, 'Test Staff Name');
        if ($staffResult['success']) {
            $this->info("✅ Staff approval email sent!");
        } else {
            $this->error("❌ Staff approval email failed: " . $staffResult['error']);
        }

        $this->info("\nSending supplier invitation email to {$to}...");
        $supplierData = [
            'contact_person' => 'Supplier Contact',
            'company_name'   => 'Test Supplier Co',
            'portal_url'     => 'http://127.0.0.1:8000/supplier/login',
            'email'          => $to,
            'password'       => 'TempPass123!',
            'owner_phone'    => '+60123456789',
            'telegram_token' => 'test-token-123'
        ];
        // Test PO and Return emails
        // Create mock PO
        $po = \App\Models\PurchaseOrder::first();
        if ($po) {
            $this->info("\nSending PO Created email to {$to}...");
            $po->supplier->contact_email = $to; // Mock email
            $poCreatedResult = $service->sendPurchaseOrderCreatedToSupplier($po);
            if ($poCreatedResult['success']) $this->info("✅ PO Created email sent!");
            else $this->error("❌ PO Created email failed: " . $poCreatedResult['error']);

            $this->info("\nSending PO Updated email to {$to}...");
            $poUpdatedResult = $service->sendPurchaseOrderUpdatedToSupplier($po);
            if ($poUpdatedResult['success']) $this->info("✅ PO Updated email sent!");
            else $this->error("❌ PO Updated email failed: " . $poUpdatedResult['error']);
        } else {
            $this->warn("No PurchaseOrder found in DB to test PO emails.");
        }

        // Create mock Return Request
        $rr = \App\Models\ReturnRequest::first();
        if ($rr) {
            $this->info("\nSending Return Request Created email to {$to}...");
            $rr->supplier->contact_email = $to; // Mock email
            $rrCreatedResult = $service->sendReturnRequestCreatedToSupplier($rr);
            if ($rrCreatedResult['success']) $this->info("✅ Return Request Created email sent!");
            else $this->error("❌ Return Request Created email failed: " . $rrCreatedResult['error']);
        } else {
            $this->warn("No ReturnRequest found in DB to test Return emails.");
        }
        
        return 0;
    }
}
