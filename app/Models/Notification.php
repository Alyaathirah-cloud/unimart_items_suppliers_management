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

        return $notification;
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
}
