<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'contact_email',
        'contact_phone',
        'contact_person',
        'company_registration_number',
        'tax_number',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'postal_code',
        'country',
        'bank_name',
        'bank_account',
        'portal_enabled',
        'portal_link',
        'temporary_password',
        'invite_email_status',
        'invite_whatsapp_status',
        'user_id',
        'accepts_returns',
        'telegram_chat_id',
        'telegram_connection_token',
        'telegram_username',
        'telegram_connected',
        'telegram_connected_at',
    ];

    protected $casts = [
        'portal_enabled'      => 'boolean',
        'accepts_returns'     => 'boolean',
        'telegram_connected'  => 'boolean',
        'telegram_connected_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(Item::class);
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function returnRequests()
    {
        return $this->hasMany(ReturnRequest::class);
    }

    public function creditNotes()
    {
        return $this->hasMany(CreditNote::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function generateTelegramToken()
    {
        // Always regenerate a fresh token so old links can't be reused
        $this->telegram_connection_token = \Illuminate\Support\Str::random(32);
        $this->save();
        return $this->telegram_connection_token;
    }

    /**
     * Return true if the supplier has a verified Telegram connection.
     */
    public function isTelegramConnected(): bool
    {
        return $this->telegram_connected && !empty($this->telegram_chat_id);
    }

    /**
     * Build the Telegram deep-link for connecting this supplier's account.
     */
    public function telegramConnectUrl(): string
    {
        $botUsername = config('services.telegram.bot_username', env('TELEGRAM_BOT_USERNAME'));
        $token = $this->generateTelegramToken();
        return "https://t.me/{$botUsername}?start={$token}";
    }
}

