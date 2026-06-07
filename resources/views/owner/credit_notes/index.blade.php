<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Credit Notes – 22UniMart</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: #eef2f7; display: flex; min-height: 100vh; }

        /* ── Sidebar ── */
        .sidebar { width: 210px; flex-shrink: 0; background: #0f2044; color: #fff; display: flex; flex-direction: column; padding: 0 0 24px 0; position: fixed; top: 0; left: 0; height: 100vh; }
        .sidebar-brand { padding: 20px 20px 4px; display: flex; align-items: center; gap: 12px; }
        .brand-square { width: 32px; height: 32px; background: #fff; color: #0f2044; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 0.9rem; font-weight: 800; flex-shrink: 0; }
        .brand-text .brand-name { font-size: 0.9rem; font-weight: 800; }
        .brand-text .brand-sub  { font-size: 0.68rem; color: #8ca0c0; }
        .sidebar-nav { flex: 1; margin-top: 20px; overflow-y: auto; }
        .nav-item { display: flex; align-items: center; gap: 12px; padding: 11px 20px; font-size: 0.88rem; font-weight: 500; color: #8ca0c0; cursor: pointer; text-decoration: none; transition: all 0.15s; border-left: 3px solid transparent; }
        .nav-item:hover { color: #fff; background: rgba(255,255,255,0.06); }
        .nav-item.active { color: #fff; background: rgba(255,255,255,0.1); border-left-color: #4a90d9; }
        .nav-icon { width: 18px; text-align: center; flex-shrink: 0; }
        .sidebar-bottom { padding: 0 20px; display: flex; flex-direction: column; gap: 8px; }
        .btn-logout { background: #c0392b; color: #fff; border: none; border-radius: 8px; padding: 12px 16px; font-size: 0.85rem; font-weight: 600; cursor: pointer; width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px; transition: background 0.15s; }
        .btn-logout:hover { background: #a93226; }

        /* ── Main ── */
        .main { margin-left: 210px; flex: 1; display: flex; flex-direction: column; }

        /* Topbar */
        .topbar { background: #fff; border-bottom: 1px solid #e2e8f0; padding: 0 32px; height: 56px; display: flex; align-items: center; gap: 16px; position: sticky; top: 0; z-index: 10; }
        .search-box { display: flex; align-items: center; gap: 8px; background: #f4f6fb; border-radius: 8px; padding: 8px 14px; flex: 1; max-width: 360px; }
        .search-box input { border: none; background: transparent; outline: none; font-family: 'Inter', sans-serif; font-size: 0.88rem; color: #3a4d6a; width: 100%; }
        .search-box input::placeholder { color: #9daec5; }
        .topbar-right { margin-left: auto; display: flex; align-items: center; gap: 20px; }
        .avatar { width: 36px; height: 36px; border-radius: 50%; background: #0f2044; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 0.85rem; font-weight: 700; }

        /* Content */
        .content { padding: 32px; }
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
        .filter-search { display: flex; align-items: center; gap: 8px; background: #f4f6fb; border-radius: 8px; padding: 8px 14px; flex: 1; min-width: 180px; max-width: 280px; border: 1px solid #e2e8f0; }
        .filter-search input { border: none; background: transparent; outline: none; font-family: 'Inter', sans-serif; font-size: 0.85rem; color: #3a4d6a; width: 100%; }
        .filter-search input::placeholder { color: #9daec5; }
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

        /* Flash */
        .flash { padding: 13px 20px; border-radius: 8px; font-size: 0.88rem; margin-bottom: 20px; font-weight: 500; }
        .flash-success { background: #e8f8f0; color: #1d8348; border: 1px solid #a9dfbf; }

        /* Empty */
        .empty-state { padding: 60px 20px; text-align: center; color: #9daec5; }
        .empty-icon  { font-size: 2.5rem; margin-bottom: 12px; }
        .empty-text  { font-size: 0.95rem; font-weight: 600; color: #7a8fa8; }
        .empty-sub   { font-size: 0.82rem; color: #aab8cc; margin-top: 4px; }

        /* Export btn */
        .btn-export { display: inline-flex; align-items: center; gap: 7px; background: #fff; border: 1px solid #e2e8f0; color: #3a4d6a; border-radius: 8px; padding: 9px 16px; font-size: 0.85rem; font-weight: 600; text-decoration: none; cursor: pointer; font-family: 'Inter', sans-serif; transition: all 0.15s; }
        .btn-export:hover { background: #f4f6fb; border-color: #c5cedb; }
    </style>
</head>
<body>

@php
    use App\Models\CreditNote;
    $totalCN     = CreditNote::count();
    $unusedCN    = CreditNote::where('status', 'Unused')->count();
    $partialCN   = CreditNote::where('status', 'Partially Used')->count();
    $usedCN      = CreditNote::where('status', 'Used')->count();
    $totalAmt    = CreditNote::sum('amount');
@endphp

<!-- Sidebar -->
<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="brand-square">22</div>
        <div class="brand-text">
            <div class="brand-name">22UNIMART</div>
            <div class="brand-sub">Inventory Control</div>
        </div>
    </div>
        <nav class="sidebar-nav">
        <a href="{{ route('owner.dashboard') }}" class="nav-item {{ request()->routeIs('owner.dashboard') ? 'active' : '' }}"><span class="nav-icon">⊞</span> Dashboard</a>
        <a href="{{ route('owner.items.index') }}" class="nav-item {{ request()->routeIs('owner.items.*') ? 'active' : '' }}"><span class="nav-icon">📦</span> Inventory</a>
        <a href="{{ route('owner.suppliers.index') }}" class="nav-item {{ request()->routeIs('owner.suppliers.*') ? 'active' : '' }}"><span class="nav-icon">🏢</span> Suppliers</a>
        <a href="{{ route('owner.purchase-orders.index') }}" class="nav-item {{ request()->routeIs('owner.purchase-orders.*') ? 'active' : '' }}"><span class="nav-icon">🛒</span> Purchase Orders</a>
        <a href="{{ route('owner.return-requests.index') }}" class="nav-item {{ request()->routeIs('owner.return-requests.*') ? 'active' : '' }}"><span class="nav-icon">↩</span> Return Requests</a>
        <a href="{{ route('owner.invoices.index') }}" class="nav-item {{ request()->routeIs('owner.invoices.*') ? 'active' : '' }}"><span class="nav-icon">📄</span> Invoices</a>
        <a href="{{ route('owner.credit-notes.index') }}" class="nav-item {{ request()->routeIs('owner.credit-notes.*') ? 'active' : '' }}"><span class="nav-icon">📋</span> Credit Notes</a>
        <a href="{{ route('owner.notifications.index') }}" class="nav-item {{ request()->routeIs('owner.notifications.*') ? 'active' : '' }}"><span class="nav-icon">🔔</span> Notifications</a>
    </nav>
        <div class="sidebar-bottom">
        <div class="sidebar-link" style="color: #fff; cursor: default; font-weight: bold;">Role: Owner</div>
        <form action="{{ route('logout') }}" method="POST" style="margin: 0; width: 100%;">
            @csrf
            <button type="submit" class="btn-report" style="background: #c0392b;">Logout</button>
        </form>
    </div>
</aside>

<!-- Main -->
<div class="main">
    <!-- Topbar -->
    <div class="topbar">
        <div class="search-box">
            <svg width="15" height="15" fill="none" stroke="#9daec5" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            <input type="text" placeholder="Search credit notes...">
        </div>
                <div class="topbar-right">
            <a href="{{ route('owner.notifications.index') }}" class="icon-btn" style="text-decoration:none;">🔔</a>
            <div class="topbar-profile">
                <div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <div>
                    <div style="font-size:0.85rem;font-weight:600;color:#1a2744">{{ auth()->user()->name }}</div>
                    <form action="{{ route('logout') }}" method="POST" style="display:inline">
                        @csrf
                        <button type="submit" style="background:none;border:none;font-size:0.72rem;color:#9daec5;cursor:pointer;font-family:inherit;padding:0;">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="content">

        <!-- Page Header -->
        <div class="page-header">
            <div>
                <div class="page-title">Credit Notes</div>
                <div class="page-sub">Auto-generated credit notes from approved return requests.</div>
            </div>
            <a href="{{ route('owner.credit-notes.export-pdf') }}{{ request()->getQueryString() ? '?'.request()->getQueryString() : '' }}" class="btn-export">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Export CSV
            </a>
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
        <form method="GET" action="{{ route('owner.credit-notes.index') }}">
            <div class="filter-bar">
                <div class="filter-search">
                    <svg width="14" height="14" fill="none" stroke="#9daec5" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                    <input type="text" name="search" placeholder="Search by CN ID or supplier…" value="{{ request('search') }}">
                </div>

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
                <a href="{{ route('owner.credit-notes.index') }}" class="filter-clear">Clear</a>

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
                            <th>Item</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Date Issued</th>
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
                            <td style="color:#0f2044;font-weight:500;">
                                @php
                                    $rrLines = $note->returnRequest?->lines ?? collect();
                                    $firstItemName = optional($rrLines->first()?->item)->name ?? '—';
                                    $lineCount = $rrLines->count();
                                @endphp
                                {{ $firstItemName }}{{ $lineCount > 1 ? ' +' . ($lineCount - 1) . ' more' : '' }}
                            </td>
                            <td><span class="amount-val">RM {{ number_format($note->amount, 2) }}</span></td>
                            <td>
                                @if($note->status === 'Approved')
                                    <span class="badge badge-unused"><span class="badge-dot"></span> Approved</span>
                                @elseif($note->status === 'Credit Applied')
                                    <span class="badge badge-partial"><span class="badge-dot"></span> Credit Applied</span>
                                @else
                                    <span class="badge badge-used"><span class="badge-dot"></span> {{ $note->status }}</span>
                                @endif
                            </td>
                            <td style="color:#7a8fa8;white-space:nowrap;">{{ $note->issue_date->format('d M Y') }}</td>
                            <td>
                                @if(in_array($note->status, ['Approved', 'Credit Applied']))
                                    <button class="btn-export" onclick="event.stopPropagation(); downloadCreditNote({{ $note->id }})">⬇ Download</button>
                                @else
                                    <span style="color:#9daec5;font-size:0.8rem;">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
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
                <div class="footer-note">Credit notes are read-only records. Click any row to view the full document.</div>
                <div class="pagination-wrap">
                    @if($creditNotes->onFirstPage())
                        <span class="page-btn disabled">‹</span>
                    @else
                        <a class="page-btn" href="{{ $creditNotes->previousPageUrl() }}">‹</a>
                    @endif

                    <span style="font-size:0.8rem;color:#7a8fa8;padding:0 8px;">Page {{ $creditNotes->currentPage() }} / {{ $creditNotes->lastPage() }}</span>

                    @if($creditNotes->hasMorePages())
                        <a class="page-btn" href="{{ $creditNotes->nextPageUrl() }}">›</a>
                    @else
                        <span class="page-btn disabled">›</span>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>

<script>
function downloadCreditNote(creditNoteId) {
    window.open(`/owner/credit-notes/${creditNoteId}/export-pdf`, '_blank');
}
</script>
</body>
</html>