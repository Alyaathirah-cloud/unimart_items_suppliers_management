<?php

namespace App\Models;

use App\Mail\AppNotificationMail;
use App\Mail\PurchaseOrderCreatedToSupplier;
use App\Mail\PurchaseOrderDeliveryToOwner;
use App\Mail\PurchaseOrderStatusToOwner;
use App\Mail\ReturnRequestCreatedToSupplier;
use App\Mail\ReturnRequestStatusToOwner;
use App\Mail\SupplierInvitationMail;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'message',
        'data',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function markAsRead()
    {
        $this->update(['read_at' => now()]);
    }

    public function isRead()
    {
        return !is_null($this->read_at);
    }

    public static function send(User $user, string $type, string $message, array $data = [])
    {
        $notification = static::create([
            'user_id' => $user->id,
            'type' => $type,
            'message' => $message,
            'data' => $data,
        ]);

        if ($user->email) {
            try {
                $mailable = static::buildMailable($type, $message, $data);
                if ($mailable) {
                    Mail::to($user->email)->queue($mailable);
                }
            } catch (\Throwable $e) {
                // Email failure should not block app behavior
            }
        }

        // Send Telegram notification if the user is a supplier and has connected Telegram
        $supplier = \App\Models\Supplier::where('user_id', $user->id)->first();
        if ($supplier && !empty($supplier->telegram_chat_id)) {
            try {
                $telegramMessage = static::buildTelegramMessage($type, $message, $data);
                if ($telegramMessage) {
                    (new \App\Services\TelegramService())->send($telegramMessage, $supplier->telegram_chat_id);
                }
            } catch (\Throwable $e) {
                // Telegram failure should not block app behavior
            }
        }

        return $notification;
    }

    public static function sendToOwners(string $type, string $message, array $data = [])
    {
        $owners = User::where('role', 'owner')->get();
        foreach ($owners as $owner) {
            static::send($owner, $type, $message, $data);
        }
    }

    protected static function buildMailable(string $type, string $message, array $data)
    {
        switch ($type) {
            case 'purchase_order_created':
                return new PurchaseOrderCreatedToSupplier(
                    $data['po_number'] ?? 'N/A',
                    $data['item_name'] ?? 'Unknown',
                    (int) ($data['quantity'] ?? 0),
                    $data['login_url'] ?? route('login')
                );
            case 'purchase_order_approved':
            case 'purchase_order_rejected':
                return new PurchaseOrderStatusToOwner(
                    $data['po_number'] ?? 'N/A',
                    $data['status'] ?? ucfirst(str_replace('purchase_order_', '', $type)),
                    $data['supplier_name'] ?? 'Supplier'
                );
            case 'purchase_order_delivered':
                return new PurchaseOrderDeliveryToOwner(
                    $data['po_number'] ?? 'N/A',
                    $data['delivery_date'] ?? 'N/A',
                    $data['delivery_time'] ?? 'N/A'
                );
            case 'return_request_created':
                return new ReturnRequestCreatedToSupplier(
                    $data['return_number'] ?? 'N/A',
                    $data['item_name'] ?? 'Unknown',
                    $data['reason'] ?? 'Not specified'
                );
            case 'return_request_approved':
            case 'return_request_rejected':
                return new ReturnRequestStatusToOwner(
                    $data['return_number'] ?? 'N/A',
                    $data['status'] ?? ucfirst(str_replace('return_request_', '', $type))
                );
            case 'supplier_invited':
                return new SupplierInvitationMail(
                    $data['supplier_name'] ?? 'Supplier',
                    $data['login_url'] ?? route('login')
                );
            default:
                return new AppNotificationMail($message);
        }
    }

    protected static function buildTelegramMessage(string $type, string $message, array $data)
    {
        switch ($type) {
            case 'purchase_order_created':
                return "📦 PURCHASE ORDER CREATED\n\n" .
                       "PO ID: #" . ($data['po_number'] ?? 'N/A') . "\n" .
                       "Item: " . ($data['item_name'] ?? 'Multiple Items') . "\n" .
                       "Quantity: " . ($data['quantity'] ?? 0) . "\n\n" .
                       "Please log in to review.";
            case 'purchase_order_updated':
                return "✏️ PURCHASE ORDER UPDATED\n\n" .
                       "PO ID: #" . ($data['po_number'] ?? 'N/A') . "\n\n" .
                       "Note: Changes have been made and review is required.";
            case 'po_approved':
            case 'purchase_order_approved':
                return "✅ PURCHASE ORDER APPROVED\n\n" .
                       "PO ID: #" . ($data['po_number'] ?? 'N/A');
            case 'po_rejected':
            case 'purchase_order_rejected':
                return "❌ PURCHASE ORDER REJECTED\n\n" .
                       "PO ID: #" . ($data['po_number'] ?? 'N/A') . "\n" .
                       "Reason: " . ($data['reason'] ?? 'Not specified');
            case 'po_received':
            case 'purchase_order_delivered':
                return "📥 PURCHASE ORDER RECEIVED\n\n" .
                       "PO ID: #" . ($data['po_number'] ?? 'N/A');
            case 'return_request_created':
                return "↩️ RETURN REQUEST CREATED\n\n" .
                       "Return ID: #" . ($data['return_number'] ?? 'N/A') . "\n" .
                       "Item: " . ($data['item_name'] ?? 'N/A') . "\n" .
                       "Reason: " . ($data['reason'] ?? 'N/A');
            case 'return_request_approved':
                return "✅ RETURN REQUEST APPROVED\n\n" .
                       "Return ID: #" . ($data['return_number'] ?? 'N/A');
            case 'return_request_rejected':
                return "❌ RETURN REQUEST REJECTED\n\n" .
                       "Return ID: #" . ($data['return_number'] ?? 'N/A');
            default:
                return "🔔 NOTIFICATION\n\n" . $message;
        }
    }
}
