<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DirectPurchaseLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'direct_purchase_id',
        'item_id',
        'quantity',
        'uom',
        'amount_paid',
        'unit_price',
        'selling_price',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'amount_paid' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
    ];

    public function directPurchase()
    {
        return $this->belongsTo(DirectPurchase::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
