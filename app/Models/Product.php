<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';

    protected $fillable = [
        'name',
        'expiry_date',
        'stock',
        'alert_sent',
        'min_stock',
        'low_stock_alert_sent'
    ];

    protected $casts = [
        'alert_sent' => 'boolean',
        'low_stock_alert_sent' => 'boolean',
    ];
}