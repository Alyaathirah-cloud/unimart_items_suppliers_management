<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'quantity',
        'expiry_date',
        'supplier_id',
        'reorder_point',
        'unit_price',
        'selling_price',
        'source_type',
        'markup_percentage',
        'uom',
        'pieces_per_uom',
        'is_critical',
        'reorder_frequency',
        'last_purchase_date',
        'is_damaged',
        'damage_reason',
        'damaged_quantity',
    ];

    protected $casts = [
        'expiry_date'       => 'datetime',
        'unit_price'        => 'decimal:2',
        'selling_price'     => 'decimal:2',
        'markup_percentage' => 'decimal:2',
        'is_critical'       => 'boolean',
        'is_damaged'        => 'boolean',
        'last_purchase_date'=> 'date',
    ];

    // ── Relationships ────────────────────────────────────────────────────────

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function returnRequests()
    {
        return $this->hasMany(ReturnRequest::class);
    }

    // ── Status checks ────────────────────────────────────────────────────────

    public function isLowStock(): bool
    {
        return $this->quantity <= $this->reorder_point;
    }

    public function isNearExpiry($warningDays = null): bool
    {
        $warningDays = $warningDays ?? config('inventory.expiry_warning_days', 7);
        return $this->expiry_date
            && $this->expiry_date->gte(now()->startOfDay())
            && $this->expiry_date->lte(now()->addDays($warningDays));
    }

    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->lt(now());
    }

    public function isDamaged(): bool
    {
        return (bool) $this->is_damaged;
    }

    public function getSellingPriceAttribute($value)
    {
        $markup = (float) ($this->attributes['markup_percentage'] ?? 0);
        $unitPrice = (float) ($this->attributes['unit_price'] ?? 0);

        return round($unitPrice * (1 + ($markup / 100)), 2);
    }

    /**
     * Returns true if this item has a pending return request line.
     */
    public function hasPendingReturn(): bool
    {
        return ReturnRequestLine::where('item_id', $this->id)
            ->whereHas('returnRequest', fn($q) => $q->where('status', 'Pending'))
            ->exists();
    }

    // ── Label / Class helpers ─────────────────────────────────────────────────

    public function statusLabel(): string
    {
        $parts = [];
        if ($this->isExpired())    $parts[] = 'Expired';
        if ($this->isNearExpiry() && !$this->isExpired()) $parts[] = 'Expiring Soon';
        if ($this->isLowStock())   $parts[] = 'Low Stock';
        if ($this->isDamaged())    $parts[] = 'Damaged';
        return $parts ? implode(' / ', $parts) : 'OK';
    }

    public function statusClass(): string
    {
        if ($this->isExpired())    return 'danger';
        if ($this->isNearExpiry()) return 'warning text-dark';
        if ($this->isLowStock())   return 'warning';
        return 'success';
    }

    // ── Reorder helpers ───────────────────────────────────────────────────────

    public function isReorderDue(): bool
    {
        if (!$this->is_critical || !$this->reorder_frequency || !$this->last_purchase_date) {
            return false;
        }
        $lastPurchase = \Carbon\Carbon::parse($this->last_purchase_date);
        if ($this->reorder_frequency === 'weekly') {
            return $lastPurchase->addDays(7)->lte(now());
        } elseif ($this->reorder_frequency === 'monthly') {
            return $lastPurchase->addMonths(1)->lte(now());
        }
        return false;
    }

    public function getReorderReminderText(): ?string
    {
        if (!$this->is_critical) return null;
        if (!$this->reorder_frequency || !$this->last_purchase_date) {
            return 'Critical item (no reorder schedule set)';
        }
        if ($this->isReorderDue()) {
            return 'REORDER DUE: ' . $this->reorder_frequency . ' cycle overdue';
        }
        $nextReorderDate = \Carbon\Carbon::parse($this->last_purchase_date);
        if ($this->reorder_frequency === 'weekly') $nextReorderDate->addDays(7);
        elseif ($this->reorder_frequency === 'monthly') $nextReorderDate->addMonths(1);
        $daysUntil = $nextReorderDate->diffInDays(now());
        if ($daysUntil <= 2) {
            return 'Reorder due in ' . $daysUntil . ' day' . ($daysUntil > 1 ? 's' : '');
        }
        return null;
    }
}
