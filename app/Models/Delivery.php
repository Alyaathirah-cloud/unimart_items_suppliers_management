<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_id',
        'delivery_date',
        'delivery_time',
        'notes',
    ];

    protected $casts = [
        'delivery_date' => 'date',
        'delivery_time' => 'datetime:H:i',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }
}
