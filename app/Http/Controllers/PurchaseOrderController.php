<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Invoice;
use App\Models\Notification;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\InvoiceLine;
use App\Models\ReturnRequest;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;

class PurchaseOrderController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:owner']);
    }

    public function getSupplierCreditNotes(\App\Models\Supplier $supplier)
    {
        $creditNotes = \App\Models\CreditNote::with('returnRequest.item')
            ->where('supplier_id', $supplier->id)
            ->whereIn('status', ['Unused', 'Partially Used'])
            ->where('remaining_balance', '>', 0)
            ->get();

        $availableCredit = $creditNotes->sum('remaining_balance');

        return response()->json([
            'creditNotes'    => $creditNotes,
            'availableCredit'=> $availableCredit,
        ]);
    }

    /**
     * Return items that are low stock or out of stock for a given supplier.
     * Used by the PO create page to auto-suggest items.
     */
    public function getLowStockItemsBySupplier(Request $request, Supplier $supplier)
    {
        $items = Item::where('supplier_id', $supplier->id)
            ->where(function ($q) {
                $q->where('quantity', 0)
                  ->orWhereColumn('quantity', '<=', 'reorder_point');
            })
            ->orderByRaw('quantity ASC')
            ->get()
            ->map(fn($item) => [
                'id'             => $item->id,
                'name'           => $item->name,
                'quantity'       => $item->quantity,
                'reorder_point'  => $item->reorder_point,
                'unit_price'     => (float) $item->unit_price,
                'suggested_qty'  => max(1, $item->reorder_point * 2),
                'status'         => $item->quantity == 0 ? 'out_of_stock' : 'low_stock',
            ]);

        return response()->json(['items' => $items]);
    }

    protected function normalizeItemsPayload(Request $request): void
    {
        $items = $request->input('items');

        if (is_array($items) && count($items) > 0) {
            return;
        }

        if ($request->filled('item_id') && $request->filled('quantity')) {
            $request->merge([
                'items' => [[
                    'item_id' => $request->input('item_id'),
                    'quantity' => (int) $request->input('quantity'),
                ]],
            ]);
        }
    }

    protected function syncLegacyFields(PurchaseOrder $purchaseOrder, array $lines): void
    {
        $legacyQuantity = array_sum(array_column($lines, 'quantity'));
        $primaryLine = $lines[0] ?? null;

        $purchaseOrder->update([
            'quantity'   => $legacyQuantity,
            'item_id'    => $primaryLine['item_id'] ?? null,
            'unit_price' => $primaryLine['unit_price'] ?? null,
        ]);
    }

    public function index(Request $request)
    {
        $query = PurchaseOrder::with(['orderItems.item', 'supplier', 'delivery']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('po_number', 'like', "%{$search}%")
                  ->orWhereHas('supplier', fn($sq) => $sq->where('name', 'like', "%{$search}%"));
            });
        }

        $query->orderByRaw("CASE WHEN status = 'Pending' THEN 0 ELSE 1 END")
              ->orderBy('order_date', 'desc');

        $orders    = $request->has('view_all') ? $query->get() : $query->paginate(15);
        $suppliers = Supplier::orderBy('name')->get();

        return view('owner.purchase_orders.index', compact('orders', 'suppliers'));
    }

    public function create(Request $request)
    {
        $preselectedItem = null;
        if ($request->filled('item_id')) {
            $preselectedItem = Item::with('supplier')->find($request->item_id);
        }
        $suppliers = Supplier::orderBy('name')->get();
        $items     = Item::with('supplier')->orderBy('name')->get();

        return view('owner.purchase_orders.create', compact('preselectedItem', 'suppliers', 'items'));
    }

    public function store(Request $request)
    {
        $this->normalizeItemsPayload($request);

        $request->validate([
            'supplier_id'          => 'required|exists:suppliers,id',
            'notes'                => 'nullable|string|max:1000',
            'items'                => 'required|array|min:1',
            'items.*.item_id'      => 'required|exists:items,id',
            'items.*.quantity'     => 'required|integer|min:1',
        ]);

        $supplier = Supplier::findOrFail($request->supplier_id);

        $lines      = [];
        $totalAmount = 0;

        foreach ($request->items as $row) {
            $item      = Item::findOrFail($row['item_id']);
            $unitPrice = (float)($item->unit_price ?? 0);

            if ($unitPrice <= 0) {
                return back()->withErrors([
                    'items' => "Item \"{$item->name}\" has no unit price set. Please update the item record first.",
                ])->withInput();
            }

            $subtotal    = $unitPrice * (int)$row['quantity'];
            $totalAmount += $subtotal;

            $lines[] = [
                'item_id'    => $item->id,
                'quantity'   => (int)$row['quantity'],
                'unit_price' => $unitPrice,
                'subtotal'   => $subtotal,
            ];
        }

        $po = DB::transaction(function () use ($request, $supplier, $lines, $totalAmount) {
            // Generate PO number
            $lastPo    = PurchaseOrder::orderByDesc('id')->first();
            $nextIndex = 1;
            if ($lastPo && preg_match('/PO-(\d+)/', $lastPo->po_number, $m)) {
                $nextIndex = intval($m[1]) + 1;
            }
            $poNumber = 'PO-' . str_pad($nextIndex, 4, '0', STR_PAD_LEFT);
            while (PurchaseOrder::where('po_number', $poNumber)->exists()) {
                $nextIndex++;
                $poNumber = 'PO-' . str_pad($nextIndex, 4, '0', STR_PAD_LEFT);
            }

            $po = PurchaseOrder::create([
                'po_number'    => $poNumber,
                'supplier_id'  => $request->supplier_id,
                'order_date'   => now(),
                'status'       => 'Pending',
                'notes'        => $request->notes,
                'total_amount' => $totalAmount,
                'final_amount' => $totalAmount,
            ]);

            foreach ($lines as $line) {
                PurchaseOrderItem::create(array_merge(['purchase_order_id' => $po->id], $line));
            }

            $this->syncLegacyFields($po, $lines);

            return $po;
        });

        // Notify supplier
        if ($supplier->user) {
            Notification::send(
                $supplier->user,
                'purchase_order_created',
                'A new purchase order ' . $po->po_number . ' has been created. Please log in to review.',
                [
                    'po_number'  => $po->po_number,
                    'login_url'  => route('login'),
                ]
            );
        }

        // Send WhatsApp notification to supplier via CallMeBot or Twilio
        try {
            $callMeBotPhone = trim((string) Setting::get('callmebot_phone', config('services.callmebot.phone')));
            $callMeBotApiKey = trim((string) Setting::get('callmebot_api_key', config('services.callmebot.apikey')));
            if ($supplier->contact_phone) {
                if ($callMeBotPhone !== '' && $callMeBotApiKey !== '') {
                    $c = new \App\Services\CallMeBotService();
                    $c->sendPurchaseOrderNotification($po->po_number, optional($po->orderItems->first())->item->name ?? 'Item', optional($po->orderItems->first())->quantity ?? $po->quantity, $supplier->contact_phone, $supplier->portal_link ?? route('supplier.login'));
                } elseif (config('services.twilio.sid')) {
                    $w = new \App\Services\WhatsAppService();
                    $msg = "📦 NEW PURCHASE ORDER\n\nPO Number: {$po->po_number}\nItem: " . (optional($po->orderItems->first())->item->name ?? 'Item') . "\nQuantity: " . (optional($po->orderItems->first())->quantity ?? $po->quantity) . "\n\nPlease review and respond through the Supplier Portal.\n\nPortal:\n" . ($supplier->portal_link ?? route('supplier.login'));
                    $w->sendMessage($supplier->contact_phone, $msg);
                }
            }
        } catch (\Throwable $e) {
            Log::channel('whatsapp_alerts')->error('Failed to send purchase order whatsapp', ['po' => $po->po_number, 'error' => $e->getMessage()]);
        }

        return redirect()->route('owner.purchase-orders.index')
            ->with('success', 'Purchase order ' . $po->po_number . ' created successfully.');
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['orderItems.item', 'supplier', 'delivery', 'returnRequests.creditNote', 'invoice.creditNotes']);
        return view('owner.purchase_orders.show', compact('purchaseOrder'));
    }

    public function edit(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'Pending') {
            return redirect()->route('owner.purchase-orders.show', $purchaseOrder)
                ->with('error', 'Only pending orders can be edited.');
        }

        $suppliers = Supplier::orderBy('name')->get();
        $items     = Item::with('supplier')->orderBy('name')->get();
        $purchaseOrder->load('orderItems.item');

        return view('owner.purchase_orders.edit', compact('purchaseOrder', 'suppliers', 'items'));
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'Pending') {
            return back()->with('error', 'Only pending orders can be updated.');
        }

        $this->normalizeItemsPayload($request);

        $request->validate([
            'supplier_id'          => 'required|exists:suppliers,id',
            'notes'                => 'nullable|string|max:1000',
            'items'                => 'required|array|min:1',
            'items.*.item_id'      => 'required|exists:items,id',
            'items.*.quantity'     => 'required|integer|min:1',
        ]);

        $lines       = [];
        $totalAmount = 0;

        foreach ($request->items as $row) {
            $item      = Item::findOrFail($row['item_id']);
            $unitPrice = (float)($item->unit_price ?? 0);

            if ($unitPrice <= 0) {
                return back()->withErrors([
                    'items' => "Item \"{$item->name}\" has no unit price set.",
                ])->withInput();
            }

            $subtotal    = $unitPrice * (int)$row['quantity'];
            $totalAmount += $subtotal;
            $lines[] = [
                'item_id'    => $item->id,
                'quantity'   => (int)$row['quantity'],
                'unit_price' => $unitPrice,
                'subtotal'   => $subtotal,
            ];
        }

        DB::transaction(function () use ($purchaseOrder, $request, $lines, $totalAmount) {
            $purchaseOrder->update([
                'supplier_id'  => $request->supplier_id,
                'notes'        => $request->notes,
                'total_amount' => $totalAmount,
                'final_amount' => $totalAmount,
            ]);

            // Replace all existing lines
            $purchaseOrder->orderItems()->delete();
            foreach ($lines as $line) {
                PurchaseOrderItem::create(array_merge(['purchase_order_id' => $purchaseOrder->id], $line));
            }

            $this->syncLegacyFields($purchaseOrder, $lines);
        });

        return redirect()->route('owner.purchase-orders.show', $purchaseOrder)
            ->with('success', 'Purchase order updated successfully.');
    }

    public function generateInvoiceForPurchaseOrder(PurchaseOrder $purchaseOrder): Invoice
    {
        $existingInvoice = Invoice::where('purchase_order_id', $purchaseOrder->id)->first();
        if ($existingInvoice) {
            return $existingInvoice;
        }

        $purchaseOrder->load('orderItems.item');

        $invoiceNumber = 'INV-' . $purchaseOrder->po_number;

        $invoiceTotal = 0;

        foreach ($purchaseOrder->orderItems as $poItem) {
            $qty = $poItem->received_quantity ?? $poItem->quantity;
            $invoiceTotal += $qty * $poItem->unit_price;
        }
        if ($invoiceTotal <= 0 && $purchaseOrder->orderItems->isEmpty()) {
            $invoiceTotal = $purchaseOrder->total_amount ?? 0;
        }

        $today = now()->toDateString();
        $dueDate = now()->addDays(30)->toDateString();

        $invoice = Invoice::create([
            'invoice_number'    => $invoiceNumber,
            'purchase_order_id' => $purchaseOrder->id,
            'supplier_id'       => $purchaseOrder->supplier_id,
            'invoice_date'      => $today,
            'total_amount'      => $invoiceTotal,
            'payment_due_date'  => $dueDate,
            'status'            => 'Active',
            'source'            => 'auto',
        ]);

        if ($purchaseOrder->orderItems->isNotEmpty()) {
            foreach ($purchaseOrder->orderItems as $poItem) {
                $qty = $poItem->received_quantity ?? $poItem->quantity;
                InvoiceLine::create([
                    'invoice_id'         => $invoice->id,
                    'item_id'            => $poItem->item_id,
                    'uom'                => $poItem->item->uom ?? 'unit',
                    'quantity'           => $qty,
                    'unit_price'         => $poItem->unit_price,
                    'selling_price'      => $poItem->unit_price,
                    'invoice_line_total' => $qty * $poItem->unit_price,
                ]);
            }
        } elseif ($purchaseOrder->item_id) {
            InvoiceLine::create([
                'invoice_id'         => $invoice->id,
                'item_id'            => $purchaseOrder->item_id,
                'uom'                => $purchaseOrder->item->uom ?? 'unit',
                'quantity'           => $purchaseOrder->quantity,
                'unit_price'         => $purchaseOrder->unit_price ?? 0,
                'selling_price'      => $purchaseOrder->unit_price ?? 0,
                'invoice_line_total' => $purchaseOrder->total_amount ?? 0,
            ]);
        }

        return $invoice;
    }

    public function markAsReceived(Request $request, PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'Approved') {
            return back()->with('error', 'Only Approved purchase orders can be marked as received.');
        }
        if (!$purchaseOrder->delivery || !$purchaseOrder->delivery->delivery_date) {
            return back()->with('error', 'Cannot mark as received: no delivery date has been set by the supplier.');
        }

        $request->validate([
            'items'                     => 'required|array',
            'items.*.received_quantity' => 'required|integer|min:0',
            'items.*.damaged_quantity'  => 'required|integer|min:0',
            'items.*.expiry_date'       => 'nullable|date',
        ]);

        DB::transaction(function () use ($purchaseOrder, $request) {
            // 1 — Update PO status
            $purchaseOrder->status = 'Received';
            $purchaseOrder->save();

            // 2 — Increment inventory for each PO line item based on checklist
            $purchaseOrder->load('orderItems.item');
            foreach ($purchaseOrder->orderItems as $poItem) {
                $input = $request->input("items.{$poItem->id}");
                if (!$input) continue;

                $receivedQty = (int) ($input['received_quantity'] ?? 0);
                $damagedQty  = (int) ($input['damaged_quantity'] ?? 0);
                if ($damagedQty > $receivedQty) {
                    $damagedQty = $receivedQty;
                }
                $goodQty = max(0, $receivedQty - $damagedQty);
                $expiryDate = $input['expiry_date'] ?? null;

                $poItem->update([
                    'received_quantity' => $receivedQty,
                    'damaged_quantity'  => $damagedQty,
                    'good_quantity'     => $goodQty,
                    'expiry_date'       => $expiryDate,
                ]);

                if ($poItem->item) {
                    $poItem->item->increment('quantity', $goodQty);
                    
                    if ($damagedQty > 0) {
                        $poItem->item->increment('damaged_quantity', $damagedQty);
                        $poItem->item->is_damaged = true;
                        $poItem->item->save();
                    }

                    if ($expiryDate) {
                        $poItem->item->expiry_date = $expiryDate;
                        $poItem->item->save();
                    }
                }
            }

            // Fallback: if no orderItems exist, use legacy single-item fields
            if ($purchaseOrder->orderItems->isEmpty() && $purchaseOrder->item_id) {
                $singleItem = Item::find($purchaseOrder->item_id);
                if ($singleItem) {
                    $singleItem->increment('quantity', $purchaseOrder->quantity ?? 0);
                }
            }

            // 3 — Removed: Invoice generation is now handled manually by the supplier.

            // 4 — Notify supplier
            $supplier = $purchaseOrder->supplier;
            if ($supplier && $supplier->user) {
                Notification::send(
                    $supplier->user,
                    'po_received',
                    'Purchase Order #' . $purchaseOrder->po_number . ' has been marked as Received.',
                    ['po_number' => $purchaseOrder->po_number]
                );
            }
        });

        return redirect()
            ->route('owner.purchase-orders.show', $purchaseOrder)
            ->with('success', 'Delivery received. Inventory updated and invoice generated.')
            ->with('item_update_reminder', true);
    }

    public function generateForLowStock()
    {
        $items = Item::whereColumn('quantity', '<=', 'reorder_point')->get();
        foreach ($items as $item) {
            $po = PurchaseOrder::firstOrCreate(
                ['supplier_id' => $item->supplier_id, 'status' => 'Pending'],
                [
                    'po_number'    => 'PO' . now()->format('Ymd') . str_pad($item->id, 4, '0', STR_PAD_LEFT),
                    'order_date'   => now(),
                    'total_amount' => 0,
                    'final_amount' => 0,
                ]
            );

            if ($po->wasRecentlyCreated) {
                $unitPrice = (float)($item->unit_price ?? 0);
                $qty       = max(1, $item->reorder_point * 2);
                PurchaseOrderItem::create([
                    'purchase_order_id' => $po->id,
                    'item_id'           => $item->id,
                    'quantity'          => $qty,
                    'unit_price'        => $unitPrice,
                    'subtotal'          => $unitPrice * $qty,
                ]);
                $po->update(['total_amount' => $unitPrice * $qty, 'final_amount' => $unitPrice * $qty]);

                $supplierUser = optional($item->supplier)->user;
                if ($supplierUser) {
                    Notification::send($supplierUser, 'purchase_order_created',
                        'A new purchase order has been created.',
                        ['po_number' => $po->po_number]);
                }
            }
        }
    }

    public function exportCsv(Request $request)
    {
        $query = PurchaseOrder::with(['orderItems.item', 'supplier', 'delivery']);

        if ($request->filled('status'))      $query->where('status', $request->status);
        if ($request->filled('supplier_id')) $query->where('supplier_id', $request->supplier_id);

        $orders   = $query->orderBy('order_date', 'desc')->get();
        $filename = 'purchase_orders_' . now()->format('Ymd_His') . '.csv';
        $headers  = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        return response()->stream(function () use ($orders) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['PO Number', 'Supplier', 'Total (RM)', 'Order Date', 'Delivery Date', 'Status', 'Items']);
            foreach ($orders as $order) {
                $itemNames = $order->orderItems->map(fn($i) => optional($i->item)->name . ' x' . $i->quantity)->implode(', ');
                fputcsv($handle, [
                    $order->po_number,
                    optional($order->supplier)->name ?? 'N/A',
                    number_format($order->total_amount ?? 0, 2),
                    $order->order_date?->format('Y-m-d') ?? '',
                    optional($order->delivery)->delivery_date?->format('Y-m-d') ?? '',
                    $order->status,
                    $itemNames,
                ]);
            }
            fclose($handle);
        }, 200, $headers);
    }

    public function invoiceStatus(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load('invoice');
        if ($purchaseOrder->invoice) {
            return response()->json([
                'status'         => 'ready',
                'invoice_number' => $purchaseOrder->invoice->invoice_number,
                'invoice_date'   => $purchaseOrder->invoice->invoice_date?->format('M d, Y') ?? 'N/A',
                'export_url'     => route('owner.invoices.export-pdf', $purchaseOrder->invoice),
            ]);
        }
        return response()->json(['status' => 'pending']);
    }
}
