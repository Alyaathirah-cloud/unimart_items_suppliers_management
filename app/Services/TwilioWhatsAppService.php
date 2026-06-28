<?php

namespace App\Services;

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;

/**
 * TwilioWhatsAppService
 *
 * Sends WhatsApp messages via the Twilio WhatsApp Sandbox (or production).
 * All credentials are read from config/services.php which pulls from .env.
 *
 * IMPORTANT – Sandbox requirement:
 *   Every recipient phone number must have first joined the Twilio Sandbox by
 *   sending "join <sandbox-code>" to +1 415 523 8886 before messages can be
 *   delivered to them.
 */
class TwilioWhatsAppService
{
    protected ?Client $twilio = null;
    protected string  $from;

    public function __construct()
    {
        $sid         = config('services.twilio.sid');
        $token       = config('services.twilio.token');
        $this->from  = config('services.twilio.whatsapp_from', 'whatsapp:+14155238886');

        // Only initialise the client if credentials are present
        if ($sid && $token) {
            $this->twilio = new Client($sid, $token);
        }
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    /**
     * Normalise a phone number to E.164 format (+60XXXXXXXXX).
     * Handles Malaysian local numbers (01X) and strips non-digit characters.
     */
    protected function normalizeNumber(string $number): string
    {
        // Strip everything except digits and leading +
        $number = preg_replace('/[^0-9+]/', '', $number);
        $number = ltrim($number, '+');

        // Malaysian local: 01X... → 601X...
        if (preg_match('/^0(1\d{8,9})$/', $number, $m)) {
            $number = '6' . $number;
        }

        return '+' . $number;
    }

    // ── DB phone resolvers ────────────────────────────────────────────────────

    /**
     * Fetch the owner's WhatsApp number from the database.
     *
     * Looks for the first user with role 'owner' and returns their
     * whatsapp_number column value. Falls back to null if none is set,
     * rather than falling back to .env, so misconfiguration is surfaced early.
     *
     * @return string|null
     */
    public function getOwnerPhone(): ?string
    {
        $owner = User::where('role', 'owner')->first();

        if (!$owner || empty($owner->whatsapp_number)) {
            Log::channel('whatsapp_alerts')->warning(
                'Owner WhatsApp number is not set. '
                . 'Go to My Profile → WhatsApp Number to configure it.'
            );
            return null;
        }

        return $owner->whatsapp_number;
    }

    /**
     * Fetch the WhatsApp number for a given Supplier.
     *
     * Uses contact_phone — no separate whatsapp_number column is needed
     * because suppliers only have one phone number recorded.
     *
     * @param  \App\Models\Supplier  $supplier
     * @return string|null
     */
    public function getSupplierPhone(Supplier $supplier): ?string
    {
        if (empty($supplier->contact_phone)) {
            Log::channel('whatsapp_alerts')->warning(
                "Supplier [{$supplier->id}] {$supplier->name} has no contact_phone set. "
                . 'WhatsApp notification skipped.'
            );
            return null;
        }

        return $supplier->contact_phone;
    }

    // ── Core send ────────────────────────────────────────────────────────────

    /**
     * Send a raw WhatsApp message to any phone number.
     *
     * @param  string $recipientPhone  E.g. "+60123456789" or "0123456789"
     * @param  string $message
     * @return array  ['success' => bool, 'sid' => string|null, 'error' => string|null]
     */
    public function sendMessage(string $recipientPhone, string $message): array
    {
        // Guard: never attempt to send to a blank/null number
        $recipientPhone = trim($recipientPhone);
        if ($recipientPhone === '') {
            $err = 'WhatsApp send skipped — recipient phone number is empty. '
                 . 'Ensure the number is configured in the database.';
            Log::channel('whatsapp_alerts')->warning($err);
            return ['success' => false, 'sid' => null, 'error' => $err];
        }

        if (!$this->twilio) {
            $err = 'Twilio is not configured (check TWILIO_ACCOUNT_SID / TWILIO_AUTH_TOKEN in .env).';
            Log::channel('whatsapp_alerts')->warning($err);
            return ['success' => false, 'sid' => null, 'error' => $err];
        }

        try {
            $to = 'whatsapp:' . $this->normalizeNumber($recipientPhone);

            $result = $this->twilio->messages->create($to, [
                'from' => $this->from,
                'body' => $message,
            ]);

            Log::channel('whatsapp_alerts')->info('Twilio WhatsApp sent', [
                'sid' => $result->sid,
                'to'  => $to,
            ]);

            return ['success' => true, 'sid' => $result->sid, 'error' => null];

        } catch (\Exception $e) {
            Log::channel('whatsapp_alerts')->error('Twilio WhatsApp failed', [
                'to'    => $recipientPhone,
                'error' => $e->getMessage(),
            ]);
            return ['success' => false, 'sid' => null, 'error' => $e->getMessage()];
        }
    }

    // ── Owner notifications ───────────────────────────────────────────────────

    /**
     * Notify owner that an item is running low on stock.
     */
    public function notifyLowStock(string $ownerPhone, string $productName, int $currentQty): array
    {
        $message = "⚠️ *Low Stock Alert* — 22UniMart\n\n"
            . "Product: {$productName}\n"
            . "Current Stock: {$currentQty} unit(s)\n\n"
            . "Please raise a Purchase Order to restock soon.";

        return $this->sendMessage($ownerPhone, $message);
    }

    /**
     * Notify owner that a product is nearing its expiry date.
     */
    public function notifyExpiryNearing(string $ownerPhone, string $productName, string $expiryDate): array
    {
        $message = "⏰ *Expiry Reminder* — 22UniMart\n\n"
            . "Product: {$productName}\n"
            . "Expiry Date: {$expiryDate}\n\n"
            . "Please inspect the stock and take necessary action.";

        return $this->sendMessage($ownerPhone, $message);
    }

    /**
     * Notify owner that a purchase order has been approved by the supplier.
     */
    public function notifyPurchaseOrderApproved(string $ownerPhone, string $poNumber): array
    {
        $message = "✅ *Purchase Order Approved* — 22UniMart\n\n"
            . "PO Number: {$poNumber}\n\n"
            . "The supplier has approved your purchase order. "
            . "Check the portal for the scheduled delivery date.";

        return $this->sendMessage($ownerPhone, $message);
    }

    /**
     * Notify owner that a return request has been approved by the supplier.
     */
    public function notifyReturnRequestApproved(string $ownerPhone, string $returnId): array
    {
        $message = "✅ *Return Request Approved* — 22UniMart\n\n"
            . "Return ID: {$returnId}\n\n"
            . "The supplier has approved your return request. "
            . "A credit note will be processed shortly.";

        return $this->sendMessage($ownerPhone, $message);
    }

    // ── Supplier notifications ─────────────────────────────────────────────────

    /**
     * Send a portal invitation to a new supplier.
     */
    public function inviteSupplier(string $supplierPhone, string $supplierName, string $inviteLink): array
    {
        $message = "👋 Hello {$supplierName},\n\n"
            . "You have been invited to join the *22UniMart Supplier Portal*.\n\n"
            . "Click the link below to get started:\n"
            . "{$inviteLink}\n\n"
            . "Please log in and change your password on first access.";

        return $this->sendMessage($supplierPhone, $message);
    }

    /**
     * Notify a supplier that a new purchase order has been raised for them.
     */
    public function notifyPurchaseOrderRequest(string $supplierPhone, string $poNumber, string $details): array
    {
        $message = "📦 *New Purchase Order Request* — 22UniMart\n\n"
            . "PO Number: {$poNumber}\n"
            . "Details: {$details}\n\n"
            . "Please log in to the Supplier Portal to review and confirm this order.";

        return $this->sendMessage($supplierPhone, $message);
    }

    /**
     * Notify a supplier that a return request has been raised against them.
     */
    public function notifyReturnRequest(string $supplierPhone, string $returnId, string $reason): array
    {
        $message = "↩️ *New Return Request* — 22UniMart\n\n"
            . "Return ID: {$returnId}\n"
            . "Reason: {$reason}\n\n"
            . "Please log in to the Supplier Portal to review this return request.";

        return $this->sendMessage($supplierPhone, $message);
    }
}
