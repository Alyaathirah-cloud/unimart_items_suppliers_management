<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'purchase_order_id',
        'supplier_id',
        'invoice_date',
        'total_amount',
        'payment_due_date',
        'status',
        'source',
    ];

    protected $casts = [
        'invoice_date'     => 'date',
        'payment_due_date' => 'date',
        'total_amount'     => 'decimal:2',
    ];

    // ── Relationships ────────────────────────────────────────────────────────

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function lines()
    {
        return $this->hasMany(InvoiceLine::class);
    }

    public function creditNotes()
    {
        return $this->hasMany(CreditNote::class, 'invoice_id');
    }

    public function returnRequests()
    {
        return $this->hasMany(ReturnRequest::class, 'invoice_id');
    }

    // ── Financial helpers ────────────────────────────────────────────────────

    /**
     * Sum of all approved credit notes linked to this invoice.
     */
    public function getTotalCreditDeduction(): float
    {
        return (float) $this->creditNotes()->sum('amount');
    }

    /**
     * Net payable = invoice total minus all approved credit deductions.
     */
    public function getNetPayableAmount(): float
    {
        return max(0, (float)$this->total_amount - $this->getTotalCreditDeduction());
    }

    // ── Status helpers ───────────────────────────────────────────────────────

    /**
     * Compute the dynamic display status:
     *  - Paid             → locked and paid
     *  - Settled          → partially or fully credited via return request
     *  - Overdue          → past due date and not paid
     *  - Pending          → default active state
     */
    public function computeStatus(): string
    {
        if ($this->status === 'paid') {
            return 'paid';
        }

        if ($this->payment_due_date && Carbon::today()->gt($this->payment_due_date)) {
            return 'Overdue';
        }

        if ($this->getTotalCreditDeduction() > 0 || $this->status === 'Partially Credited') {
            return 'Partially Credited';
        }

        return 'Active';
    }

    /**
     * CSS class / colour key for the computed status.
     */
    public function statusColor(): string
    {
        return match ($this->computeStatus()) {
            'Active'             => 'blue',
            'Partially Credited' => 'purple',
            'Overdue'            => 'red',
            'paid'               => 'green',
            default              => 'gray',
        };
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isLocked(): bool
    {
        return $this->isPaid();
    }
}
