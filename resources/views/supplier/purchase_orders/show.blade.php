@extends('layouts.supplier')

@section('title', 'PO Details - ' . $order->po_number)

@push('styles')
<style>
    .po-detail-card {
        background-color: #ffffff;
        border-radius: 12px;
        padding: 32px;
        color: #0f2044;
        max-width: 900px;
        margin: 0 auto;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        font-family: 'Inter', sans-serif;
        border: 1px solid #eef2f7;
    }
    .po-back-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #f0f4f8;
        border: 1px solid #d1dce8;
        color: #2980b9;
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 0.85rem;
        font-weight: 700;
        text-decoration: none;
        margin-bottom: 24px;
        transition: background 0.2s;
    }
    .po-back-btn:hover {
        background: #e1e8f0;
    }
    .po-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 32px;
    }
    .po-title {
        font-size: 1.8rem;
        font-weight: 800;
        margin-bottom: 8px;
    }
    .po-subtitle {
        color: #7a8fa8;
        font-size: 0.9rem;
    }
    .po-status {
        background: #e8f8f0;
        color: #1d8348;
        padding: 6px 16px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .po-status.pending { background: #fef3e2; color: #d4870a; }
    .po-status.rejected { background: #fdedec; color: #c0392b; }
    
    .po-meta-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
        margin-bottom: 40px;
    }
    .po-meta-label {
        color: #7a8fa8;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 700;
        margin-bottom: 6px;
    }
    .po-meta-value {
        font-size: 0.95rem;
        font-weight: 600;
        line-height: 1.4;
    }
    .po-meta-subvalue {
        color: #7a8fa8;
        font-size: 0.8rem;
    }

    .po-items-title {
        color: #7a8fa8;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 700;
        margin-bottom: 16px;
    }
    .po-table {
        width: 100%;
        border-collapse: collapse;
    }
    .po-table th {
        text-align: left;
        color: #7a8fa8;
        font-size: 0.85rem;
        font-weight: 700;
        padding-bottom: 12px;
        border-bottom: 2px solid #eef2f7;
    }
    .po-table th.right { text-align: right; }
    .po-table td {
        padding: 16px 0;
        border-bottom: 1px solid #eef2f7;
        vertical-align: top;
    }
    .po-table tr:last-child td {
        border-bottom: 2px solid #eef2f7;
    }
    .po-item-name {
        font-weight: 700;
        font-size: 0.95rem;
        margin-bottom: 4px;
        color: #0f2044;
    }
    .po-item-sku {
        color: #7a8fa8;
        font-size: 0.75rem;
    }
    .po-item-val {
        font-size: 0.95rem;
        font-weight: 600;
        color: #3a4d6a;
    }
    .po-total-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 24px 0;
    }
    .po-total-label {
        font-weight: 700;
        font-size: 1.1rem;
        color: #0f2044;
    }
    .po-total-value {
        font-weight: 800;
        font-size: 1.4rem;
        color: #0f2044;
    }
</style>
@endpush

@section('content')
<div style="padding: 24px;">
    <div class="po-detail-card">
        <a href="{{ route('supplier.purchase-orders.index') }}" class="po-back-btn">
            ← Back to purchase orders
        </a>

        <div class="po-header">
            <div>
                <div class="po-title">{{ $order->po_number }}</div>
                <div class="po-subtitle">
                    Ordered {{ $order->order_date ? $order->order_date->format('M d, Y') : 'N/A' }} &middot; {{ optional($order->supplier)->name }}
                </div>
            </div>
            <div>
                @php
                    $status = $order->status;
                    $statusClass = strtolower($status);
                @endphp
                <div class="po-status {{ $statusClass }}">
                    @if($status === 'Approved' || $status === 'Received') ✓
                    @elseif($status === 'Rejected') ✕
                    @endif
                    {{ $status }}
                </div>
            </div>
        </div>

        <div class="po-meta-grid">
            <div>
                <div class="po-meta-label">Delivery Schedule</div>
                @if($order->delivery && $order->delivery->delivery_date)
                    <div class="po-meta-value">{{ $order->delivery->delivery_date->format('M d, Y') }}</div>
                    <div class="po-meta-subvalue">{{ $order->delivery->delivery_time ? \Carbon\Carbon::parse($order->delivery->delivery_time)->format('g:i A') : '' }}</div>
                @else
                    <div class="po-meta-value">Not scheduled</div>
                @endif
            </div>
            <div>
                <div class="po-meta-label">Created By</div>
                <div class="po-meta-value">{{ optional($order->user)->name ?? 'Owner' }} ({{ ucfirst(optional($order->user)->role ?? 'Owner') }})</div>
                <div class="po-meta-subvalue">{{ $order->created_at ? $order->created_at->format('M d, Y · g:i A') : '' }}</div>
            </div>
        </div>

        <div class="po-items-title">Order Items</div>
        <table class="po-table">
            <thead>
                <tr>
                    <th>Item description</th>
                    <th>Qty</th>
                    <th>Unit price</th>
                    <th class="right">Total</th>
                </tr>
            </thead>
            <tbody>
                @if($order->orderItems && $order->orderItems->count() > 0)
                    @foreach($order->orderItems as $line)
                        <tr>
                            <td>
                                <div class="po-item-name">{{ optional($line->item)->name }}</div>
                                <div class="po-item-sku">{{ optional($line->item)->sku ?? 'SKU-'.$line->item_id }}</div>
                            </td>
                            <td><div class="po-item-val">{{ $line->quantity }}</div></td>
                            <td><div class="po-item-val">RM {{ number_format($line->unit_price, 2) }}</div></td>
                            <td class="right"><div class="po-item-val">RM {{ number_format($line->quantity * $line->unit_price, 2) }}</div></td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td>
                            <div class="po-item-name">{{ optional($order->item)->name ?? 'Unknown Item' }}</div>
                            <div class="po-item-sku">{{ optional($order->item)->sku ?? 'SKU-'.$order->item_id }}</div>
                        </td>
                        <td><div class="po-item-val">{{ $order->quantity }}</div></td>
                        <td><div class="po-item-val">RM {{ number_format($order->unit_price, 2) }}</div></td>
                        <td class="right"><div class="po-item-val">RM {{ number_format($order->total_amount, 2) }}</div></td>
                    </tr>
                @endif
            </tbody>
        </table>

        <div class="po-total-row">
            <div class="po-total-label">Total amount</div>
            <div class="po-total-value">RM {{ number_format($order->total_amount, 2) }}</div>
        </div>
    </div>
</div>
@endsection
