<?php

namespace App\Http\Controllers;

use App\Models\ReturnRequest;
use App\Models\CreditNote;
use App\Models\Supplier;
use App\Models\PurchaseOrder;
use App\Models\Invoice;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;

class ReturnRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index(Request $request)
    {
        $user = auth()->user();

        if ($user->isSupplier()) {
            // Suppliers have their own view handled by SupplierController
            return redirect()->route('supplier.dashboard');
        }

        $query = ReturnRequest::with(['supplier', 'creditNote', 'lines.item']);

        if ($user->isOwner()) {
            // Owner can see all
        } else {
            abort(403);
        }

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }
        if ($request->filled('reason')) {
            // reason filter now looks at line-level reasons via a join
            $query->whereHas('lines', function ($q) use ($request) {
                $q->where('reason', strtolower($request->reason));
            });
        }

        // Sorting: Pending first, then by created_at desc
        $query->orderByRaw("CASE WHEN status = 'Pending' THEN 0 ELSE 1 END")
              ->orderBy('created_at', 'desc');

        $requests = $request->has('view_all') ? $query->get() : $query->paginate(15);

        $suppliers = $user->isOwner() ? \App\Models\Supplier::orderBy('name')->get() : collect();

        return view('owner.return_requests.index', compact('requests', 'suppliers'));
    }

    public function create(Request $request)
    {
        $preselectedItem  = null;
        $preselectedInvoice = null;
        $preselectedQuantity = (int) $request->input('quantity', 0);

        // If coming from expired inventory, load the item context
        if ($request->filled('item_id')) {
            $preselectedItem = \App\Models\Item::with('supplier')->find($request->item_id);
            if ($preselectedItem && $preselectedItem->isDamaged()) {
                $requestedQuantity = $preselectedQuantity > 0 ? $preselectedQuantity : (int) $preselectedItem->damaged_quantity;
                $preselectedQuantity = min($requestedQuantity, (int) $preselectedItem->damaged_quantity);
            }
        }

        // Build invoice query - filter by item's supplier when pre-selected
        $invoiceQuery = Invoice::with(['supplier', 'lines.item', 'purchaseOrder.item'])
            ->orderBy('invoice_date', 'desc');

        if ($request->filled('invoice_id')) {
            $invoiceQuery->where('id', $request->invoice_id);
        }

        if ($preselectedItem && $preselectedItem->supplier_id) {
            $invoiceQuery->where('supplier_id', $preselectedItem->supplier_id);
        }

        $invoices = $invoiceQuery->limit(100)->get();

        // For each invoice, if it has no lines, synthesize a virtual line from the PO
        $invoices->each(function ($invoice) {
            if ($invoice->lines->isEmpty() && $invoice->purchaseOrder) {
                $po   = $invoice->purchaseOrder;
                $item = $po->item;
                if ($po && $item) {
                    // Build a synthetic line object that mirrors InvoiceLine structure
                    $syntheticLine = new \stdClass();
                    $syntheticLine->id                = 'po_' . $po->id; // virtual ID
                    $syntheticLine->invoice_id        = $invoice->id;
                    $syntheticLine->item_id           = $item->id;
                    $syntheticLine->item              = $item;
                    $syntheticLine->quantity          = $po->quantity;
                    $syntheticLine->unit_price        = $po->unit_price ?? $item->unit_price;
                    $syntheticLine->uom               = $item->uom ?? 'unit';
                    $syntheticLine->is_synthetic      = true;
                    $invoice->setRelation('lines', collect([$syntheticLine]));
                }
            }
        });

        if ($request->filled('invoice_id')) {
            $preselectedInvoice = $invoices->firstWhere('id', $request->invoice_id);
        }

        // If a specific item was pre-selected, try to pre-select the most recent matching invoice
        if ($preselectedItem) {
            $preselectedInvoice = $invoices->first(function ($inv) use ($preselectedItem) {
                return $inv->lines->contains(function ($line) use ($preselectedItem) {
                    return $line->item_id == $preselectedItem->id;
                });
            });
        }

        return view('owner.return_requests.create', compact(
            'invoices',
            'preselectedItem',
            'preselectedInvoice',
            'preselectedQuantity'
        ));
    }

    public function show(ReturnRequest $returnRequest)
    {
        $returnRequest->load(['lines.item', 'supplier', 'purchaseOrder.item.supplier', 'creditNote.items.item', 'invoice']);
        return view('owner.return_requests.show', compact('returnRequest'));
    }

    protected function normalizeReturnRequestPayload(Request $request): void
    {
        if (! $request->filled('invoice_id') && $request->filled('po_id')) {
            $invoice = Invoice::where('purchase_order_id', $request->input('po_id'))->latest()->first();
            if ($invoice) {
                $request->merge(['invoice_id' => $invoice->id]);
            }
        }

        $items = $request->input('items');
        if (is_array($items) && count($items) > 0) {
            return;
        }

        if (! $request->filled('item_id') || ! $request->filled('quantity')) {
            return;
        }

        $invoice = Invoice::with('lines.item', 'purchaseOrder.item')->find($request->input('invoice_id'));
        if (! $invoice) {
            return;
        }

        $invoiceLine = $invoice->lines->firstWhere('item_id', $request->input('item_id'));
        if (! $invoiceLine && $invoice->purchaseOrder) {
            $invoiceLine = (object) [
                'id'         => 'po_' . $invoice->purchase_order_id,
                'item_id'    => $invoice->purchaseOrder->item_id,
                'item'       => $invoice->purchaseOrder->item,
                'quantity'   => $invoice->purchaseOrder->quantity,
                'unit_price' => $invoice->purchaseOrder->unit_price ?? optional($invoice->purchaseOrder->item)->unit_price,
                'uom'        => optional($invoice->purchaseOrder->item)->uom ?? 'unit',
            ];
        }

        if (! $invoiceLine) {
            return;
        }

        $request->merge([
            'items' => [[
                'invoice_line_id' => $invoiceLine->id,
                'quantity'        => (int) $request->input('quantity'),
                'reason'          => strtolower((string) $request->input('reason')),
            ]],
        ]);
    }

    public function store(Request $request)
    {
        $this->normalizeReturnRequestPayload($request);

        $request->validate([
            'invoice_id'              => 'required|exists:invoices,id',
            'notes'                   => 'nullable|string',
            'items'                   => 'required|array|min:1',
            'items.*.invoice_line_id' => 'required',
            'items.*.quantity'        => 'required|integer|min:0',
            'items.*.reason'          => 'nullable|in:expired,damaged',
        ]);

        $invoice  = Invoice::with(['supplier', 'lines.item', 'purchaseOrder.item'])->findOrFail($request->invoice_id);
        $supplier = $invoice->supplier;

        if (! $supplier) {
            return back()->withErrors(['invoice_id' => 'Invoice has no associated supplier.'])->withInput();
        }

        // Build a unified line map supporting both real invoice_lines and synthetic PO lines
        $realLineMap = $invoice->lines->keyBy('id');

        // Build synthetic line map from PO if lines are empty
        $syntheticLineMap = collect();
        if ($invoice->lines->isEmpty() && $invoice->purchaseOrder) {
            $po   = $invoice->purchaseOrder;
            $item = $po->item;
            if ($po && $item) {
                $key = 'po_' . $po->id;
                $syntheticLineMap[$key] = (object) [
                    'id'         => $key,
                    'item_id'    => $item->id,
                    'item'       => $item,
                    'quantity'   => $po->quantity,
                    'unit_price' => $po->unit_price ?? $item->unit_price,
                    'uom'        => $item->uom ?? 'unit',
                ];
            }
        }

        $hasReturn = false;

        foreach ($request->items as $itemData) {
            $lineId   = $itemData['invoice_line_id'];
            $quantity = (int) ($itemData['quantity'] ?? 0);
            if ($quantity <= 0) continue;

            // Try real line first, then synthetic
            $invoiceLine = $realLineMap[$lineId] ?? $syntheticLineMap[$lineId] ?? null;

            if (! $invoiceLine) {
                return back()->withErrors(['items' => 'Selected item does not belong to the chosen invoice.'])->withInput();
            }
            $item = $invoiceLine->item ?? \App\Models\Item::find($invoiceLine->item_id);
            if (! $item) {
                return back()->withErrors(['items' => 'Selected return item no longer exists in inventory.'])->withInput();
            }

            $returnableQty = min((int) $invoiceLine->quantity, (int) $item->quantity);
            if ($item->isDamaged()) {
                $returnableQty = min($returnableQty, (int) $item->damaged_quantity);
            }

            if ($quantity > $returnableQty) {
                $itemName = optional($invoiceLine->item)->name ?? 'item';
                return back()->withErrors(['items' => "Return quantity for {$itemName} cannot exceed remaining eligible inventory ({$returnableQty})."])->withInput();
            }
            if ($quantity > 0 && empty($itemData['reason'])) {
                $itemName = optional($invoiceLine->item)->name ?? 'item';
                return back()->withErrors(['items' => "Please select a return reason for {$itemName}."])->withInput();
            }
            $reason = strtolower($itemData['reason'] ?? '');
            if ($quantity > 0 && $item->isDamaged() && $reason !== 'damaged') {
                return back()->withErrors(['items' => "{$item->name} is flagged as damaged and must be returned as damaged stock."])->withInput();
            }
            if ($quantity > 0 && $reason === 'expired' && ! $item->isExpired()) {
                return back()->withErrors(['items' => "{$item->name} is not expired and cannot be returned as expired stock."])->withInput();
            }
            $hasReturn = true;
        }

        if (! $hasReturn) {
            return back()->withErrors(['items' => 'Please return at least one item quantity greater than zero.'])->withInput();
        }

        $lastRequest = ReturnRequest::orderByDesc('id')->first();
        $nextIndex   = 1;
        if ($lastRequest && preg_match('/RR-(\d+)/', $lastRequest->return_number, $matches)) {
            $nextIndex = intval($matches[1]) + 1;
        }
        $returnNumber = 'RR-' . str_pad($nextIndex, 4, '0', STR_PAD_LEFT);

        $returnRequest = ReturnRequest::create([
            'return_number'     => $returnNumber,
            'invoice_id'        => $invoice->id,
            'invoice_number'    => $invoice->invoice_number,
            'supplier_id'       => $supplier->id,
            'purchase_order_id' => $invoice->purchase_order_id,
            'notes'             => $request->notes,
            'status'            => 'Draft',
            'request_date'      => now()->toDateString(),
            'created_by'        => auth()->id(),
        ]);

        $returnedQuantitiesByItem = [];

        foreach ($request->items as $itemData) {
            $lineId   = $itemData['invoice_line_id'];
            $quantity = (int) ($itemData['quantity'] ?? 0);
            if ($quantity <= 0) continue;

            $invoiceLine = $realLineMap[$lineId] ?? $syntheticLineMap[$lineId];
            $unitPrice   = (float) $invoiceLine->unit_price;
            $subtotal    = $quantity * $unitPrice;

            \App\Models\ReturnRequestLine::create([
                'return_request_id' => $returnRequest->id,
                'invoice_line_id'   => is_numeric($lineId) ? intval($lineId) : null,
                'item_id'           => $invoiceLine->item_id,
                'quantity'          => $quantity,
                'uom'               => $invoiceLine->uom ?? 'unit',
                'reason'            => strtolower($itemData['reason']),
                'unit_price'        => $unitPrice,
                'subtotal'          => $subtotal,
            ]);

            $returnedQuantitiesByItem[$invoiceLine->item_id] = ($returnedQuantitiesByItem[$invoiceLine->item_id] ?? 0) + $quantity;
        }

        foreach ($returnedQuantitiesByItem as $itemId => $returnedQuantity) {
            $item = \App\Models\Item::find($itemId);
            if (! $item || ! $item->isDamaged()) {
                continue;
            }

            $remainingDamagedQuantity = max(0, (int) $item->damaged_quantity - $returnedQuantity);
            $item->update([
                'damaged_quantity' => $remainingDamagedQuantity,
                'is_damaged'       => $remainingDamagedQuantity > 0,
                'damage_reason'    => $remainingDamagedQuantity > 0 ? $item->damage_reason : null,
            ]);
        }

        return redirect()->route('owner.return-requests.show', $returnRequest)->with('success', 'Return request drafted successfully. Please review and submit.');
    }

    public function submit(ReturnRequest $returnRequest)
    {
        if ($returnRequest->status !== 'Draft') {
            return back()->with('error', 'Only draft return requests can be submitted.');
        }

        $returnRequest->update([
            'status' => 'Pending',
            'submitted_at' => now(),
        ]);

        $supplier = $returnRequest->supplier;

        if ($supplier && $supplier->user) {
            Notification::send(
                $supplier->user,
                'return_request_created',
                'A return request has been submitted. Please log in to review.',
                [
                    'return_number'  => $returnRequest->return_number,
                    'invoice_number' => $returnRequest->invoice_number,
                    'reason'         => $returnRequest->lines->pluck('reason')->filter()->unique()->join(', '),
                ]
            );

            // Send WhatsApp to supplier (CallMeBot preferred)
            try {
                $callMeBotPhone = trim((string) Setting::get('callmebot_phone', config('services.callmebot.phone')));
                $callMeBotApiKey = trim((string) Setting::get('callmebot_api_key', config('services.callmebot.apikey')));

                $portalLink = $supplier->portal_link ?? route('supplier.login');
                $firstLine = $returnRequest->lines->first();
                $data = [
                    'item_name' => optional($firstLine?->item)->name ?? '',
                    'quantity'  => $returnRequest->lines->sum('quantity'),
                    'reason'    => $returnRequest->lines->pluck('reason')->filter()->unique()->join(', '),
                ];

                if ($callMeBotPhone !== '' && $callMeBotApiKey !== '') {
                    $cb = new \App\Services\CallMeBotService();
                    $cb->sendReturnRequestNotificationToSupplier($data, $supplier->contact_phone, $portalLink);
                } elseif ($supplier->contact_phone && config('services.twilio.sid')) {
                    $w = new \App\Services\WhatsAppService();
                    $msg = "↩ RETURN REQUEST CREATED\n\nItem: " . $data['item_name'] . "\nQuantity: " . $data['quantity'] . "\nReason: " . $data['reason'] . "\n\nPlease review through the Supplier Portal.\n\nPortal:\n" . $portalLink;
                    $w->sendMessage($supplier->contact_phone, $msg);
                }
            } catch (\Throwable $e) {
                Log::channel('whatsapp_alerts')->error('Failed to send return request whatsapp', ['return' => $returnRequest->return_number, 'error' => $e->getMessage()]);
            }
        }

        return redirect()->route('owner.return-requests.show', $returnRequest)->with('success', 'Return request submitted successfully.');
    }

    public function destroy(ReturnRequest $returnRequest)
    {
        if ($returnRequest->status !== 'Draft') {
            return back()->with('error', 'Only draft return requests can be deleted.');
        }

        $returnRequest->lines()->delete();
        $returnRequest->delete();

        return redirect()->route('owner.return-requests.index')->with('success', 'Draft return request deleted.');
    }

    public function updateStatus(Request $request, ReturnRequest $returnRequest)
    {
        $user = auth()->user();
        if ($user->isSupplier() && $returnRequest->supplier_id == $user->supplier->id) {
            $request->validate([
                'status' => 'in:Approved,Rejected',
                'reason' => 'nullable|string|max:500',
            ]);
            $oldStatus = $returnRequest->status;
            $returnRequest->update(['status' => $request->status]);

            // Create credit note if approved
            if ($request->status === 'Approved' && $oldStatus !== 'Approved') {
                $poNumber = $returnRequest->purchaseOrder ? $returnRequest->purchaseOrder->po_number : 'N/A';
                $creditNoteId = 'CN-' . $poNumber . '-' . str_pad($returnRequest->id, 3, '0', STR_PAD_LEFT);
                $approvedAmount = $returnRequest->getTotal();

                CreditNote::create([
                    'credit_note_id' => $creditNoteId,
                    'return_id' => $returnRequest->id,
                    'supplier_id' => $returnRequest->supplier_id,
                    'invoice_id' => $returnRequest->invoice_id,
                    'purchase_order_id' => $returnRequest->purchase_order_id,
                    'amount' => $approvedAmount,
                    'remaining_balance' => $approvedAmount,
                    'issue_date' => now()->toDateString(),
                    'status' => 'Unused',
                    'reason' => $request->reason,
                ]);
            }

            // Notify owner
            $owner = \App\Models\User::where('role', 'owner')->first();
            if ($owner) {
                Notification::send(
                    $owner,
                    'return_request_' . strtolower($request->status),
                    "Return request {$returnRequest->return_number} has been " . strtolower($request->status),
                    ['rr_id' => $returnRequest->id]
                );
            }

            return back();
        }

        abort(403);
    }

    public function exportPdf(Request $request)
    {
        $query = ReturnRequest::with(['supplier', 'creditNote', 'lines.item']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }
        if ($request->filled('reason')) {
            $query->where('reason', $request->reason);
        }

        $requests = $query->orderBy('created_at', 'desc')->get();

        $filename = 'return-requests-' . now()->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($requests) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Return Number', 'Supplier', 'Reason', 'Status', 'Invoice Number', 'Credit Note ID', 'Request Date', 'Notes']);
            foreach ($requests as $rr) {
                fputcsv($handle, [
                    $rr->return_number,
                    optional($rr->supplier)->name ?? '',
                    $rr->reason,
                    $rr->status,
                    $rr->invoice_number ?? '',
                    optional($rr->creditNote)->credit_note_id ?? '',
                    $rr->request_date ? $rr->request_date->format('Y-m-d') : '',
                    $rr->notes ?? '',
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function getCreditNote(ReturnRequest $returnRequest)
    {
        if (!$returnRequest->creditNote || !in_array($returnRequest->status, ['Approved', 'Credit Applied'])) {
            return response()->json(['success' => false, 'message' => 'Credit note not available.']);
        }

        $creditNote = $returnRequest->creditNote->load(['supplier', 'returnRequest.lines.item', 'purchaseOrder']);

        $html = view('owner.credit_notes.single_pdf', compact('creditNote'))->render();

        return response()->json([
            'success' => true,
            'html' => $html,
            'pdf_url' => route('owner.credit-notes.export-single-pdf', $creditNote)
        ]);
    }
}
