<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\ReturnRequest;
use App\Models\Supplier;
use App\Services\TwilioWhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * WhatsAppNotificationController
 *
 * Handles manual "send WhatsApp notification" button clicks from the owner UI.
 * Phone numbers are NO LONGER taken from the request — they are resolved from
 * the database via TwilioWhatsAppService::getOwnerPhone() and getSupplierPhone().
 *
 * If a number is missing (null / empty), the service logs a warning and returns
 * an error response — it does NOT throw an exception.
 *
 * All routes are POST and are protected by the owner middleware group.
 */
class WhatsAppNotificationController extends Controller
{
    protected TwilioWhatsAppService $whatsapp;

    public function __construct(TwilioWhatsAppService $whatsapp)
    {
        $this->whatsapp = $whatsapp;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // OWNER NOTIFICATIONS
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * POST owner/whatsapp/notify-low-stock
     * Send a low-stock alert to the owner's WhatsApp (number from DB).
     */
    public function notifyLowStock(Request $request)
    {
        $request->validate([
            'item_name'   => 'required|string|max:255',
            'current_qty' => 'required|integer|min:0',
        ]);

        // Resolve owner phone from database — not from request / .env
        $ownerPhone = $this->whatsapp->getOwnerPhone();
        if (!$ownerPhone) {
            return $this->missingPhoneResponse('owner');
        }

        $result = $this->whatsapp->notifyLowStock(
            $ownerPhone,
            $request->item_name,
            (int) $request->current_qty
        );

        return $this->jsonResponse($result, 'Low stock alert sent successfully.');
    }

    /**
     * POST owner/whatsapp/notify-expiry
     * Send an expiry-nearing alert to the owner's WhatsApp (number from DB).
     */
    public function notifyExpiry(Request $request)
    {
        $request->validate([
            'item_name'   => 'required|string|max:255',
            'expiry_date' => 'required|string|max:50',
        ]);

        $ownerPhone = $this->whatsapp->getOwnerPhone();
        if (!$ownerPhone) {
            return $this->missingPhoneResponse('owner');
        }

        $result = $this->whatsapp->notifyExpiryNearing(
            $ownerPhone,
            $request->item_name,
            $request->expiry_date
        );

        return $this->jsonResponse($result, 'Expiry alert sent successfully.');
    }



    /**
     * POST owner/whatsapp/notify-rr-approved/{returnRequest}
     * Notify the owner that a return request has been approved.
     */
    public function notifyReturnRequestApproved(Request $request, ReturnRequest $returnRequest)
    {
        $ownerPhone = $this->whatsapp->getOwnerPhone();
        if (!$ownerPhone) {
            return $this->missingPhoneResponse('owner');
        }

        $result = $this->whatsapp->notifyReturnRequestApproved(
            $ownerPhone,
            $returnRequest->return_number
        );

        Log::channel('whatsapp_alerts')->info('Manual RR approved notification sent', [
            'rr_id'  => $returnRequest->id,
            'result' => $result,
        ]);

        return $this->jsonResponse($result, 'Return request approval notification sent.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // SUPPLIER NOTIFICATIONS
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * POST owner/whatsapp/invite-supplier/{supplier}
     * Send a portal invitation to a supplier via WhatsApp.
     * Uses supplier->contact_phone resolved via service.
     */
    public function inviteSupplier(Request $request, Supplier $supplier)
    {
        // Resolve supplier phone from DB via the service helper
        $supplierPhone = $this->whatsapp->getSupplierPhone($supplier);
        if (!$supplierPhone) {
            return $this->missingPhoneResponse('supplier', $supplier->name);
        }

        $inviteLink = $supplier->portal_link ?? route('supplier.login');

        $result = $this->whatsapp->inviteSupplier(
            $supplierPhone,
            $supplier->contact_person ?: $supplier->name,
            $inviteLink
        );

        Log::channel('whatsapp_alerts')->info('Manual supplier invite sent', [
            'supplier_id' => $supplier->id,
            'result'      => $result,
        ]);

        if ($result['success']) {
            $supplier->update(['invite_whatsapp_status' => 'sent']);
        }

        return $this->jsonResponse($result, 'Supplier invite sent via WhatsApp.');
    }



    /**
     * POST owner/whatsapp/notify-return-request/{returnRequest}
     * Notify a supplier that a return request has been raised.
     */
    public function notifyReturnRequest(Request $request, ReturnRequest $returnRequest)
    {
        $supplier = $returnRequest->supplier;

        if (!$supplier) {
            return response()->json([
                'success' => false,
                'message' => 'No supplier is linked to this return request.',
            ], 422);
        }

        $supplierPhone = $this->whatsapp->getSupplierPhone($supplier);
        if (!$supplierPhone) {
            return $this->missingPhoneResponse('supplier', $supplier->name);
        }

        // Aggregate reasons from return lines
        $reason = $returnRequest->lines
            ->pluck('reason')
            ->filter()
            ->unique()
            ->implode(', ');

        $reason = $reason ?: ($returnRequest->notes ?? 'Not specified');

        $result = $this->whatsapp->notifyReturnRequest(
            $supplierPhone,
            $returnRequest->return_number,
            $reason
        );

        Log::channel('whatsapp_alerts')->info('Manual return request notification sent to supplier', [
            'rr_id'  => $returnRequest->id,
            'result' => $result,
        ]);

        return $this->jsonResponse($result, 'Return request notification sent to supplier.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Build a short human-readable PO details string.
     */
    protected function buildPoDetails(PurchaseOrder $purchaseOrder): string
    {
        if ($purchaseOrder->orderItems && $purchaseOrder->orderItems->count() > 0) {
            $lines = $purchaseOrder->orderItems->map(function ($poItem) {
                $name = optional($poItem->item)->name ?? 'Unknown';
                return "{$name} × {$poItem->quantity}";
            })->implode(', ');
        } else {
            $itemName = optional($purchaseOrder->item)->name ?? 'Unknown';
            $lines    = "{$itemName} × {$purchaseOrder->quantity}";
        }

        return "Items: {$lines} | Total: RM " . number_format($purchaseOrder->total_amount, 2);
    }

    /**
     * Return a standardised JSON error for a missing phone number.
     *
     * @param  string       $party  'owner' or 'supplier'
     * @param  string|null  $name   Supplier name, if applicable
     */
    protected function missingPhoneResponse(string $party, ?string $name = null): \Illuminate\Http\JsonResponse
    {
        $who = $party === 'owner'
            ? 'your own WhatsApp number (My Profile → WhatsApp Number)'
            : "the supplier's contact phone number ({$name})";

        return response()->json([
            'success' => false,
            'message' => "WhatsApp notification skipped — please configure {$who} first.",
        ], 422);
    }

    /**
     * Return a standardised JSON response.
     */
    protected function jsonResponse(array $result, string $successMessage): \Illuminate\Http\JsonResponse
    {
        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => $successMessage,
                'sid'     => $result['sid'],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'WhatsApp notification failed: ' . ($result['error'] ?? 'Unknown error.'),
        ], 500);
    }
}
