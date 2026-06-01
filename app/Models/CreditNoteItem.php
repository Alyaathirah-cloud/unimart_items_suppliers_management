<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditNoteItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'credit_note_id',
        'return_request_line_id',
        'item_id',
        'quantity',
        'uom',
        'unit_price',
        'subtotal',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function creditNote()
    {
        return $this->belongsTo(CreditNote::class);
    }

    public function returnRequestLine()
    {
        return $this->belongsTo(ReturnRequestLine::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
