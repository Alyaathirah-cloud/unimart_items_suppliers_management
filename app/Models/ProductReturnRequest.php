<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductReturnRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'quantity',
        'reason',
        'status',
        'admin_note'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
