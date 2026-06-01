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
    ];

    protected $casts = [
        'portal_enabled' => 'boolean',
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
}

