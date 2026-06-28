<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnRequestLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'return_request_id',
        'invoice_line_id',
        'item_id',
        'quantity',
        'approved_qty',
        'approved_subtotal',
        'uom',
        'reason',
        'damage_remark',
        'unit_price',
        'subtotal',
    ];

    protected $casts = [
        'quantity'          => 'integer',
        'approved_qty'      => 'integer',
        'unit_price'        => 'decimal:2',
        'subtotal'          => 'decimal:2',
        'approved_subtotal' => 'decimal:2',
    ];

    // ── Relationships ────────────────────────────────────────────────────────

    public function returnRequest()
    {
        return $this->belongsTo(ReturnRequest::class);
    }

    public function invoiceLine()
    {
        return $this->belongsTo(InvoiceLine::class, 'invoice_line_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    /**
     * The value of the approved portion of this line.
     * Falls back to full subtotal if not yet reviewed.
     */
    public function getApprovedSubtotal(): float
    {
        if (!is_null($this->approved_qty)) {
            return round((float)$this->approved_qty * (float)$this->unit_price, 2);
        }
        return 0.0;
    }

    public function getRequestedSubtotal(): float
    {
        return (float) $this->subtotal;
    }

    public function getGrossLoss(): float
    {
        return max(0, $this->getRequestedSubtotal() - $this->getApprovedSubtotal());
    }

    /**
     * True if this line was at least partially approved.
     */
    public function isApproved(): bool
    {
        return !is_null($this->approved_qty) && $this->approved_qty > 0;
    }
}
