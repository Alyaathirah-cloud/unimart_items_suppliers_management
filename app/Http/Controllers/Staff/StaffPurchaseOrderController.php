<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StaffPurchaseOrderController extends Controller
{
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

        $orders = $request->has('view_all') ? $query->get() : $query->paginate(15);
        $suppliers = Supplier::orderBy('name')->get();

        return view('staff.purchase_orders.index', compact('orders', 'suppliers'));
    }

    public function create(Request $request)
    {
        $preselectedItem = null;
        if ($request->filled('item_id')) {
            $preselectedItem = Item::with('supplier')->find($request->item_id);
        }
        $suppliers = Supplier::orderBy('name')->get();
        $items = Item::with('supplier')->orderBy('name')->get();

        return view('staff.purchase_orders.create', compact('preselectedItem', 'suppliers', 'items'));
    }

    protected function normalizeItemsPayload(Request $request): void
    {
        $items = $request->input('items');
        if (is_array($items) && count($items) > 0) return;
        
        if ($request->filled('item_id') && $request->filled('quantity')) {
            $request->merge([
                'items' => [[
                    'item_id' => $request->input('item_id'),
                    'quantity' => (int) $request->input('quantity'),
                ]],
            ]);
        }
    }

    public function store(Request $request)
    {
        $this->normalizeItemsPayload($request);

        $request->validate([
            'supplier_id'      => 'required|exists:suppliers,id',
            'notes'            => 'nullable|string|max:1000',
            'items'            => 'required|array|min:1',
            'items.*.item_id'  => 'required|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $lines = [];
        $totalAmount = 0;

        foreach ($request->items as $row) {
            $item = Item::findOrFail($row['item_id']);
            $unitPrice = (float)($item->unit_price ?? 0);

            if ($unitPrice <= 0) {
                return back()->withErrors(['items' => "Item \"{$item->name}\" has no unit price set."])->withInput();
            }

            $subtotal = $unitPrice * (int)$row['quantity'];
            $totalAmount += $subtotal;

            $lines[] = [
                'item_id'    => $item->id,
                'quantity'   => (int)$row['quantity'],
                'unit_price' => $unitPrice,
                'subtotal'   => $subtotal,
            ];
        }

        $po = DB::transaction(function () use ($request, $lines, $totalAmount) {
            $lastPo = PurchaseOrder::orderByDesc('id')->first();
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
                'created_by'   => auth()->id(),
            ]);

            foreach ($lines as $line) {
                PurchaseOrderItem::create(array_merge(['purchase_order_id' => $po->id], $line));
            }
            
            // Legacy sync
            $po->update([
                'quantity' => array_sum(array_column($lines, 'quantity')),
                'item_id' => $lines[0]['item_id'] ?? null,
                'unit_price' => $lines[0]['unit_price'] ?? null,
            ]);

            return $po;
        });

        // Send Email Notification to Supplier
        try {
            (new \App\Services\EmailNotificationService)->sendPurchaseOrderCreatedToSupplier($po);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Supplier PO Created email failed', ['po' => $po->po_number, 'error' => $e->getMessage()]);
        }

        return redirect()->route('staff.po.index')->with('success', 'Purchase order created successfully.');
    }

    public function show($id)
    {
        $purchaseOrder = PurchaseOrder::with(['orderItems.item', 'supplier', 'delivery', 'returnRequests.creditNote', 'invoice.creditNotes'])->findOrFail($id);
        return view('staff.purchase_orders.show', compact('purchaseOrder'));
    }

    public function edit($id)
    {
        $purchaseOrder = PurchaseOrder::findOrFail($id);
        
        if ($purchaseOrder->status !== 'Pending') {
            return redirect()->route('staff.po.show', $purchaseOrder)->with('error', 'Only pending orders can be edited.');
        }

        $suppliers = Supplier::orderBy('name')->get();
        $items = Item::with('supplier')->orderBy('name')->get();
        $purchaseOrder->load('orderItems.item');

        return view('staff.purchase_orders.edit', compact('purchaseOrder', 'suppliers', 'items'));
    }

    public function update(Request $request, $id)
    {
        $purchaseOrder = PurchaseOrder::findOrFail($id);

        if ($purchaseOrder->status !== 'Pending') {
            return back()->with('error', 'Only pending orders can be updated.');
        }

        $this->normalizeItemsPayload($request);

        $request->validate([
            'supplier_id'      => 'required|exists:suppliers,id',
            'notes'            => 'nullable|string|max:1000',
            'items'            => 'required|array|min:1',
            'items.*.item_id'  => 'required|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $lines = [];
        $totalAmount = 0;

        foreach ($request->items as $row) {
            $item = Item::findOrFail($row['item_id']);
            $unitPrice = (float)($item->unit_price ?? 0);

            if ($unitPrice <= 0) {
                return back()->withErrors(['items' => "Item \"{$item->name}\" has no unit price set."])->withInput();
            }

            $subtotal = $unitPrice * (int)$row['quantity'];
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

            $purchaseOrder->orderItems()->delete();
            foreach ($lines as $line) {
                PurchaseOrderItem::create(array_merge(['purchase_order_id' => $purchaseOrder->id], $line));
            }

            $purchaseOrder->update([
                'quantity' => array_sum(array_column($lines, 'quantity')),
                'item_id' => $lines[0]['item_id'] ?? null,
                'unit_price' => $lines[0]['unit_price'] ?? null,
            ]);
        });

        // Send Email Notification to Supplier for PO Update
        try {
            (new \App\Services\EmailNotificationService)->sendPurchaseOrderUpdatedToSupplier($purchaseOrder);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Supplier PO Updated email failed', ['po' => $purchaseOrder->po_number, 'error' => $e->getMessage()]);
        }

        return redirect()->route('staff.po.show', $purchaseOrder)->with('success', 'Purchase order updated successfully.');
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
}
