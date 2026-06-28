@extends('layouts.staff')

@section('title', 'Item Details – 22UniMart')

@push('styles')
<style>
    .page-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 28px; }
    .page-title { font-size: 1.7rem; font-weight: 800; color: #0f2044; }
    .detail-card { background: #fff; border-radius: 14px; padding: 32px; box-shadow: 0 1px 8px rgba(15,32,68,.07); margin-bottom: 20px; }
    .detail-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px; }
    .detail-label { font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #9daec5; margin-bottom: 6px; }
    .detail-value { font-size: 1.05rem; font-weight: 600; color: #1a2744; }
    .badge-status { display: inline-flex; align-items: center; padding: 4px 12px; border-radius: 6px; font-size: .8rem; font-weight: 700; }
    .badge-ok { background: #e8f8f0; color: #1d8348; }
    .badge-low { background: #fef3e2; color: #d4870a; }
    .badge-out { background: #fdedec; color: #c0392b; }
    .alert-box { border-radius: 10px; padding: 14px 18px; font-size: .88rem; margin-bottom: 16px; }
    .alert-danger { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }
    .alert-warning { background: #fffbeb; border: 1px solid #fde68a; color: #92400e; }
    .btn { display: inline-flex; align-items: center; gap: 6px; padding: 10px 20px; border-radius: 8px; font-size: .88rem; font-weight: 600; cursor: pointer; border: 1px solid transparent; transition: all .15s; font-family: 'Inter', sans-serif; text-decoration: none; }
    .btn-primary { background: #0f2044; color: #fff; }
    .btn-primary:hover { background: #182e5e; }
    .btn-secondary { background: #f4f6fb; color: #3a4d6a; border-color: #d1dce8; }
    .btn-secondary:hover { background: #e8eef5; }
</style>
@endpush

@section('breadcrumbs')
    <a href="{{ route('staff.inventory.index') }}">Inventory</a> › <span style="color:#0f2044;">{{ $item->name }}</span>
@endsection

@section('content')
<div class="page-header">
    <div class="page-title">{{ $item->name }}</div>
    <div style="display:flex;gap:10px;">
        <a href="{{ route('staff.inventory.edit', $item) }}" class="btn btn-primary">✏ Edit Item</a>
        <a href="{{ route('staff.inventory.index') }}" class="btn btn-secondary">← Back</a>
    </div>
</div>

@if($item->expiry_date && $item->expiry_date < now())
    <div class="alert-box alert-danger">
        <strong>⚠️ This item has expired!</strong> Expiry date: {{ $item->expiry_date->format('Y-m-d') }}
    </div>
@elseif($item->expiry_date && $item->expiry_date <= now()->addDays(7))
    <div class="alert-box alert-warning">
        <strong>⚠️ This item is expiring soon!</strong> Expiry date: {{ $item->expiry_date->format('Y-m-d') }}
    </div>
@endif

@if($item->quantity <= $item->reorder_point)
    <div class="alert-box alert-warning">
        <strong>⚠️ Low stock alert!</strong> Current quantity ({{ $item->quantity }}) is at or below reorder point ({{ $item->reorder_point }}).
    </div>
@endif

<div class="detail-card">
    <div class="detail-grid">
        <div>
            <div class="detail-label">Item Name</div>
            <div class="detail-value">{{ $item->name }}</div>
        </div>
        <div>
            <div class="detail-label">Category</div>
            <div class="detail-value">{{ $item->category ?? '—' }}</div>
        </div>
        <div>
            <div class="detail-label">Quantity</div>
            <div class="detail-value">{{ $item->quantity }}</div>
        </div>
        <div>
            <div class="detail-label">Reorder Point</div>
            <div class="detail-value">{{ $item->reorder_point }}</div>
        </div>
        <div>
            <div class="detail-label">Unit Cost Price</div>
            <div class="detail-value">RM {{ number_format($item->unit_price, 2) }}</div>
        </div>
        <div>
            <div class="detail-label">Selling Price</div>
            <div class="detail-value" style="color:#27ae60;">RM {{ number_format($item->selling_price, 2) }}</div>
        </div>
        <div>
            <div class="detail-label">Expiry Date</div>
            <div class="detail-value">{{ $item->expiry_date ? $item->expiry_date->format('Y-m-d') : '—' }}</div>
        </div>
        <div>
            <div class="detail-label">Supplier</div>
            <div class="detail-value">{{ optional($item->supplier)->name ?? '—' }}</div>
        </div>
        <div>
            <div class="detail-label">Status</div>
            <div class="detail-value">
                @if($item->isOutOfStock())
                    <span class="badge-status badge-out">Out of Stock</span>
                @elseif($item->isLowStock())
                    <span class="badge-status badge-low">Low Stock</span>
                @else
                    <span class="badge-status badge-ok">Normal</span>
                @endif
            </div>
        </div>
        <div>
            <div class="detail-label">Created At</div>
            <div class="detail-value">{{ $item->created_at->format('Y-m-d H:i') }}</div>
        </div>
    </div>
</div>
@endsection
