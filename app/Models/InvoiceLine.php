<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'item_id',
        'uom',
        'quantity',
        'invoice_line_total',
        'unit_price',
        'selling_price',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'invoice_line_total' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function returnRequestLines()
    {
        return $this->hasMany(ReturnRequestLine::class, 'invoice_line_id');
    }
}
