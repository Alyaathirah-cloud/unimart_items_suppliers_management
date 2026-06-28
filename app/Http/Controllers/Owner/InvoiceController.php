<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceLine;
use App\Models\Item;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:owner']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Invoice::with(['supplier', 'purchaseOrder', 'lines.item']);

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $invoices = $query->orderBy('invoice_date', 'desc')->paginate(15);
        $suppliers = \App\Models\Supplier::orderBy('name')->get();

        return view('owner.invoices.index', compact('invoices', 'suppliers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $purchaseOrder = null;
        if ($request->has('po_id')) {
            $purchaseOrder = PurchaseOrder::with('item.supplier')->find($request->po_id);
        }

        $items = Item::with('supplier')->whereHas('supplier')->orderBy('name')->get();
        return view('owner.invoices.create', compact('purchaseOrder', 'items'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'invoice_number' => 'required|string|max:255|unique:invoices',
            'supplier_id' => 'required|exists:suppliers,id',
            'invoice_date' => 'required|date',
            'payment_due_date' => 'nullable|date|after:invoice_date',
            'lines.*.item_id' => 'required|exists:items,id',
            'lines.*.quantity' => 'required|integer|min:1',
            'lines.*.uom' => 'nullable|string|max:50',
            'lines.*.invoice_line_total' => 'required|numeric|min:0.01',
        ]);

        $invoice = Invoice::create([
            'invoice_number' => $request->invoice_number,
            'supplier_id' => $request->supplier_id,
            'invoice_date' => $request->invoice_date,
            'payment_due_date' => $request->payment_due_date ?? now()->addDays(30)->toDateString(),
            'status' => 'Unpaid',
            'source' => 'manual',
            'total_amount' => 0,
        ]);

        $totalAmount = 0;

        if ($request->has('lines')) {
            foreach ($request->lines as $lineData) {
                if (!isset($lineData['item_id'])) continue;

                $item = Item::find($lineData['item_id']);
                $quantity = (int)$lineData['quantity'];
                $invoiceLineTotal = (float)$lineData['invoice_line_total'];
                $uom = $lineData['uom'] ?? 'unit';

                // Calculate unit_price: line_total ÷ pieces_per_uom
                $piecesPerUom = $item->pieces_per_uom ?? 1;
                $unitPrice = $invoiceLineTotal / max(1, $quantity * $piecesPerUom);

                // Calculate selling_price: unit_price × (1 + markup_percentage/100)
                $markupPercentage = $item->markup_percentage ?? 20;
                $sellingPrice = $unitPrice * (1 + ($markupPercentage / 100));

                $invoiceLine = InvoiceLine::create([
                    'invoice_id' => $invoice->id,
                    'item_id' => $item->id,
                    'quantity' => $quantity,
                    'uom' => $uom,
                    'invoice_line_total' => $invoiceLineTotal,
                    'unit_price' => $unitPrice,
                    'selling_price' => $sellingPrice,
                ]);

                // Update item unit_price and selling_price
                $item->unit_price = $unitPrice;
                $item->selling_price = $sellingPrice;
                $item->save();

                $totalAmount += $invoiceLineTotal;
            }
        }

        $invoice->total_amount = $totalAmount;
        $invoice->save();

        return redirect()->route('owner.invoices.show', $invoice)
            ->with('success', 'Invoice recorded successfully. Item prices updated.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice)
    {
        $invoice->load([
            'lines.item',
            'supplier',
            'purchaseOrder.item',
            'purchaseOrder.orderItems.item',
            'returnRequests.lines.item',
            'creditNotes.returnRequest.lines.item',
        ]);
        return view('owner.invoices.show', compact('invoice'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice)
    {
        // Only manual invoices can be deleted, and not closed ones
        if ($invoice->source !== 'manual') {
            return back()->with('error', 'Cannot delete auto-generated invoices.');
        }
        if ($invoice->isLocked()) {
            return back()->with('error', 'Cannot delete a closed invoice.');
        }

        $invoice->lines()->delete();
        $invoice->delete();

        return redirect()->route('owner.invoices.index')
            ->with('success', 'Invoice deleted successfully.');
    }

    public function exportPdf(Invoice $invoice)
    {
        $invoice->load(['lines.item', 'supplier', 'purchaseOrder.item']);

        $filename = 'invoice-' . $invoice->invoice_number . '.pdf';

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('owner.invoices.pdf', compact('invoice'))
            ->setPaper('a4', 'portrait');

        return $pdf->download($filename);
    }

    public function markPaid(Invoice $invoice)
    {
        $invoice->update(['status' => 'Paid']);
        return redirect()->route('owner.invoices.show', $invoice)
            ->with('success', 'Invoice marked as Paid. It is now locked.');
    }

    /**
     * Save the owner's Est. Sold overrides for the Stock Performance panel.
     * Accepts a JSON map of {item_id: est_sold_qty} via the estimates[] input.
     */
    public function saveEstimates(Request $request, Invoice $invoice)
    {
        $request->validate([
            'estimates'   => 'required|array',
            'estimates.*' => 'integer|min:0',
        ]);

        $po        = $invoice->purchaseOrder;
        $estimates = $request->estimates;

        if ($po) {
            // Build a delivered-qty map keyed by item_id for capping
            $deliveredMap = [];

            // Multi-item PO (new style): use orderItems relationship
            if ($po->orderItems()->exists()) {
                foreach ($po->orderItems as $poItem) {
                    $deliveredMap[$poItem->item_id] = (int) $poItem->quantity;
                }
            } elseif ($po->item_id) {
                // Legacy single-item PO
                $deliveredMap[$po->item_id] = (int) $po->quantity;
            }

            // Cap each estimate at the delivered quantity for that item
            foreach ($estimates as $itemId => $soldQty) {
                $cap = $deliveredMap[$itemId] ?? PHP_INT_MAX;
                $estimates[$itemId] = min((int) $soldQty, $cap);
            }
        }

        $invoice->update(['sold_estimates' => $estimates]);

        return back()->with('success', 'Stock estimates saved successfully.');
    }
}
