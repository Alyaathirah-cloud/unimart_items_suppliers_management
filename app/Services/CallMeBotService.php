<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Setting;

class CallMeBotService
{
    protected $phone;
    protected $apikey;

    public function __construct()
    {
        $this->phone = trim((string) config('services.callmebot.phone'));
        $this->apikey = trim((string) config('services.callmebot.apikey'));
    }

    public function isConfigured(): bool
    {
        return $this->phone !== '' && $this->apikey !== '';
    }
    protected function normalizePhoneNumber(string $phone): string
    {
        $p = preg_replace('/[^0-9+]/', '', (string)$phone);
        // Remove leading + and zeroes, convert common local formats
        $p = ltrim($p, '+');
        if (preg_match('/^0(1\d{8,9})$/', $p, $m)) {
            // Malaysian local number like 01123456789 -> 601123456789
            $p = '6' . $p;
        }
        // Ensure international without plus
        return $p;
    }

    protected function request(array $params)
    {
        if (!$this->isConfigured()) {
            throw new \Exception('CallMeBot is not configured.');
        }

        $endpoint = 'https://api.callmebot.com/whatsapp.php';
        $response = Http::get($endpoint, $params);

        if ($response->failed()) {
            Log::channel('whatsapp_alerts')->error('CallMeBot request failed', [
                'params' => $params,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \Exception('CallMeBot request failed: ' . $response->body());
        }

        $result = trim((string) $response->body());
        if (str_contains(strtolower($result), 'error')) {
            Log::channel('whatsapp_alerts')->error('CallMeBot error response', ['result' => $result]);
            throw new \Exception('CallMeBot error: ' . $result);
        }

        Log::channel('whatsapp_alerts')->info('CallMeBot request success', ['params' => $params, 'result' => $result]);
        return $result;
    }

    public function sendToNumber(string $recipientNumber, string $message): string
    {
        $number = $this->normalizePhoneNumber($recipientNumber ?: $this->phone);
        $params = [
            'phone' => $number,
            'text' => $message,
            'apikey' => $this->apikey,
        ];

        return $this->request($params);
    }

    public function sendToOwner(string $message): string
    {
        $owner = config('services.callmebot.owner') ?: null;
        if ($owner) {
            return $this->sendToNumber($owner, $message);
        }
        // fallback to configured phone
        return $this->sendToNumber($this->phone, $message);
    }

    public function sendToSupplier(string $supplierPhone, string $message): string
    {
        return $this->sendToNumber($supplierPhone, $message);
    }

    // Domain helpers
    public function sendLowStockAlert($item, $ownerNumber)
    {
        $rop = $item->reorder_point ?? Setting::get('low_stock_threshold', 0);
        $msg = "⚠ LOW STOCK ALERT\n\nItem: {$item->name}\nCurrent Stock: {$item->quantity}\nReorder Point: {$item->reorder_point}\n\nPlease create a Purchase Order to replenish stock.";
        return $this->sendToNumber($ownerNumber, $msg);
    }

    public function sendExpiryAlert($item, $ownerNumber, $daysRemaining = null)
    {
        if ($item->expiry_date && $item->expiry_date->isPast()) {
            $msg = "🚨 EXPIRED ITEM ALERT\n\nItem: {$item->name}\nExpired On: {$item->expiry_date->format('Y-m-d')}\n\nPlease create a Return Request if applicable.";
        } else {
            $days = $daysRemaining ?? $item->expiry_date->diffInDays(now());
            $msg = "⏰ EXPIRY REMINDER\n\nItem: {$item->name}\nExpiry Date: {$item->expiry_date->format('Y-m-d')}\nDays Remaining: {$days}\n\nPlease inspect and take appropriate action.";
        }

        return $this->sendToNumber($ownerNumber, $msg);
    }

    public function sendPurchaseOrderNotification($poNumber, $itemName, $quantity, $supplierPhone, $portalLink)
    {
        $msg = "📦 NEW PURCHASE ORDER\n\nPO Number: {$poNumber}\nItem: {$itemName}\nQuantity: {$quantity}\n\nPlease review and respond through the Supplier Portal.\n\nPortal:\n{$portalLink}";
        return $this->sendToSupplier($supplierPhone, $msg);
    }

    public function sendPurchaseOrderStatusToOwner($status, $poNumber, $supplierName, $ownerPhone, $deliveryDate = null)
    {
        if ($status === 'approved') {
            $msg = "✅ PURCHASE ORDER APPROVED\n\nPO Number: {$poNumber}\nSupplier: {$supplierName}\n\nExpected Delivery:\n" . ($deliveryDate ? $deliveryDate->format('Y-m-d') : 'TBD');
        } elseif ($status === 'rejected') {
            $msg = "❌ PURCHASE ORDER REJECTED\n\nPO Number: {$poNumber}\nSupplier: {$supplierName}\n\nPlease review supplier comments.";
        } else {
            $msg = "Purchase order status update: {$status} for {$poNumber}";
        }

        return $this->sendToOwner($msg);
    }

    public function sendReturnRequestNotificationToSupplier($data, $supplierPhone, $portalLink)
    {
        $msg = "↩ RETURN REQUEST CREATED\n\nItem: {$data['item_name']}\nQuantity: {$data['quantity']}\nReason: {$data['reason']}\n\nPlease review through the Supplier Portal.\n\nPortal:\n{$portalLink}";
        return $this->sendToSupplier($supplierPhone, $msg);
    }

    public function sendReturnRequestStatusToOwner($status, $itemName, $quantity, $ownerPhone)
    {
        if ($status === 'approved') {
            $msg = "✅ RETURN REQUEST APPROVED\n\nItem: {$itemName}\nQuantity: {$quantity}\n\nCredit Note will be processed.";
        } else {
            $msg = "❌ RETURN REQUEST REJECTED\n\nItem: {$itemName}\n\nPlease review supplier comments.";
        }
        return $this->sendToOwner($msg);
    }

    public function sendSupplierInvite($supplierPhone, $data)
    {
        $msg = "Welcome to 22UniMart Supplier Portal.\n\nEmail: {$data['email']}\nTemporary Password: {$data['password']}\n\nPortal: {$data['portal_url']}\n\nPlease log in and change your password after first login.";
        return $this->sendToSupplier($supplierPhone, $msg);
    }
}
