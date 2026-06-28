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

        $query = ReturnRequest::with(['supplier', 'creditNote', 'lines.item', 'createdBy']);

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
        // Auto-detect expired items
        $expiredItems = \App\Models\Item::where('expiry_date', '<', now())
            ->where('quantity', '>', 0)
            ->with('supplier')
            ->get();

        // Auto-detect damaged items
        $damagedItems = \App\Models\Item::where('is_damaged', true)
            ->where('damaged_quantity', '>', 0)
            ->with('supplier')
            ->get();

        // Merge and de-duplicate by item ID
        $flaggedItems = $expiredItems->merge($damagedItems)->unique('id');

        $pendingItemIds = \App\Models\ReturnRequestLine::whereHas('returnRequest', function($q) {
            $q->where('status', 'Pending');
        })->pluck('item_id')->toArray();

        $flaggedItems = $flaggedItems->reject(function($item) use ($pendingItemIds) {
            return in_array($item->id, $pendingItemIds);
        });

        $suggestedItems = collect();

        foreach ($flaggedItems as $item) {
            $invoiceLine = \App\Models\InvoiceLine::where('item_id', $item->id)
                ->whereHas('invoice', function ($q) {
                    $q->whereNotIn('status', ['Paid', 'paid']);
                })
                ->with(['invoice.supplier'])
                ->latest()
                ->first();

            if (!$invoiceLine) {
                continue;
            }

            $invoice  = $invoiceLine->invoice;
            $supplier = $invoice->supplier;
            $isExpired = $item->isExpired();
            $isDamaged = $item->isDamaged();

            $maxReturnableFromInvoice = (int)$invoiceLine->quantity;

            $addSuggestion = function ($reason, $returnableQty) use ($item, $invoiceLine, $invoice, $supplier) {
                return [
                    'item_id'          => $item->id,
                    'item_name'        => $item->name,
                    'invoice_line_id'  => $invoiceLine->id,
                    'invoice_id'       => $invoice->id,
                    'invoice_number'   => $invoice->invoice_number,
                    'supplier_id'      => $supplier?->id,
                    'supplier_name'    => $supplier?->name ?? 'Unknown Supplier',
                    'accepts_returns'  => $supplier?->accepts_returns ?? true,
                    'quantity'         => $item->quantity,
                    'returnable_qty'   => $returnableQty,
                    'unit_price'       => (float)$invoiceLine->unit_price,
                    'uom'              => $invoiceLine->uom ?? 'unit',
                    'expiry_date'      => $item->expiry_date?->format('d M Y'),
                    'is_expired'       => $reason === 'expired',
                    'is_damaged'       => $reason === 'damaged',
                    'damage_reason'    => $item->damage_reason ?? '',
                    'suggested_reason' => $reason,
                    'suggested'        => true,
                ];
            };

            if ($isDamaged && $isExpired) {
                $damagedQty = (int)$item->damaged_quantity;
                $expiredQty = max(0, (int)$item->quantity - $damagedQty);

                $returnableDamaged = min($maxReturnableFromInvoice, $damagedQty);
                $remainingInvoiceQty = max(0, $maxReturnableFromInvoice - $returnableDamaged);
                $returnableExpired = min($remainingInvoiceQty, $expiredQty);

                if ($returnableDamaged > 0) {
                    $suggestedItems->push($addSuggestion('damaged', $returnableDamaged));
                }
                if ($returnableExpired > 0) {
                    $suggestedItems->push($addSuggestion('expired', $returnableExpired));
                }
            } elseif ($isDamaged) {
                $returnableQty = min($maxReturnableFromInvoice, (int)$item->damaged_quantity);
                if ($returnableQty > 0) {
                    $suggestedItems->push($addSuggestion('damaged', $returnableQty));
                }
            } elseif ($isExpired) {
                $returnableQty = min($maxReturnableFromInvoice, (int)$item->quantity);
                if ($returnableQty > 0) {
                    $suggestedItems->push($addSuggestion('expired', $returnableQty));
                }
            }
        }

        $suggestedItems = $suggestedItems->values();

        return view('owner.return_requests.create', compact('suggestedItems'));
    }

    public function getInvoiceItems(Invoice $invoice)
    {
        $invoice->load(['lines.item', 'purchaseOrder.item']);
        
        $lines = $invoice->lines->map(function ($line) {
            $item = $line->item;
            $currentQuantity = (int) ($item->quantity ?? 0);
            $damagedQuantity = (int) ($item->damaged_quantity ?? 0);
            $isExpired = is_object($item) && method_exists($item, 'isExpired') ? $item->isExpired() : false;
            $isDamaged = is_object($item) && method_exists($item, 'isDamaged') ? $item->isDamaged() : false;
            
            $returnableQuantity = min((int)$line->quantity, $currentQuantity);
            if ($isDamaged) {
                $returnableQuantity = min($returnableQuantity, $damagedQuantity);
            }
            
            return [
                'invoice_line_id' => $line->id,
                'item_id'         => $line->item_id,
                'item_name'       => $item->name ?? 'Unknown',
                'quantity'        => (int)$line->quantity,
                'current_quantity'=> $currentQuantity,
                'damaged_quantity'=> $damagedQuantity,
                'returnable_qty'  => $returnableQuantity,
                'is_expired'      => $isExpired,
                'is_damaged'      => $isDamaged,
                'eligible'        => ($isExpired || $isDamaged) && $returnableQuantity > 0,
                'damage_reason'   => $item->damage_reason ?? '',
                'unit_price'      => (float)$line->unit_price,
                'uom'             => $line->uom ?? 'unit',
            ];
        });

        if ($invoice->lines->isEmpty() && $invoice->purchaseOrder && $invoice->purchaseOrder->item) {
            $po = $invoice->purchaseOrder;
            $item = $po->item;
            $currentQuantity = (int) ($item->quantity ?? 0);
            $damagedQuantity = (int) ($item->damaged_quantity ?? 0);
            $isExpired = is_object($item) && method_exists($item, 'isExpired') ? $item->isExpired() : false;
            $isDamaged = is_object($item) && method_exists($item, 'isDamaged') ? $item->isDamaged() : false;
            
            $returnableQuantity = min((int)$po->quantity, $currentQuantity);
            if ($isDamaged) {
                $returnableQuantity = min($returnableQuantity, $damagedQuantity);
            }
            
            $lines->push([
                'invoice_line_id' => 'po_' . $po->id,
                'item_id'         => $item->id,
                'item_name'       => $item->name,
                'quantity'        => (int)$po->quantity,
                'current_quantity'=> $currentQuantity,
                'damaged_quantity'=> $damagedQuantity,
                'returnable_qty'  => $returnableQuantity,
                'is_expired'      => $isExpired,
                'is_damaged'      => $isDamaged,
                'eligible'        => ($isExpired || $isDamaged) && $returnableQuantity > 0,
                'damage_reason'   => $item->damage_reason ?? '',
                'unit_price'      => (float)($po->unit_price ?? $item->unit_price),
                'uom'             => $item->uom ?? 'unit',
            ]);
        }

        return response()->json(['items' => $lines]);
    }

    public function show(ReturnRequest $returnRequest)
    {
        $returnRequest->load(['lines.item', 'supplier', 'purchaseOrder.item.supplier', 'creditNote.items.item', 'invoice', 'createdBy']);
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
                'invoice_id'      => $invoice->id,
                'quantity'        => (int) $request->input('quantity'),
                'reason'          => strtolower((string) $request->input('reason')),
                'damage_remark'   => $request->input('damage_remark') ?? $request->input('notes') ?? 'Damaged item',
            ]],
        ]);
    }

    public function store(Request $request)
    {
        $this->normalizeReturnRequestPayload($request);

        $request->validate([
            'items'                      => 'required|array|min:1',
            'items.*.invoice_line_id'    => 'required|exists:invoice_lines,id',
            'items.*.invoice_id'         => 'required|exists:invoices,id',
            'items.*.quantity'           => 'required|integer|min:1',
            'items.*.reason'             => 'required|in:expired,damaged',
            'items.*.damage_remark'      => 'nullable|string|max:500',
            'notes'                      => 'nullable|string',
        ]);

        // Extra validation: damage_remark is required when reason = damaged
        foreach ($request->items as $i => $itemData) {
            if (($itemData['reason'] ?? '') === 'damaged' && empty($itemData['damage_remark'])) {
                return back()->withErrors(['items' => 'A damage remark is required for all damaged items.'])->withInput();
            }
        }

        // Group submitted items by invoice_id
        $groupedByInvoice = collect($request->items)->groupBy('invoice_id');

        $createdRequests = [];

        foreach ($groupedByInvoice as $invoiceId => $itemsForInvoice) {
            $invoice  = Invoice::with(['supplier', 'lines.item'])->findOrFail($invoiceId);
            $supplier = $invoice->supplier;

            if ($invoice->isPaid()) {
                return back()->withErrors(['items' => "Invoice {$invoice->invoice_number} has been paid and cannot be used for a return request."])->withInput();
            }

            if (!$supplier) {
                return back()->withErrors(['items' => "Invoice {$invoice->invoice_number} has no associated supplier."])->withInput();
            }

            $realLineMap = $invoice->lines->keyBy('id');

            // Validate items by accumulating quantities per invoice line
            $lineQuantities = [];
            foreach ($itemsForInvoice as $itemData) {
                $lineId = $itemData['invoice_line_id'];
                $reason = strtolower($itemData['reason'] ?? '');
                $qty = (int)($itemData['quantity'] ?? 0);
                
                if (!isset($lineQuantities[$lineId])) {
                    $lineQuantities[$lineId] = ['total' => 0, 'damaged' => 0, 'expired' => 0];
                }
                $lineQuantities[$lineId]['total'] += $qty;
                if ($reason === 'damaged') {
                    $lineQuantities[$lineId]['damaged'] += $qty;
                } elseif ($reason === 'expired') {
                    $lineQuantities[$lineId]['expired'] += $qty;
                }
            }

            foreach ($lineQuantities as $lineId => $qtys) {
                $invoiceLine = $realLineMap[$lineId] ?? null;

                if (!$invoiceLine) {
                    return back()->withErrors(['items' => 'A selected item does not belong to its invoice.'])->withInput();
                }

                $item = $invoiceLine->item ?? \App\Models\Item::find($invoiceLine->item_id);
                if (!$item) {
                    return back()->withErrors(['items' => 'A selected return item no longer exists in inventory.'])->withInput();
                }

                $hasPending = \App\Models\ReturnRequestLine::where('item_id', $item->id)
                    ->whereHas('returnRequest', function($q) {
                        $q->where('status', 'Pending');
                    })->exists();

                if ($hasPending) {
                    return back()->withErrors(['items' => "{$item->name} already has a pending return request. Please wait for the supplier to process it first."])->withInput();
                }

                $maxReturnableTotal = min((int)$invoiceLine->quantity, (int)$item->quantity);

                if ($qtys['total'] > $maxReturnableTotal) {
                    return back()->withErrors(['items' => "Total return quantity for {$item->name} ({$qtys['total']}) cannot exceed available quantity ({$maxReturnableTotal})."])->withInput();
                }

                if ($qtys['damaged'] > 0) {
                    if (!$item->isDamaged()) {
                        return back()->withErrors(['items' => "{$item->name} is not flagged as damaged."])->withInput();
                    }
                    $maxDamaged = (int)$item->damaged_quantity;
                    if ($qtys['damaged'] > $maxDamaged) {
                        return back()->withErrors(['items' => "Damaged return quantity for {$item->name} ({$qtys['damaged']}) cannot exceed available damaged stock ({$maxDamaged})."])->withInput();
                    }
                }

                if ($qtys['expired'] > 0) {
                    if (!$item->isExpired()) {
                        return back()->withErrors(['items' => "{$item->name} is not expired and cannot be returned as expired stock."])->withInput();
                    }
                }
            }

            // Generate Return Request Number — format: RR-YYYYMMDD-XXXX
            // Each RR gets a unique daily sequence number.
            $today      = now()->format('Ymd');
            $prefix     = 'RR-' . $today . '-';
            
            // Query the latest once or just use the latest in the DB without double-adding.
            $lastToday  = ReturnRequest::where('return_number', 'like', $prefix . '%')
                ->orderByDesc('return_number')->first();
            $nextIndex  = 1;
            if ($lastToday && preg_match('/RR-\d{8}-(\d+)/', $lastToday->return_number, $matches)) {
                $nextIndex = intval($matches[1]) + 1;
            }
            $returnNumber = $prefix . str_pad($nextIndex, 4, '0', STR_PAD_LEFT);

            $returnRequest = ReturnRequest::create([
                'return_number'     => $returnNumber,
                'invoice_id'        => $invoice->id,
                'invoice_number'    => $invoice->invoice_number,
                'supplier_id'       => $supplier->id,
                'purchase_order_id' => $invoice->purchase_order_id,
                'notes'             => $request->notes,
                'status'            => 'Pending',
                'request_date'      => now()->toDateString(),
                'created_by'        => auth()->id(),
            ]);

            $returnedQtyByItem = [];

            foreach ($itemsForInvoice as $itemData) {
                $lineId      = $itemData['invoice_line_id'];
                $quantity    = (int)$itemData['quantity'];
                $invoiceLine = $realLineMap[$lineId];
                $unitPrice   = (float)$invoiceLine->unit_price;

                \App\Models\ReturnRequestLine::create([
                    'return_request_id' => $returnRequest->id,
                    'invoice_line_id'   => (int)$lineId,
                    'item_id'           => $invoiceLine->item_id,
                    'quantity'          => $quantity,
                    'uom'               => $invoiceLine->uom ?? 'unit',
                    'reason'            => strtolower($itemData['reason']),
                    'damage_remark'     => strtolower($itemData['reason']) === 'damaged'
                                            ? ($itemData['damage_remark'] ?? null)
                                            : null,
                    'unit_price'        => $unitPrice,
                    'subtotal'          => $quantity * $unitPrice,
                ]);

                $returnedQtyByItem[$invoiceLine->item_id] = ($returnedQtyByItem[$invoiceLine->item_id] ?? 0);
                if (strtolower($itemData['reason']) === 'damaged') {
                    $returnedQtyByItem[$invoiceLine->item_id] += $quantity;
                }
            }

            // Update damaged stock counts
            foreach ($returnedQtyByItem as $itemId => $damagedReturnedQty) {
                if ($damagedReturnedQty <= 0) continue;
                $item = \App\Models\Item::find($itemId);
                if ($item && $item->isDamaged()) {
                    $remaining = max(0, (int)$item->damaged_quantity - $damagedReturnedQty);
                    $item->update([
                        'damaged_quantity' => $remaining,
                        'is_damaged'       => $remaining > 0,
                        'damage_reason'    => $remaining > 0 ? $item->damage_reason : null,
                    ]);
                }
            }

            if ($supplier && $supplier->user) {
                \App\Models\Notification::send(
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
                    $callMeBotPhone = trim((string) \App\Models\Setting::get('callmebot_phone', config('services.callmebot.phone')));
                    $callMeBotApiKey = trim((string) \App\Models\Setting::get('callmebot_api_key', config('services.callmebot.apikey')));

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
                    \Illuminate\Support\Facades\Log::channel('whatsapp_alerts')->error('Failed to send return request whatsapp', ['return' => $returnRequest->return_number, 'error' => $e->getMessage()]);
                }
            }

            // Send Email Notification to Supplier
            try {
                (new \App\Services\EmailNotificationService)->sendReturnRequestCreatedToSupplier($returnRequest);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Supplier Return Request Created email failed', ['return' => $returnRequest->return_number, 'error' => $e->getMessage()]);
            }

            $createdRequests[] = $returnRequest;
        }

        if (count($createdRequests) === 1) {
            return redirect()->route('owner.return-requests.show', $createdRequests[0])
                ->with('success', 'Return Request submitted successfully and is awaiting supplier approval.');
        }

        $rrNumbers = implode(', ', array_map(fn($r) => $r->return_number, $createdRequests));
        return redirect()->route('owner.return-requests.index')
            ->with('success', count($createdRequests) . ' return requests submitted successfully and are awaiting supplier approval (' . $rrNumbers . ').');
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

        // Send Email Notification to Supplier
        try {
            (new \App\Services\EmailNotificationService)->sendReturnRequestCreatedToSupplier($returnRequest);
        } catch (\Throwable $e) {
            Log::error('Supplier Return Request Created email failed', ['return' => $returnRequest->return_number, 'error' => $e->getMessage()]);
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
        $returnRequest->update([
            'status'     => $request->status,
            'updated_by' => auth()->id(),
        ]);

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

                // Automatically set the linked invoice status to 'Settled'
                if ($returnRequest->invoice_id) {
                    $linkedInvoice = Invoice::find($returnRequest->invoice_id);
                    if ($linkedInvoice && !$linkedInvoice->isPaid()) {
                        $linkedInvoice->update(['status' => 'Settled']);
                    }
                }
            }

            // Notify owner
            $owner = \App\Models\User::where('role', 'owner')->first();
            if ($owner) {
                \App\Models\Notification::sendToOwners(
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
