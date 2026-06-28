@extends('layouts.staff')

@section('title', 'Return Requests – 22UniMart')

@push('styles')
<style>
    /* Content */
    .page-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 28px; }
    .page-title { font-size: 1.6rem; font-weight: 800; color: #0f2044; }
    .page-sub   { font-size: 0.85rem; color: #7a8fa8; margin-top: 4px; }
    .btn-primary-action { background: #0f2044; color: #fff; border: none; border-radius: 8px; padding: 11px 20px; font-size: 0.88rem; font-weight: 600; cursor: pointer; font-family: 'Inter', sans-serif; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: background 0.15s; white-space: nowrap; }
    .btn-primary-action:hover { background: #1e3a6e; color: #fff; }

    /* Stat cards */
    .stats-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 28px; }
    .stat-card { background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 1px 6px rgba(15,32,68,0.07); position: relative; overflow: hidden; }
    .stat-label { font-size: 0.65rem; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; color: #9daec5; margin-bottom: 6px; }
    .stat-value { font-size: 2rem; font-weight: 800; color: #0f2044; line-height: 1; margin-bottom: 6px; }
    .stat-hint { font-size: 0.72rem; color: #9daec5; }
    .stat-hint.up { color: #27ae60; }
    .stat-hint.down { color: #e74c3c; }
    .stat-hint.warn { color: #d4870a; }
    .stat-icon { position: absolute; top: 16px; right: 16px; font-size: 1.4rem; opacity: 0.15; }

    /* Filters bar */
    .filter-bar { background: #fff; border-radius: 12px; padding: 16px 20px; box-shadow: 0 1px 6px rgba(15,32,68,0.07); margin-bottom: 20px; display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
    .filter-select { border: 1px solid #e2e8f0; border-radius: 8px; padding: 8px 14px; font-size: 0.85rem; font-family: 'Inter', sans-serif; color: #3a4d6a; background: #f8fafc; outline: none; cursor: pointer; }
    .filter-select:focus { border-color: #4a90d9; }
    .filter-btn { background: #0f2044; color: #fff; border: none; border-radius: 8px; padding: 8px 18px; font-size: 0.85rem; font-weight: 600; cursor: pointer; font-family: 'Inter', sans-serif; transition: background 0.15s; }
    .filter-btn:hover { background: #1e3a6e; }
    .filter-clear { background: #fff; color: #7a8fa8; border: 1px solid #e2e8f0; border-radius: 8px; padding: 8px 18px; font-size: 0.85rem; font-weight: 500; cursor: pointer; font-family: 'Inter', sans-serif; text-decoration: none; transition: all 0.15s; }
    .filter-clear:hover { background: #f4f6fb; color: #0f2044; }
    .filter-spacer { flex: 1; }
    .results-info { font-size: 0.8rem; color: #7a8fa8; white-space: nowrap; }

    /* Table card */
    .table-card { background: #fff; border-radius: 12px; box-shadow: 0 1px 6px rgba(15,32,68,0.07); overflow: hidden; }
    .table-wrap { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; }
    thead tr { background: #f8fafc; border-bottom: 2px solid #eef2f7; }
    thead th { padding: 13px 16px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: #7a8fa8; text-align: left; white-space: nowrap; }
    tbody tr { border-bottom: 1px solid #f4f6fb; transition: background 0.12s; }
    tbody tr:last-child { border-bottom: none; }
    tbody tr:hover { background: #f8fafc; }
    tbody td { padding: 14px 16px; font-size: 0.86rem; color: #3a4d6a; vertical-align: middle; }

    .rr-number { font-size: 0.78rem; font-weight: 700; color: #4a90d9; font-family: monospace; letter-spacing: 0.5px; }
    .item-name { font-weight: 600; color: #0f2044; font-size: 0.88rem; }
    .item-cat  { font-size: 0.72rem; color: #9daec5; margin-top: 2px; }
    .supplier-link { color: #4a90d9; font-weight: 500; font-size: 0.85rem; text-decoration: none; }
    .supplier-link:hover { text-decoration: underline; }

    /* Reason badges */
    .badge-reason { display: inline-flex; align-items: center; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
    .badge-expired  { background: #fef3e2; color: #d4870a; }
    .badge-damaged  { background: #fdedec; color: #c0392b; }

    /* Status badges */
    .badge-status { display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; }
    .badge-draft    { background: #f4f6fb; color: #5a6a85; }
    .badge-pending  { background: #e8f4fd; color: #2980b9; }
    .badge-approved { background: #e8f8f0; color: #1d8348; }
    .badge-rejected { background: #fdedec; color: #c0392b; }
    .badge-dot { width: 7px; height: 7px; border-radius: 50%; background: currentColor; display: inline-block; }

    /* Credit note badge */
    .cn-badge { display: inline-flex; flex-direction: column; gap: 2px; }
    .cn-id { font-size: 0.72rem; font-weight: 700; color: #4a90d9; font-family: monospace; }
    .cn-amt { font-size: 0.68rem; color: #27ae60; font-weight: 600; }

    /* Action link */
    .btn-outline-action { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 6px; border: 1px solid #d1dce8; font-size: 0.78rem; font-weight: 600; color: #3a4d6a; text-decoration: none; background: #fff; transition: all 0.15s; white-space: nowrap; font-family: 'Inter', sans-serif; }
    .btn-outline-action:hover { border-color: #0f2044; color: #0f2044; background: #f8fafc; }

    /* Pagination */
    .pagination-bar { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-top: 1px solid #f0f4f8; font-size: 0.82rem; color: #7a8fa8; flex-wrap: wrap; gap: 12px; }
    .pagination-links { display: flex; align-items: center; gap: 4px; }
    .page-btn { width: 32px; height: 32px; border-radius: 7px; border: 1px solid #e2e8f0; background: #fff; display: flex; align-items: center; justify-content: center; font-size: 0.82rem; font-weight: 600; color: #3a4d6a; cursor: pointer; text-decoration: none; transition: all 0.15s; }
    .page-btn:hover { background: #f4f6fb; }
    .page-btn.active { background: #0f2044; color: #fff; border-color: #0f2044; }
    .page-btn.disabled { color: #c0cfe0; cursor: not-allowed; pointer-events: none; }

    /* Flash */
    .flash { padding: 13px 20px; border-radius: 8px; font-size: 0.88rem; margin-bottom: 20px; font-weight: 500; }
    .flash-success { background: #e8f8f0; color: #1d8348; border: 1px solid #a9dfbf; }
    .flash-error   { background: #fdedec; color: #922b21; border: 1px solid #f5b7b1; }

    /* Empty */
    .empty-state { padding: 60px 20px; text-align: center; color: #9daec5; }
    .empty-icon  { font-size: 2.5rem; margin-bottom: 12px; }
    .empty-text  { font-size: 0.95rem; font-weight: 600; color: #7a8fa8; }
    .empty-sub   { font-size: 0.82rem; color: #aab8cc; margin-top: 4px; }
    
    /* Topbar Search */
    .topbar-search { display: flex; align-items: center; gap: 8px; background: #f4f6fb; border-radius: 8px; padding: 8px 14px; flex: 1; max-width: 340px; }
    .topbar-search input { border: none; outline: none; font-family: 'Inter', sans-serif; font-size: .85rem; color: #3a4d6a; width: 100%; background: transparent; }
    .topbar-search input::placeholder { color: #9daec5; }
    .avatar { width: 34px; height: 34px; border-radius: 50%; background: #0f2044; display: flex; align-items: center; justify-content: center; color: #fff; font-size: .82rem; font-weight: 700; }
</style>
@endpush

@section('topbar')
    <span class="topbar-brand">22UniMart</span>
    <form method="GET" action="{{ route('staff.rr.index') }}" class="topbar-search">
        <svg width="14" height="14" fill="none" stroke="#9daec5" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
        <input type="text" name="search" placeholder="Search return requests..." value="{{ request('search') }}">
    </form>
    <div class="topbar-right">
        <a href="{{ route('staff.notif.index') }}" class="icon-btn" style="text-decoration:none;">🔔</a>
        @include('owner.components.topbar-profile')
    </div>
@endsection

@section('content')
@php
    use App\Models\ReturnRequest;
    $totalRR      = ReturnRequest::count();
    $pendingCount = ReturnRequest::where('status', 'Pending')->count();
    $approvedCount= ReturnRequest::where('status', 'Approved')->count();
    $rejectedCount= ReturnRequest::where('status', 'Rejected')->count();
@endphp

<!-- Page header -->
<div class="page-header">
    <div>
        <div class="page-title">Return Requests Overview</div>
        <div class="page-sub">Manage expired & damaged item returns, and supplier credit note authorisations.</div>
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
        <div class="stat-label">Total Requests</div>
        <div class="stat-value">{{ number_format($totalRR) }}</div>
        <div class="stat-hint">All time</div>
        <div class="stat-icon">↩</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Pending Approval</div>
        <div class="stat-value" style="color:#2980b9;">{{ $pendingCount }}</div>
        <div class="stat-hint warn">Needs your review</div>
        <div class="stat-icon">⏳</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Approved</div>
        <div class="stat-value" style="color:#1d8348;">{{ $approvedCount }}</div>
        <div class="stat-hint up">Credit notes issued</div>
        <div class="stat-icon">✔</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Rejected</div>
        <div class="stat-value" style="color:#c0392b;">{{ $rejectedCount }}</div>
        <div class="stat-hint down">No credit issued</div>
        <div class="stat-icon">✕</div>
    </div>
</div>

<!-- Filter bar -->
<form method="GET" action="{{ route('staff.rr.index') }}">
    <div class="filter-bar">
        <select name="status" class="filter-select">
            <option value="">All Statuses</option>
            <option value="Draft"    {{ request('status') == 'Draft'    ? 'selected' : '' }}>Draft</option>
            <option value="Pending"  {{ request('status') == 'Pending'  ? 'selected' : '' }}>Pending</option>
            <option value="Approved" {{ request('status') == 'Approved' ? 'selected' : '' }}>Approved</option>
            <option value="Rejected" {{ request('status') == 'Rejected' ? 'selected' : '' }}>Rejected</option>
        </select>

        @if($suppliers->count() > 0)
        <select name="supplier_id" class="filter-select">
            <option value="">All Suppliers</option>
            @foreach($suppliers as $sup)
                <option value="{{ $sup->id }}" {{ request('supplier_id') == $sup->id ? 'selected' : '' }}>{{ $sup->name }}</option>
            @endforeach
        </select>
        @endif

        <select name="reason" class="filter-select">
            <option value="">All Reasons</option>
            <option value="Expired"  {{ request('reason') == 'Expired'  ? 'selected' : '' }}>Expired</option>
            <option value="Damaged"  {{ request('reason') == 'Damaged'  ? 'selected' : '' }}>Damaged</option>
        </select>

        <button type="submit" class="filter-btn">Apply Filters</button>
        <a href="{{ route('staff.rr.index') }}" class="filter-clear">Clear</a>

        <div class="filter-spacer"></div>

        @if(!request()->has('view_all'))
            <div class="results-info">
                Displaying {{ $requests->firstItem() ?? 0 }}–{{ $requests->lastItem() ?? 0 }} of {{ $requests->total() }}
            </div>
        @endif
    </div>
</form>

<!-- Table -->
<div class="table-card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Request ID</th>
                    <th>Supplier</th>
                    <th>Invoice No.</th>
                    <th>Created At</th>
                    <th>Status</th>
                    <th>Created By</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($requests as $rr)
                <tr>
                    <td>
                        <span class="rr-number">#{{ $rr->return_number }}</span>
                    </td>
                    <td>
                        <span style="font-weight:600; color:#0f2044;">{{ $rr->supplier->name ?? 'N/A' }}</span>
                    </td>
                    <td style="font-size:0.82rem;font-weight:600;color:#4a90d9;font-family:monospace;">{{ $rr->invoice_number ?? '—' }}</td>
                    <td style="white-space:nowrap;color:#7a8fa8;">{{ $rr->created_at->format('M d, Y') }}</td>
                    <td>
                        @if($rr->status === 'Draft')
                            <span class="badge-status badge-draft"><span class="badge-dot"></span> Draft</span>
                        @elseif($rr->status === 'Pending')
                            <span class="badge-status badge-pending"><span class="badge-dot"></span> Pending</span>
                        @elseif($rr->status === 'Approved')
                            <span class="badge-status badge-approved"><span class="badge-dot"></span> Approved</span>
                        @else
                            <span class="badge-status badge-rejected"><span class="badge-dot"></span> Rejected</span>
                        @endif
                    </td>
                    <td style="font-size:0.82rem;color:#5a6a85;">{{ optional($rr->createdBy)->name ?? '—' }}</td>
                    <td>
                        <a href="{{ route('staff.rr.show', $rr) }}" class="btn-outline-action">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            View Details
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <div class="empty-icon">↩</div>
                            <div class="empty-text">No return requests found</div>
                            <div class="empty-sub">Try adjusting your filters or create a new return request.</div>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="pagination-bar">
        <span>Showing {{ $requests->firstItem() ?? 0 }} to {{ $requests->lastItem() ?? 0 }} of {{ number_format($requests->total()) }} entries</span>
        <div class="pagination-links">
            <a href="{{ $requests->previousPageUrl() ?? '#' }}" class="page-btn {{ $requests->onFirstPage() ? 'disabled' : '' }}">‹</a>
            @foreach($requests->getUrlRange(1, $requests->lastPage()) as $page => $url)
                @if($page <= 3 || $page > $requests->lastPage() - 1 || abs($page - $requests->currentPage()) <= 1)
                    <a href="{{ $url }}" class="page-btn {{ $page == $requests->currentPage() ? 'active' : '' }}">{{ $page }}</a>
                @elseif($page == 4 && $requests->currentPage() > 5)
                    <span class="page-btn disabled">…</span>
                @endif
            @endforeach
            <a href="{{ $requests->nextPageUrl() ?? '#' }}" class="page-btn {{ !$requests->hasMorePages() ? 'disabled' : '' }}">›</a>
        </div>
    </div>
</div>
@endsection
