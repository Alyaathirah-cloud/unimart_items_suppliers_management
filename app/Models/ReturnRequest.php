<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Invoice;
use App\Models\User;

class ReturnRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'return_number',
        'invoice_id',
        'invoice_number',
        'notes',
        'supplier_id',
        'purchase_order_id',
        'status',
        'request_date',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'request_date' => 'date',
    ];

    // ── Relationships ────────────────────────────────────────────────────────

    /**
     * All return request lines (multi-item support).
     */
    public function lines()
    {
        return $this->hasMany(ReturnRequestLine::class);
    }

    /**
     * Only lines that were (at least partially) approved by the supplier.
     */
    public function approvedLines()
    {
        return $this->hasMany(ReturnRequestLine::class)
                    ->where('approved_qty', '>', 0);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function creditNote()
    {
        return $this->hasOne(CreditNote::class, 'return_id');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    // ── Financial helpers ────────────────────────────────────────────────────

    /**
     * Total value of all requested return lines (what owner is claiming).
     */
    public function getRequestedTotal(): float
    {
        return (float) $this->lines()->sum('subtotal');
    }

    /**
     * Total value of approved portions only (what supplier approved).
     */
    public function getApprovedTotal(): float
    {
        $lines = $this->lines()->get();
        return $lines->sum(fn($l) => $l->getApprovedSubtotal());
    }

    /**
     * Gross loss = what owner paid for but cannot recover.
     */
    public function getGrossLoss(): float
    {
        return max(0, $this->getRequestedTotal() - $this->getApprovedTotal());
    }

    /**
     * Legacy helper — kept for compatibility with credit note views.
     */
    public function getTotal(): float
    {
        return $this->getRequestedTotal();
    }

    /**
     * Credit amount derived entirely from line items.
     */
    public function getCreditAmountAttribute()
    {
        return $this->lines()->sum('subtotal');
    }
}
