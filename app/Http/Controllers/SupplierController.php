<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\ReturnRequest;
use App\Models\Notification;
use App\Models\Delivery;
use App\Models\CreditNote;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;

class SupplierController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:supplier']);
    }

    public function dashboard()
    {
        $user = auth()->user();
        $supplier = $user->supplier;

        if (!$supplier) {
            abort(403, 'Your account is not linked to a supplier profile. Please contact the owner.');
        }

        $orders = PurchaseOrder::where('supplier_id', $supplier->id)
            ->where('status', 'Pending')
            ->orderBy('order_date', 'asc')
            ->limit(5)
            ->get();
        $returns = ReturnRequest::where('supplier_id', $supplier->id)
            ->where('status', 'Pending')
            ->latest()
            ->limit(5)
            ->get();
        $creditNotes = \App\Models\CreditNote::where('supplier_id', $supplier->id)->latest()->limit(5)->get();
        return view('supplier.dashboard', compact('orders', 'returns', 'creditNotes'));
    }

    public function orders()
    {
        $supplier = auth()->user()->supplier;
        if (!$supplier) abort(403);

        $orders = PurchaseOrder::where('supplier_id', $supplier->id)
            ->with(['item', 'delivery'])
            ->orderByRaw("CASE WHEN status = 'Pending' THEN 0 ELSE 1 END")
            ->latest()
            ->paginate(15);

        return view('supplier.purchase_orders.index', compact('orders'));
    }

    public function returnRequests(Request $request)
    {
        $supplier = auth()->user()->supplier;
        if (!$supplier) abort(403);

        $query = ReturnRequest::with(['item', 'creditNote', 'lines.item'])
            ->where('supplier_id', $supplier->id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('reason')) {
            $query->where('reason', $request->reason);
        }

        $query->orderByRaw("CASE WHEN status = 'Pending' THEN 0 ELSE 1 END")
              ->orderBy('created_at', 'desc');

        $returns = $query->paginate(15);
        return view('supplier.return_requests.index', compact('returns'));
    }

    public function creditNotes()
    {
        $supplier = auth()->user()->supplier;
        if (!$supplier) abort(403);

        $creditNotes = \App\Models\CreditNote::with('returnRequest.item')
            ->where('supplier_id', $supplier->id)
            ->latest()
            ->paginate(15);

        return view('supplier.credit_notes.index', compact('creditNotes'));
    }

    public function creditNoteShow(\App\Models\CreditNote $creditNote)
    {
        $supplier = auth()->user()->supplier;
        if (!$supplier || $creditNote->supplier_id !== $supplier->id) {
            abort(403);
        }

        $creditNote->load('returnRequest.item', 'items.item');
        return view('supplier.credit_notes.show', compact('creditNote'));
    }

    public function confirmOrder(Request $request, PurchaseOrder $order)
    {
        if ($order->supplier_id != auth()->user()->supplier->id) {
            abort(403);
        }
        $request->validate([
            'status' => 'required|in:Approved,Rejected',
            'delivery_date' => 'required_if:status,Approved|nullable|date|after:today',
            'delivery_time' => 'nullable|date_format:H:i',
            'notes' => 'nullable|string',
        ]);

        if ($request->status === 'Approved') {
            $deliveryDate = \Carbon\Carbon::parse($request->delivery_date);
            $deliveryTime = $request->delivery_time
                ? \Carbon\Carbon::parse($request->delivery_time)
                : null;

            // Block Sunday entirely
            if ($deliveryDate->isSunday()) {
                return back()->withErrors([
                    'delivery_date' => 'Delivery is not allowed on Sundays.'
                ])->withInput();
            }

            // Block public holidays
            $isHoliday = \App\Models\PublicHoliday::whereDate('date', $deliveryDate->toDateString())->exists();
            if ($isHoliday) {
                return back()->withErrors([
                    'delivery_date' => 'Delivery is not allowed on public holidays.'
                ])->withInput();
            }

            // Validate time if provided
            if ($deliveryTime) {
                $hour   = (int) $deliveryTime->format('H');
                $minute = (int) $deliveryTime->format('i');
                $timeInMinutes = $hour * 60 + $minute;

                $startMinutes = 8 * 60;       // 08:00
                $endWeekday   = 17 * 60;      // 17:00
                $endSaturday  = 12 * 60;      // 12:00 noon

                if ($deliveryDate->isSaturday()) {
                    // Saturday: 08:00 – 12:00 only
                    if ($timeInMinutes < $startMinutes || $timeInMinutes > $endSaturday) {
                        return back()->withErrors([
                            'delivery_time' => 'Saturday delivery must be between 08:00 and 12:00 noon.'
                        ])->withInput();
                    }
                } else {

                // Send WhatsApp to owner about PO status
                try {
                    $callMeBotPhone = trim((string) Setting::get('callmebot_phone', config('services.callmebot.phone')));
                    $callMeBotApiKey = trim((string) Setting::get('callmebot_api_key', config('services.callmebot.apikey')));
                    $ownerPhone = trim((string) Setting::get('owner_whatsapp_number', config('services.twilio.owner_whatsapp_number')));

                    if ($callMeBotPhone !== '' && $callMeBotApiKey !== '') {
                        $cb = new \App\Services\CallMeBotService();
                        $cb->sendPurchaseOrderStatusToOwner(strtolower($request->status) === 'approved' ? 'approved' : 'rejected', $order->po_number, optional($order->supplier)->name ?? '', $ownerPhone, ($order->delivery ? optional($order->delivery)->delivery_date : null));
                    } elseif ($ownerPhone && config('services.twilio.sid')) {
                        $w = new \App\Services\WhatsAppService();
                        if ($request->status === 'Approved') {
                            $msg = "✅ PURCHASE ORDER APPROVED\n\nPO Number: {$order->po_number}\nSupplier: " . (optional($order->supplier)->name ?? '') . "\n\nExpected Delivery:\n" . ($request->delivery_date ?: 'TBD');
                        } else {
                            $msg = "❌ PURCHASE ORDER REJECTED\n\nPO Number: {$order->po_number}\nSupplier: " . (optional($order->supplier)->name ?? '') . "\n\nPlease review supplier comments.";
                        }
                        $w->sendMessage($ownerPhone, $msg);
                    }
                } catch (\Throwable $e) {
                    Log::channel('whatsapp_alerts')->error('Failed to send PO status whatsapp', ['po' => $order->po_number, 'error' => $e->getMessage()]);
                }
                    // Monday – Friday: 08:00 – 17:00
                    if ($timeInMinutes < $startMinutes || $timeInMinutes > $endWeekday) {
                        return back()->withErrors([
                            'delivery_time' => 'Weekday delivery must be between 08:00 and 17:00.'
                        ])->withInput();
                    }
                }
            }
        }

        $order->update(['status' => $request->status]);

        if ($request->status === 'Approved') {
            $deliveryDate = $request->delivery_date ?: now()->addDays(3)->toDateString();

            $order->delivery()->updateOrCreate(
                ['purchase_order_id' => $order->id],
                [
                    'delivery_date' => $deliveryDate,
                    'delivery_time' => $request->delivery_time,
                    'notes' => $request->notes,
                ]
            );

            // Auto-generate the invoice once the supplier approves the order so downstream
            // invoice and export flows continue to work without an extra owner action.
            $purchaseOrderController = app(\App\Http\Controllers\PurchaseOrderController::class);
            $purchaseOrderController->generateInvoiceForPurchaseOrder($order);
        } elseif ($request->status === 'Rejected' && $order->delivery) {
            $order->delivery()->delete();
        }

        $owner = \App\Models\User::where('role', 'owner')->first();
        if ($owner) {
            Notification::send(
                $owner,
                'purchase_order_' . strtolower($request->status),
                "Purchase order {$order->po_number} has been {$request->status}.",
                [
                    'po_number' => $order->po_number,
                    'status' => $request->status,
                    'supplier_name' => optional($order->supplier)->name ?? auth()->user()->supplier->name,
                ]
            );

            if ($request->status === 'Approved' && $request->delivery_date) {
                Notification::send(
                    $owner,
                    'purchase_order_delivered',
                    "Delivery details submitted for purchase order {$order->po_number}.",
                    [
                        'po_number' => $order->po_number,
                        'delivery_date' => $request->delivery_date,
                        'delivery_time' => $request->delivery_time,
                    ]
                );
            }
        }

        return back()->with('success', 'Order ' . strtolower($request->status));
    }

    public function setDelivery(Request $request, PurchaseOrder $order)
    {
        if ($order->supplier_id != auth()->user()->supplier->id) {
            abort(403);
        }
        if ($order->status !== 'Approved') {
            return back()->with('error', 'Delivery can only be set for approved orders.');
        }
        $request->validate([
            'delivery_date' => 'required|date|after:today',
            'delivery_time' => 'nullable|date_format:H:i',
        ]);

        $deliveryDate = \Carbon\Carbon::parse($request->delivery_date);
        $deliveryTime = $request->delivery_time
            ? \Carbon\Carbon::parse($request->delivery_time)
            : null;

        // Block Sunday entirely
        if ($deliveryDate->isSunday()) {
            return back()->withErrors([
                'delivery_date' => 'Delivery is not allowed on Sundays.'
            ])->withInput();
        }

        // Block public holidays
        $isHoliday = \App\Models\PublicHoliday::whereDate('date', $deliveryDate->toDateString())->exists();
        if ($isHoliday) {
            return back()->withErrors([
                'delivery_date' => 'Delivery is not allowed on public holidays.'
            ])->withInput();
        }

        // Validate time if provided
        if ($deliveryTime) {
            $hour   = (int) $deliveryTime->format('H');
            $minute = (int) $deliveryTime->format('i');
            $timeInMinutes = $hour * 60 + $minute;

            $startMinutes = 8 * 60;       // 08:00
            $endWeekday   = 17 * 60;      // 17:00
            $endSaturday  = 12 * 60;      // 12:00 noon

            if ($deliveryDate->isSaturday()) {
                // Saturday: 08:00 – 12:00 only
                if ($timeInMinutes < $startMinutes || $timeInMinutes > $endSaturday) {
                    return back()->withErrors([
                        'delivery_time' => 'Saturday delivery must be between 08:00 and 12:00 noon.'
                    ])->withInput();
                }
            } else {
                // Monday – Friday: 08:00 – 17:00
                if ($timeInMinutes < $startMinutes || $timeInMinutes > $endWeekday) {
                    return back()->withErrors([
                        'delivery_time' => 'Weekday delivery must be between 08:00 and 17:00.'
                    ])->withInput();
                }
            }
        }

        $order->delivery()->updateOrCreate(
            ['purchase_order_id' => $order->id],
            [
                'delivery_date' => $request->delivery_date,
                'delivery_time' => $request->delivery_time,
            ]
        );

        $owner = \App\Models\User::where('role', 'owner')->first();
        if ($owner) {
            Notification::send(
                $owner,
                'purchase_order_delivered',
                "Delivery details submitted for purchase order {$order->po_number}.",
                [
                    'po_number'     => $order->po_number,
                    'delivery_date' => $request->delivery_date,
                    'delivery_time' => $request->delivery_time,                ]
            );
            try {
                $callMeBotPhone = trim((string) Setting::get('callmebot_phone', config('services.callmebot.phone')));
                $callMeBotApiKey = trim((string) Setting::get('callmebot_api_key', config('services.callmebot.apikey')));
                $ownerPhone = trim((string) Setting::get('owner_whatsapp_number', config('services.twilio.owner_whatsapp_number')));

                if ($callMeBotPhone !== '' && $callMeBotApiKey !== '') {
                    $cb = new \App\Services\CallMeBotService();
                    $cb->sendPurchaseOrderStatusToOwner('delivered', $order->po_number, optional($order->supplier)->name ?? '', $ownerPhone, \Carbon\Carbon::parse($request->delivery_date));
                } elseif ($ownerPhone && config('services.twilio.sid')) {
                    $w = new \App\Services\WhatsAppService();
                    $msg = "🚚 DELIVERY SCHEDULED\n\nPO Number: {$order->po_number}\nSupplier: " . (optional($order->supplier)->name ?? '') . "\n\nDelivery Date:\n" . ($request->delivery_date ?: 'TBD');
                    $w->sendMessage($ownerPhone, $msg);
                }
            } catch (\Throwable $e) {
                Log::channel('whatsapp_alerts')->error('Failed to send delivery whatsapp', ['po' => $order->po_number, 'error' => $e->getMessage()]);
            }
        }

        return back()->with('success', 'Delivery schedule saved.');
    }

    public function updateReturnStatus(Request $request, ReturnRequest $return)
    {
        if ($return->supplier_id != auth()->user()->supplier->id) {
            abort(403);
        }
        $request->validate([
            'status'              => 'required|in:Approved,Rejected',
            'rejection_reason'    => 'required_if:status,Rejected|nullable|string|max:500',
            'lines'               => 'nullable|array',
            'lines.*.line_id'     => 'required|exists:return_request_lines,id',
            'lines.*.approved_qty'=> 'required|integer|min:0',
        ]);

        if ($return->status !== 'Pending') {
            return back()->with('error', 'This return request has already been reviewed.');
        }

        $return->update(['status' => $request->status]);

        if ($request->status === 'Rejected') {
            $return->lines()->update([
                'approved_qty'      => 0,
                'approved_subtotal' => 0,
            ]);
        }

        // Create credit note and adjust stock if approved
        $hasCreditNote = \App\Models\CreditNote::where('return_id', $return->id)->exists();
        if ($request->status === 'Approved' && !$hasCreditNote) {

            // Save per-line approved quantities and calculate credit amount based on INVOICE lines
            $lineItems    = $return->lines()->with('item')->get();
            $creditAmount = 0;

            foreach ($lineItems as $line) {
                // Find the approved qty submitted for this line (default to full qty if not provided)
                $approvedQty = $line->quantity; // default: full approval
                if ($request->filled('lines')) {
                    foreach ($request->lines as $submitted) {
                        if ((int)$submitted['line_id'] === $line->id) {
                            $approvedQty = min($line->quantity, max(0, (int)$submitted['approved_qty']));
                            break;
                        }
                    }
                }

                // Retrieve the unit price from the linked invoice line
                $invoiceLine = \App\Models\InvoiceLine::where('invoice_id', $return->invoice_id)
                    ->where('item_id', $line->item_id)
                    ->first();
                $unitPrice = $invoiceLine ? (float)$invoiceLine->unit_price : 0;

                $approvedSubtotal = round($approvedQty * $unitPrice, 2);
                $line->update([
                    'approved_qty'      => $approvedQty,
                    'approved_subtotal' => $approvedSubtotal,
                ]);
                $creditAmount += $approvedSubtotal;
            }

            if ($creditAmount <= 0) {
                $return->update(['status' => 'Pending']);
                $return->lines()->update([
                    'approved_qty'      => null,
                    'approved_subtotal' => null,
                ]);

                return back()->with('error', 'Approve at least one item quantity, or reject the return request.');
            }

            // Build credit note ID
            $invoice   = $return->invoice;
            $poNumber  = $return->purchaseOrder ? $return->purchaseOrder->po_number
                       : ($invoice ? $invoice->invoice_number : 'N/A');
            $creditNoteId = 'CN-' . $poNumber . '-' . str_pad($return->id, 3, '0', STR_PAD_LEFT);

            $creditNote = \App\Models\CreditNote::create([
                'credit_note_id'    => $creditNoteId,
                'return_id'         => $return->id,
                'supplier_id'       => $return->supplier_id,
                'invoice_id'        => $return->invoice_id,
                'purchase_order_id' => $return->purchase_order_id,
                'amount'            => $creditAmount,
                'remaining_balance' => $creditAmount,
                'issue_date'        => now()->toDateString(),
                'status'            => 'Unused',
                'reason'            => $request->reason ?: $request->rejection_reason,
            ]);

            // Create credit note line items (only for approved qty > 0)
            foreach ($lineItems as $line) {
                if (!$line->item || $line->approved_qty <= 0) continue;

                $invoiceLine = \App\Models\InvoiceLine::where('invoice_id', $return->invoice_id)
                    ->where('item_id', $line->item_id)
                    ->first();
                $unitPrice = $invoiceLine ? (float)$invoiceLine->unit_price : 0;

                \App\Models\CreditNoteItem::create([
                    'credit_note_id'         => $creditNote->id,
                    'return_request_line_id' => $line->id,
                    'item_id'                => $line->item_id,
                    'quantity'               => $line->approved_qty,
                    'uom'                    => $line->uom,
                    'unit_price'             => $unitPrice,
                    'subtotal'               => $line->approved_subtotal,
                ]);

                // Decrement stock ONLY for approved quantities
                $decrementBy = min($line->item->quantity, $line->approved_qty);
                if ($decrementBy > 0) {
                    $line->item->decrement('quantity', $decrementBy);
                }

                // Clear the damaged flag if this was a damaged return
                if (strtolower($line->reason) === 'damaged') {
                    $line->item->update(['is_damaged' => false, 'damage_reason' => null]);
                }
            }

            // Update the linked invoice status to settled
            if ($return->invoice && $creditAmount > 0) {
                $return->invoice->update(['status' => 'settled']);
            }
        }

        // Notify owner
        $owner = \App\Models\User::where('role', 'owner')->first();
        if ($owner) {
            Notification::send(
                $owner,
                'return_request_' . strtolower($request->status),
                "Return request {$return->return_number} has been " . strtolower($request->status) . ".",
                ['return_number' => $return->return_number, 'status' => $request->status]
            );

            try {
                $callMeBotPhone = trim((string) Setting::get('callmebot_phone', config('services.callmebot.phone')));
                $callMeBotApiKey = trim((string) Setting::get('callmebot_api_key', config('services.callmebot.apikey')));
                $ownerPhone = trim((string) Setting::get('owner_whatsapp_number', config('services.twilio.owner_whatsapp_number')));

                if ($callMeBotPhone !== '' && $callMeBotApiKey !== '') {
                    $cb = new \App\Services\CallMeBotService();
                    $cb->sendReturnRequestStatusToOwner(strtolower($request->status) === 'approved' ? 'approved' : 'rejected', optional($return->item)->name ?? '', $return->lines()->sum('quantity'), $ownerPhone);
                } elseif ($ownerPhone && config('services.twilio.sid')) {
                    $w = new \App\Services\WhatsAppService();
                    if ($request->status === 'Approved') {
                        $msg = "✅ RETURN REQUEST APPROVED\n\nItem: " . (optional($return->item)->name ?? '') . "\nQuantity: " . $return->lines()->sum('quantity') . "\n\nCredit Note will be processed.";
                    } else {
                        $msg = "❌ RETURN REQUEST REJECTED\n\nItem: " . (optional($return->item)->name ?? '') . "\n\nPlease review supplier comments.";
                    }
                    $w->sendMessage($ownerPhone, $msg);
                }
            } catch (\Throwable $e) {
                Log::channel('whatsapp_alerts')->error('Failed to send return request whatsapp', ['return' => $return->return_number, 'error' => $e->getMessage()]);
            }
        }

        return back()->with('success', 'Return request status updated successfully.');
    }

    public function getCreditNote(ReturnRequest $return)
    {
        if ($return->supplier_id != auth()->user()->supplier->id) {
            abort(403);
        }

        if (!$return->creditNote || !in_array($return->status, ['Approved', 'Credit Applied'])) {
            return response()->json(['success' => false, 'message' => 'Credit note not available.']);
        }

        $creditNote = $return->creditNote->load(['supplier', 'returnRequest.item', 'purchaseOrder']);

        return response()->json([
            'success' => true,
            'pdf_url' => route('supplier.credit-notes.show', $creditNote)
        ]);
    }

    public function exportSinglePdf(CreditNote $creditNote)
    {
        if ($creditNote->supplier_id != auth()->user()->supplier->id) {
            abort(403);
        }

        $creditNote->load(['supplier', 'returnRequest.item', 'purchaseOrder']);

        $filename = 'credit-note-' . $creditNote->credit_note_id . '.csv';
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($creditNote) {
            $handle = fopen('php://output', 'w');
            $item = optional($creditNote->returnRequest)->item;
            fputcsv($handle, ['Field', 'Value']);
            fputcsv($handle, ['Credit Note ID',         $creditNote->credit_note_id]);
            fputcsv($handle, ['Supplier',               optional($creditNote->supplier)->name ?? '']);
            fputcsv($handle, ['Return Request',         optional($creditNote->returnRequest)->return_number ?? '']);
            fputcsv($handle, ['Amount (RM)',            number_format($creditNote->amount, 2)]);
            fputcsv($handle, ['Remaining Balance (RM)', number_format($creditNote->remaining_balance, 2)]);
            fputcsv($handle, ['Status',                $creditNote->status]);
            fputcsv($handle, ['Issue Date',             $creditNote->issue_date->format('Y-m-d')]);
            fputcsv($handle, ['Reason',                 $creditNote->reason ?? '']);
            fputcsv($handle, ['Linked PO',              optional($creditNote->purchaseOrder)->po_number ?? '']);
            fputcsv($handle, []);
            fputcsv($handle, ['Item', 'Reason', 'Qty', 'Unit Price', 'Subtotal']);

            $items = $creditNote->items ?? collect();
            if ($items->isEmpty()) {
                fputcsv($handle, [optional($item)->name ?? '', optional($creditNote->returnRequest)->reason ?? '', optional($creditNote->returnRequest)->quantity ?? '', optional($item)->unit_price ? number_format($item->unit_price, 2) : '', number_format($creditNote->amount, 2)]);
            } else {
                foreach ($items as $creditItem) {
                    fputcsv($handle, [
                        optional($creditItem->item)->name ?? '',
                        optional($creditItem->returnRequestLine)->reason ?? optional($creditNote->returnRequest)->reason ?? '',
                        $creditItem->quantity,
                        number_format($creditItem->unit_price, 2),
                        number_format($creditItem->subtotal, 2),
                    ]);
                }
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function invoices(Request $request)
    {
        $supplier = auth()->user()->supplier;
        if (!$supplier) abort(403);

        $query = \App\Models\Invoice::with('purchaseOrder')
            ->where('supplier_id', $supplier->id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $query->orderBy('created_at', 'desc');

        $invoices = $query->paginate(15);
        return view('supplier.invoices.index', compact('invoices'));
    }

    public function exportInvoicePdf(\App\Models\Invoice $invoice)
    {
        if ($invoice->supplier_id != auth()->user()->supplier->id) {
            abort(403);
        }

        $invoice->load('purchaseOrder', 'supplier');
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('owner.invoices.pdf', compact('invoice'));
        return $pdf->download('invoice-' . $invoice->invoice_number . '.pdf');
    }

    /**
     * Validate delivery date based on day of week and public holidays
     * Monday-Friday: allow 08:00-17:00
     * Saturday: allow 08:00-12:00
     * Sunday: reject entirely
     * Public holidays: reject if found
     */
    private function validateDeliveryDateTime($date, $time)
    {
        $deliveryDate = \Carbon\Carbon::parse($date);

        // Check if date is a public holiday
        if (\App\Models\PublicHoliday::isHoliday($date)) {
            return [
                'valid' => false,
                'message' => 'Delivery cannot be scheduled on public holidays.'
            ];
        }

        $dayOfWeek = $deliveryDate->dayOfWeek; // 0 = Sunday, 6 = Saturday
        $timeHour = (int)\Carbon\Carbon::parse($time)->format('H');

        // Sunday: reject
        if ($dayOfWeek === 0) {
            return [
                'valid' => false,
                'message' => 'Delivery cannot be scheduled on Sundays.'
            ];
        }

        // Saturday: allow 08:00-12:00 only
        if ($dayOfWeek === 6) {
            if ($timeHour < 8 || $timeHour >= 12) {
                return [
                    'valid' => false,
                    'message' => 'Saturday deliveries must be between 08:00 and 11:59.'
                ];
            }
        }

        // Monday-Friday: allow 08:00-17:00
        if ($dayOfWeek >= 1 && $dayOfWeek <= 5) {
            if ($timeHour < 8 || $timeHour >= 17) {
                return [
                    'valid' => false,
                    'message' => 'Weekday deliveries must be between 08:00 and 16:59.'
                ];
            }
        }

        return ['valid' => true];
    }

    /**
     * Validate delivery date only (no specific time)
     */
    private function validateDeliveryDate($date)
    {
        $deliveryDate = \Carbon\Carbon::parse($date);

        // Check if date is a public holiday
        if (\App\Models\PublicHoliday::isHoliday($date)) {
            return [
                'valid' => false,
                'message' => 'Delivery cannot be scheduled on public holidays.'
            ];
        }

        $dayOfWeek = $deliveryDate->dayOfWeek;

        // Sunday: reject
        if ($dayOfWeek === 0) {
            return [
                'valid' => false,
                'message' => 'Delivery cannot be scheduled on Sundays.'
            ];
        }

        return ['valid' => true];
    }
}
