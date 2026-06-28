@extends('layouts.staff')

@section('title', 'Purchase Orders – 22UniMart')

@push('styles')
<style>
    /* Content */
    .page-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 28px; }
    .page-title { font-size: 1.6rem; font-weight: 800; color: #0f2044; }
    .page-sub   { font-size: 0.85rem; color: #7a8fa8; margin-top: 4px; }
    .btn-create { display: inline-flex; align-items: center; gap: 8px; background: #0f2044; color: #fff; border: none; border-radius: 8px; padding: 10px 20px; font-size: 0.88rem; font-weight: 600; cursor: pointer; font-family: 'Inter', sans-serif; text-decoration: none; transition: background 0.15s; }
    .btn-create:hover { background: #182e5e; }

    /* Stat Cards */
    .stats-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 28px; }
    .stat-card { background: #fff; border-radius: 12px; padding: 24px; box-shadow: 0 1px 6px rgba(15,32,68,0.07); }
    .stat-label { font-size: 0.65rem; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; color: #9daec5; margin-bottom: 6px; }
    .stat-value { font-size: 2.2rem; font-weight: 800; color: #0f2044; line-height: 1; margin-bottom: 4px; }
    .stat-value.orange { color: #d4870a; }
    .stat-value.green  { color: #1d8348; }
    .stat-value.red    { color: #c0392b; }
    .stat-hint { font-size: 0.75rem; color: #9daec5; }

    /* Section */
    .section { background: #fff; border-radius: 12px; box-shadow: 0 1px 6px rgba(15,32,68,0.07); overflow: hidden; }
    .section-header { display: flex; align-items: center; justify-content: space-between; padding: 20px 24px 16px; border-bottom: 1px solid #f0f4f8; }
    .section-title { font-size: 1rem; font-weight: 700; color: #0f2044; }
    .btn-export { display: inline-flex; align-items: center; gap: 6px; background: #fff; border: 1px solid #d1dce8; color: #3a4d6a; border-radius: 7px; padding: 7px 16px; font-size: 0.82rem; font-weight: 600; cursor: pointer; font-family: 'Inter', sans-serif; text-decoration: none; transition: background 0.15s; }
    .btn-export:hover { background: #f4f6fb; }

    /* Filter Bar */
    .filter-bar { display: flex; align-items: center; gap: 12px; padding: 16px 24px; border-bottom: 1px solid #f0f4f8; flex-wrap: wrap; }
    .filter-search { display: flex; align-items: center; gap: 8px; background: #f4f6fb; border-radius: 8px; padding: 8px 14px; flex: 1; min-width: 200px; max-width: 340px; border: 1px solid #edf2f7; }
    .filter-search input { border: none; background: transparent; outline: none; font-family: 'Inter', sans-serif; font-size: 0.85rem; color: #3a4d6a; width: 100%; }
    .filter-search input::placeholder { color: #9daec5; }
    .filter-select { border: 1px solid #d1dce8; border-radius: 8px; padding: 8px 14px; font-size: 0.82rem; font-family: 'Inter', sans-serif; color: #3a4d6a; background: #fff; outline: none; cursor: pointer; }
    .btn-apply-filter { background: #0f2044; color: #fff; border: none; border-radius: 8px; padding: 9px 20px; font-size: 0.82rem; font-weight: 600; cursor: pointer; font-family: 'Inter', sans-serif; text-decoration: none; transition: background 0.15s; }
    .btn-clear-filter { background: #fff; color: #5a6a85; border: 1px solid #d1dce8; border-radius: 8px; padding: 8px 20px; font-size: 0.82rem; font-weight: 600; cursor: pointer; font-family: 'Inter', sans-serif; text-decoration: none; transition: background 0.15s; }

    /* Table */
    .table-wrap { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; }
    thead th { padding: 10px 20px; font-size: 0.65rem; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; color: #9daec5; text-align: left; background: #f8fafc; border-bottom: 1px solid #edf2f7; white-space: nowrap; }
    tbody td { padding: 16px 20px; font-size: 0.88rem; color: #1a2744; border-bottom: 1px solid #f0f4f8; vertical-align: middle; }
    tbody tr:hover td { background: #fafbfd; }
    .po-id { font-weight: 700; font-size: 0.82rem; color: #0f2044; white-space: nowrap; }
    .supplier-name { font-size: 0.85rem; color: #3a4d6a; }
    .delivery-chip { display: inline-flex; align-items: flex-start; gap: 6px; background: #eef2f7; border-radius: 7px; padding: 6px 10px; font-size: 0.78rem; color: #3a4d6a; line-height: 1.4; white-space: nowrap; }
    .delivery-pending { color: #9daec5; font-size: 0.82rem; }

    /* Badges */
    .badge { display: inline-flex; align-items: center; padding: 5px 14px; border-radius: 20px; font-size: 0.72rem; font-weight: 700; letter-spacing: 0.5px; text-transform: capitalize; white-space: nowrap; }
    .badge-pending  { background: #fef3e2; color: #d4870a; border: 1px solid #fde0a0; }
    .badge-approved { background: #e8f8f0; color: #1d8348; border: 1px solid #a9dfbf; }
    .badge-received { background: #e8f8f0; color: #1d8348; border: 1px solid #a9dfbf; }
    .badge-rejected { background: #fdedec; color: #c0392b; border: 1px solid #f5b7b1; }

    /* Pagination */
    .pagination-bar { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-top: 1px solid #f0f4f8; font-size: 0.82rem; color: #7a8fa8; flex-wrap: wrap; gap: 12px; }
    .pagination-links { display: flex; align-items: center; gap: 4px; }
    .page-btn { width: 32px; height: 32px; border-radius: 7px; border: 1px solid #e2e8f0; background: #fff; display: flex; align-items: center; justify-content: center; font-size: 0.82rem; font-weight: 600; color: #3a4d6a; cursor: pointer; text-decoration: none; transition: all 0.15s; }
    .page-btn:hover { background: #f4f6fb; }
    .page-btn.active { background: #0f2044; color: #fff; border-color: #0f2044; }
    .page-btn.disabled { color: #c0cfe0; cursor: not-allowed; pointer-events: none; }

    /* Flash */
    .flash { padding: 12px 20px; border-radius: 8px; font-size: 0.88rem; margin-bottom: 20px; }
    .flash-success { background: #e8f8f0; color: #1d8348; border: 1px solid #a9dfbf; }
    .flash-error   { background: #fdedec; color: #c0392b; border: 1px solid #f5b7b1; }
    .empty-state { padding: 48px; text-align: center; color: #9daec5; font-size: 0.9rem; }

    /* Topbar Search */
    .topbar-search { display: flex; align-items: center; gap: 8px; background: #f4f6fb; border-radius: 8px; padding: 8px 14px; flex: 1; max-width: 340px; }
    .topbar-search input { border: none; outline: none; font-family: 'Inter', sans-serif; font-size: .85rem; color: #3a4d6a; width: 100%; background: transparent; }
    .topbar-search input::placeholder { color: #9daec5; }
    .avatar { width: 34px; height: 34px; border-radius: 50%; background: #0f2044; display: flex; align-items: center; justify-content: center; color: #fff; font-size: .82rem; font-weight: 700; }
</style>
@endpush

@section('topbar')
    <span class="topbar-brand">22UniMart</span>
    <form method="GET" action="{{ route('staff.po.index') }}" class="topbar-search">
        <svg width="14" height="14" fill="none" stroke="#9daec5" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
        <input type="text" name="search" placeholder="Search orders..." value="{{ request('search') }}">
    </form>
    <div class="topbar-right">
        <a href="{{ route('staff.notif.index') }}" class="icon-btn" style="text-decoration:none;">🔔</a>
        @include('staff.components.topbar-profile')
    </div>
@endsection

@section('content')
@php
    $totalPOs      = $orders->count();
    $pendingCount  = $orders->where('status','Pending')->count();
    $approvedCount = $orders->where('status','Approved')->count();
    $rejectedCount = $orders->where('status','Rejected')->count();
    $receivedCount = $orders->whereIn('status', ['Received', 'received'])->count();
@endphp

<!-- Header -->
<div class="page-header">
    <div>
        <div class="page-title">Purchase Orders</div>
        <div class="page-sub">Manage and track inventory requests from suppliers.</div>
    </div>
</div>

@if(session('success'))
    <div class="flash flash-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="flash flash-error">{{ session('error') }}</div>
@endif

<!-- Stat Cards -->
<div class="stats-row">
    <div class="stat-card">
        <div class="stat-label">Total Purchase Orders</div>
        <div class="stat-value">{{ $totalPOs }}</div>
        <div class="stat-hint">All purchase orders in the system</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Pending</div>
        <div class="stat-value orange">{{ $pendingCount }}</div>
        <div class="stat-hint">Awaiting approval</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Approved</div>
        <div class="stat-value green">{{ $approvedCount }}</div>
        <div class="stat-hint">In processing</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Rejected</div>
        <div class="stat-value red">{{ $rejectedCount }}</div>
        <div class="stat-hint">Requires review</div>
    </div>
</div>

<!-- Orders Table -->
<div class="section">
    <div class="section-header">
        <div class="section-title">Recent Orders</div>
        <div style="display: flex; gap: 10px; align-items: center;">
            <a href="{{ route('staff.po.create') }}" class="btn-create" style="padding: 7px 16px; font-size: 0.82rem;">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Create Purchase Order
            </a>
            <a href="{{ route('staff.po.export-csv') }}" class="btn-export">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Export to CSV
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-bar">
        <form method="GET" action="{{ route('staff.po.index') }}" style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap; width: 100%;">
            <div class="filter-search" style="display:none;">
                <input type="text" name="search" placeholder="Search PO ID or Supplier..." value="{{ request('search') }}">
            </div>
            <select name="status" class="filter-select">
                <option value="">All Status</option>
                <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                <option value="Approved" {{ request('status') == 'Approved' ? 'selected' : '' }}>Approved</option>
                <option value="Received" {{ request('status') == 'Received' ? 'selected' : '' }}>Received</option>
                <option value="Rejected" {{ request('status') == 'Rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
            <select name="supplier_id" class="filter-select">
                <option value="">All Suppliers</option>
                @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-apply-filter">Apply Filters</button>
            <a href="{{ route('staff.po.index') }}" class="btn-clear-filter">Clear</a>
        </form>
    </div>

    <div class="table-wrap">
        <table id="ordersTable">
            <thead>
                <tr>
                    <th>PO ID</th>
                    <th>Supplier</th>
                    <th>Delivery Schedule</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                    <th>Created By</th>
                    <th>Created At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                @forelse($orders as $order)
                    @php
                        $s   = $order->status;
                        $cls = match(strtolower($s)) {
                            'pending'  => 'badge-pending',
                            'approved' => 'badge-approved',
                            'received' => 'badge-received',
                            default    => 'badge-rejected',
                        };
                    @endphp
                    <tr
                        data-po="{{ strtolower($order->po_number) }}"
                        data-supplier="{{ strtolower(optional($order->supplier)->name ?? '') }}"
                        data-status="{{ $s }}"
                        data-date="{{ $order->order_date ? $order->order_date->format('Y-m-d') : '' }}"
                    >
                        <td><div class="po-id">#{{ $order->po_number }}</div></td>
                        <td><div class="supplier-name">{{ optional($order->supplier)->name ?? 'N/A' }}</div></td>
                        <td>
                            @if($order->delivery && $order->delivery->delivery_date)
                                <div class="delivery-chip">
                                    <svg width="13" height="13" fill="none" stroke="#4a90d9" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                    <div>
                                        {{ $order->delivery->delivery_date->format('M d,') }}<br>
                                        {{ $order->delivery->delivery_time ? \Carbon\Carbon::parse($order->delivery->delivery_time)->format('g:i A') : '' }}
                                    </div>
                                </div>
                            @else
                                <span class="delivery-pending">{{ $s === 'Pending' ? 'Pending' : 'N/A' }}</span>
                            @endif
                        </td>
                        <td>RM {{ number_format($order->total_amount, 2) }}</td>
                        <td><span class="badge {{ $cls }}">{{ ucfirst($s) }}</span></td>
                        @php
                            $creator = \App\Models\User::find($order->created_by);
                            $creatorText = $creator ? $creator->name . ' (' . ucfirst($creator->role) . ')' : 'System';
                        @endphp
                        <td style="font-size:0.82rem;color:#5a6a85;white-space:nowrap;">{{ $creatorText }}</td>
                        <td style="font-size:0.82rem;color:#5a6a85;white-space:nowrap;">{{ $order->created_at ? $order->created_at->format('d M Y, g:i A') : '—' }}</td>
                        <td>
                            <a href="{{ route('staff.po.show', $order) }}" style="display:inline-flex;align-items:center;gap:6px;padding:8px 12px;border-radius:8px;background:#0f2044;color:#fff;text-decoration:none;font-size:0.82rem;">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="empty-state">No purchase orders found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="pagination-bar">
        <span>Showing {{ $orders->firstItem() ?? 0 }} to {{ $orders->lastItem() ?? 0 }} of {{ number_format($orders->total()) }} entries</span>
        <div class="pagination-links">
            <a href="{{ $orders->previousPageUrl() ?? '#' }}" class="page-btn {{ $orders->onFirstPage() ? 'disabled' : '' }}">‹</a>
            @foreach($orders->getUrlRange(1, $orders->lastPage()) as $page => $url)
                @if($page <= 3 || $page > $orders->lastPage() - 1 || abs($page - $orders->currentPage()) <= 1)
                    <a href="{{ $url }}" class="page-btn {{ $page == $orders->currentPage() ? 'active' : '' }}">{{ $page }}</a>
                @elseif($page == 4 && $orders->currentPage() > 5)
                    <span class="page-btn disabled">…</span>
                @endif
            @endforeach
            <a href="{{ $orders->nextPageUrl() ?? '#' }}" class="page-btn {{ !$orders->hasMorePages() ? 'disabled' : '' }}">›</a>
        </div>
    </div>
</div>
@endsection
