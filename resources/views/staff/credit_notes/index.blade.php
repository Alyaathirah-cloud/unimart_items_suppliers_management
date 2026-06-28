@extends('layouts.staff')

@section('title', 'Credit Notes – 22UniMart')

@push('styles')
    <style>
        .page-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 28px; }
        .page-title { font-size: 1.6rem; font-weight: 800; color: #0f2044; }
        .page-sub   { font-size: 0.85rem; color: #7a8fa8; margin-top: 4px; }

        /* Stat cards */
        .stats-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 28px; }
        .stat-card { background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 1px 6px rgba(15,32,68,0.07); position: relative; overflow: hidden; }
        .stat-label { font-size: 0.65rem; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; color: #9daec5; margin-bottom: 6px; }
        .stat-value { font-size: 2rem; font-weight: 800; color: #0f2044; line-height: 1; margin-bottom: 6px; }
        .stat-hint  { font-size: 0.72rem; color: #9daec5; }
        .stat-icon  { position: absolute; top: 16px; right: 16px; font-size: 1.4rem; opacity: 0.15; }

        /* Filter bar */
        .filter-bar { background: #fff; border-radius: 12px; padding: 16px 20px; box-shadow: 0 1px 6px rgba(15,32,68,0.07); margin-bottom: 20px; display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
        .filter-select { border: 1px solid #e2e8f0; border-radius: 8px; padding: 8px 14px; font-size: 0.85rem; font-family: 'Inter', sans-serif; color: #3a4d6a; background: #f8fafc; outline: none; cursor: pointer; }
        .filter-select:focus { border-color: #4a90d9; }
        .filter-btn { background: #0f2044; color: #fff; border: none; border-radius: 8px; padding: 8px 18px; font-size: 0.85rem; font-weight: 600; cursor: pointer; font-family: 'Inter', sans-serif; transition: background 0.15s; }
        .filter-btn:hover { background: #1e3a6e; }
        .filter-clear { background: #fff; color: #7a8fa8; border: 1px solid #e2e8f0; border-radius: 8px; padding: 8px 18px; font-size: 0.85rem; font-weight: 500; cursor: pointer; font-family: 'Inter', sans-serif; text-decoration: none; transition: all 0.15s; }
        .filter-clear:hover { background: #f4f6fb; color: #0f2044; }
        .filter-spacer { flex: 1; }
        .results-info { font-size: 0.8rem; color: #7a8fa8; white-space: nowrap; }

        /* Table */
        .table-card { background: #fff; border-radius: 12px; box-shadow: 0 1px 6px rgba(15,32,68,0.07); overflow: hidden; }
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        thead tr { background: #f8fafc; border-bottom: 2px solid #eef2f7; }
        thead th { padding: 13px 16px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: #7a8fa8; text-align: left; white-space: nowrap; }
        thead th.sortable { cursor: pointer; user-select: none; }
        thead th.sortable:hover { color: #0f2044; }
        thead th .sort-icon { font-size: 0.65rem; margin-left: 4px; opacity: 0.5; }
        tbody tr { border-bottom: 1px solid #f4f6fb; transition: background 0.12s; cursor: pointer; }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: #f0f5ff; }
        tbody td { padding: 14px 16px; font-size: 0.86rem; color: #3a4d6a; vertical-align: middle; }

        .cn-id { font-size: 0.78rem; font-weight: 700; color: #4a90d9; font-family: monospace; letter-spacing: 0.5px; }
        .supplier-name { font-weight: 600; color: #0f2044; font-size: 0.88rem; }
        .supplier-email { font-size: 0.72rem; color: #9daec5; margin-top: 2px; }
        .rr-number { font-size: 0.78rem; color: #7a8fa8; font-family: monospace; }
        .amount-val { font-weight: 700; color: #0f2044; }

        /* Status badges */
        .badge { display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; border-radius: 20px; font-size: 0.73rem; font-weight: 700; letter-spacing: 0.4px; text-transform: uppercase; }
        .badge-dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; display: inline-block; }
        .badge-unused    { background: #e8f8f0; color: #1d8348; }
        .badge-partial   { background: #fef3e2; color: #d4870a; }
        .badge-used      { background: #f4f6fb; color: #7a8fa8; }

        /* Table footer */
        .table-footer { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-top: 1px solid #f0f4f8; background: #fafbfd; flex-wrap: wrap; gap: 12px; }
        .footer-note { font-size: 0.75rem; color: #aab8cc; }
        .pagination-wrap { display: flex; align-items: center; gap: 8px; }
        .page-btn { width: 32px; height: 32px; border-radius: 7px; border: 1px solid #e2e8f0; background: #fff; color: #3a4d6a; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 0.85rem; text-decoration: none; font-family: 'Inter', sans-serif; transition: all 0.12s; }
        .page-btn:hover { background: #0f2044; color: #fff; border-color: #0f2044; }
        .page-btn.disabled { opacity: 0.4; pointer-events: none; }
        .page-btn.active { background: #0f2044; color: #fff; border-color: #0f2044; }

        /* Empty */
        .empty-state { padding: 60px 20px; text-align: center; color: #9daec5; }
        .empty-icon  { font-size: 2.5rem; margin-bottom: 12px; }
        .empty-text  { font-size: 0.95rem; font-weight: 600; color: #7a8fa8; }
        .empty-sub   { font-size: 0.82rem; color: #aab8cc; margin-top: 4px; }

        /* Export btn */
        .btn-export { display: inline-flex; align-items: center; gap: 7px; background: #fff; border: 1px solid #e2e8f0; color: #3a4d6a; border-radius: 8px; padding: 9px 16px; font-size: 0.85rem; font-weight: 600; text-decoration: none; cursor: pointer; font-family: 'Inter', sans-serif; transition: all 0.15s; }
        .btn-export:hover { background: #f4f6fb; border-color: #c5cedb; }
        
        .topbar-search { display: flex; align-items: center; gap: 8px; background: #f4f6fb; border-radius: 8px; padding: 8px 14px; flex: 1; max-width: 360px; margin-right: auto; }
        .topbar-search input { border: none; background: transparent; outline: none; font-family: 'Inter', sans-serif; font-size: 0.88rem; color: #3a4d6a; width: 100%; }
        .topbar-search input::placeholder { color: #9daec5; }
    </style>
@endpush

@php
    use App\Models\CreditNote;
    $totalCN     = CreditNote::count();
    $unusedCN    = CreditNote::where('status', 'Unused')->count();
    $partialCN   = CreditNote::where('status', 'Partially Used')->count();
    $usedCN      = CreditNote::where('status', 'Used')->count();
    $totalAmt    = CreditNote::sum('amount');
@endphp

@section('topbar')
    <form class="topbar-search" method="GET" action="{{ route('staff.cn.index') }}">
        <svg width="15" height="15" fill="none" stroke="#9daec5" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
        <input type="text" name="search" placeholder="Search credit notes..." value="{{ request('search') }}">
    </form>
    <div class="topbar-right">
        <a href="{{ route('staff.notif.index') }}" class="icon-btn" style="text-decoration:none;">🔔</a>
        @include('owner.components.topbar-profile')
    </div>
@endsection

@section('content')

        <!-- Page Header -->
        <div class="page-header">
            <div>
                <div class="page-title">Credit Notes</div>
                <div class="page-sub">Auto-generated credit notes from approved return requests.</div>
            </div>

        </div>

        @if(session('success'))
            <div class="flash flash-success">{{ session('success') }}</div>
        @endif

        <!-- Stat Cards -->
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-label">Total Notes</div>
                <div class="stat-value">{{ number_format($totalCN) }}</div>
                <div class="stat-hint">All credit notes</div>
                <div class="stat-icon">📋</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Unused</div>
                <div class="stat-value" style="color:#1d8348;">{{ $unusedCN }}</div>
                <div class="stat-hint">Full balance available</div>
                <div class="stat-icon">✅</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Partially Used</div>
                <div class="stat-value" style="color:#d4870a;">{{ $partialCN }}</div>
                <div class="stat-hint">Partial balance remaining</div>
                <div class="stat-icon">⏳</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Total Credit Value</div>
                <div class="stat-value" style="font-size:1.4rem;">RM {{ number_format($totalAmt, 2) }}</div>
                <div class="stat-hint">Cumulative issued</div>
                <div class="stat-icon">💰</div>
            </div>
        </div>

        <!-- Filter Bar -->
        <form method="GET" action="{{ route('staff.cn.index') }}">
            <div class="filter-bar">
                <input type="hidden" name="search" value="{{ request('search') }}">

                <select name="supplier_id" class="filter-select">
                    <option value="">All Suppliers</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                    @endforeach
                </select>

                <select name="status" class="filter-select">
                    <option value="">All Statuses</option>
                    <option value="Approved" {{ request('status') == 'Approved' ? 'selected' : '' }}>Approved</option>
                    <option value="Credit Applied" {{ request('status') == 'Credit Applied' ? 'selected' : '' }}>Credit Applied</option>
                    <option value="Rejected" {{ request('status') == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                </select>

                <select name="sort" class="filter-select">
                    <option value="desc" {{ request('sort','desc') == 'desc' ? 'selected' : '' }}>Latest First</option>
                    <option value="asc"  {{ request('sort') == 'asc'  ? 'selected' : '' }}>Earliest First</option>
                </select>

                <button type="submit" class="filter-btn">Apply</button>
                <a href="{{ route('staff.cn.index') }}" class="filter-clear">Clear</a>

                <div class="filter-spacer"></div>
                <div class="results-info">{{ $creditNotes->total() }} result{{ $creditNotes->total() != 1 ? 's' : '' }}</div>
            </div>
        </form>

        <!-- Table -->
        <div class="table-card">
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Credit Note ID</th>
                            <th>Supplier</th>
                            <th>Return Request</th>
                            <th>Issued Amount</th>
                            <th>Remaining Balance</th>
                            <th>Status</th>
                            <th>Date Issued</th>
                            <th>Expiry Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($creditNotes as $note)
                        <tr>
                            <td><span class="cn-id">{{ $note->credit_note_id }}</span></td>
                            <td>
                                <div class="supplier-name">{{ $note->supplier->name ?? 'N/A' }}</div>
                                <div class="supplier-email">{{ $note->supplier->contact_email ?? '' }}</div>
                            </td>
                            <td><span class="rr-number">#{{ $note->returnRequest->return_number ?? 'N/A' }}</span></td>
                            <td><span class="amount-val">RM {{ number_format($note->amount, 2) }}</span></td>
                            <td>
                                @if(in_array($note->status, ['Used', 'Expired']))
                                    <span style="color:#9daec5;">—</span>
                                @else
                                    <span style="font-weight:700; color:#1d8348;">RM {{ number_format($note->remaining_balance, 2) }}</span>
                                @endif
                            </td>
                            <td>
                                @if($note->status === 'Unused')
                                    <span class="badge badge-unused"><span class="badge-dot"></span> Unused</span>
                                @elseif($note->status === 'Partially Used')
                                    <span class="badge badge-partial"><span class="badge-dot"></span> Partially Used</span>
                                @else
                                    <span class="badge badge-used"><span class="badge-dot"></span> {{ $note->status }}</span>
                                @endif
                            </td>
                            <td style="color:#7a8fa8;white-space:nowrap;">{{ $note->issue_date->format('d M Y') }}</td>
                            <td style="color:#7a8fa8;white-space:nowrap;">{{ $note->issue_date->copy()->addDays(60)->format('d M Y') }}</td>
                            <td>
                                <a href="{{ route('staff.cn.show', $note) }}" class="action-link">View Details</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">
                                <div class="empty-state">
                                    <div class="empty-icon">📋</div>
                                    <div class="empty-text">No credit notes found</div>
                                    <div class="empty-sub">Credit notes are auto-generated when return requests are approved.</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Table Footer / Pagination -->
            <div class="table-footer">
                <div class="footer-info">Showing {{ $creditNotes->firstItem() ?? 0 }} to {{ $creditNotes->lastItem() ?? 0 }} of {{ $creditNotes->total() }} items</div>
                <div class="pagination-wrap">
                    @if($creditNotes->onFirstPage())
                        <span class="page-btn disabled">‹</span>
                    @else
                        <a href="{{ $creditNotes->previousPageUrl() }}" class="page-btn">‹</a>
                    @endif

                    @foreach(range(1, max(1, $creditNotes->lastPage())) as $i)
                        @if($i >= $creditNotes->currentPage() - 2 && $i <= $creditNotes->currentPage() + 2)
                            @if($i == $creditNotes->currentPage())
                                <span class="page-btn active">{{ $i }}</span>
                            @else
                                <a href="{{ $creditNotes->url($i) }}" class="page-btn">{{ $i }}</a>
                            @endif
                        @endif
                    @endforeach

                    @if($creditNotes->hasMorePages())
                        <a href="{{ $creditNotes->nextPageUrl() }}" class="page-btn">›</a>
                    @else
                        <span class="page-btn disabled">›</span>
                    @endif
                </div>
        </div>

    </div>
@endsection


