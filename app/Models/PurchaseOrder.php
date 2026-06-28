<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'po_number',
        'item_id',       // kept for backward compat, nullable
        'quantity',      // kept for backward compat, nullable
        'supplier_id',
        'order_date',
        'status',
        'notes',
        'unit_price',    // kept for backward compat
        'total_amount',
        'final_amount',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'order_date'   => 'datetime',
        'total_amount' => 'decimal:2',
        'final_amount' => 'decimal:2',
    ];

    // ── Relationships ────────────────────────────────────────────────────────

    /** Primary multi-item relationship (new) */
    public function orderItems()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    /** Legacy single-item relationship (backward compat) */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function delivery()
    {
        return $this->hasOne(Delivery::class);
    }

    public function creditNote()
    {
        return $this->belongsTo(CreditNote::class, 'credit_note_id');
    }

    public function returnRequests()
    {
        return $this->hasMany(ReturnRequest::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class, 'purchase_order_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ── Computed helpers ─────────────────────────────────────────────────────

    /**
     * Compute total from line items (falls back to stored total_amount).
     */
    public function getComputedTotal(): float
    {
        if ($this->orderItems()->exists()) {
            return (float) $this->orderItems()->sum('subtotal');
        }
        return (float) ($this->total_amount ?? 0);
    }
}
