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
        'paid_date',
        'confirmed_by',
        'status',
        'source',
        'sold_estimates',
    ];

    // Valid statuses for the new workflow
    const STATUS_UNPAID  = 'Unpaid';
    const STATUS_SETTLED = 'Settled';
    const STATUS_PAID    = 'Paid';
    const STATUS_PENDING_PAYMENT = 'Pending Payment'; // Display alias


    protected $casts = [
        'invoice_date'     => 'date',
        'payment_due_date' => 'date',
        'paid_date'        => 'date',
        'total_amount'     => 'decimal:2',
        'sold_estimates'   => 'array',
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

    public function auditLogs()
    {
        return $this->hasMany(InvoiceAuditLog::class);
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
     * Compute the display status using the new Unpaid → Settled → Paid flow.
     *  - Paid     → manually locked by supplier after receiving payment
     *  - Settled  → return request linked to this invoice was approved
     *  - Unpaid   → default after invoice is created
     *
     * Legacy statuses (Active, Partially Credited, Overdue) are mapped for
     * backward compatibility.
     */
    public function computeStatus(): string
    {
        $s = $this->status;

        // New canonical statuses
        if ($s === self::STATUS_PAID   || $s === 'paid')    return 'Paid';
        if ($s === self::STATUS_SETTLED || $s === 'settled') return 'Settled';
        if ($s === self::STATUS_UNPAID  || $s === 'unpaid')  return 'Unpaid';

        // Legacy backward-compat mapping
        if ($s === 'Active' || $s === 'active')  return 'Unpaid';
        if ($s === 'Partially Credited')          return 'Settled';
        if ($s === 'Overdue')                     return 'Unpaid';

        return 'Unpaid';
    }

    /**
     * Compute the display status for the UI.
     * Both Unpaid and Settled map to "Pending Payment".
     */
    public function getDisplayStatus(): string
    {
        $internalStatus = $this->computeStatus();
        if ($internalStatus === 'Unpaid' || $internalStatus === 'Settled') {
            return self::STATUS_PENDING_PAYMENT;
        }
        return $internalStatus;
    }

    /**
     * CSS colour key for the computed status.
     */
    public function statusColor(): string
    {
        return match ($this->getDisplayStatus()) {
            'Paid' => 'green',
            self::STATUS_PENDING_PAYMENT => 'orange',
            default => 'gray',
        };
    }

    public function isPaid(): bool
    {
        return in_array($this->status, ['Paid', 'paid']);
    }

    public function isSettled(): bool
    {
        return in_array($this->status, ['Settled', 'settled', 'Partially Credited']);
    }

    public function isLocked(): bool
    {
        return $this->isPaid();
    }
}
