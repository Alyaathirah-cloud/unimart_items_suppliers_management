<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Return Requests – 22UniMart</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: #eef2f7; display: flex; min-height: 100vh; }

        /* Sidebar */
        .sidebar { width: 210px; flex-shrink: 0; background: #0f2044; color: #fff; display: flex; flex-direction: column; padding: 0 0 24px 0; position: fixed; top: 0; left: 0; height: 100vh; }
        .sidebar-brand { padding: 20px 20px 4px; display: flex; align-items: center; gap: 12px; }
        .brand-square { width: 32px; height: 32px; background: #fff; color: #0f2044; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 0.9rem; font-weight: 800; flex-shrink: 0; }
        .brand-text .brand-name { font-size: 0.9rem; font-weight: 800; }
        .brand-text .brand-sub  { font-size: 0.68rem; color: #8ca0c0; }
        .sidebar-nav { flex: 1; margin-top: 20px; }
        .nav-item { display: flex; align-items: center; gap: 12px; padding: 11px 20px; font-size: 0.88rem; font-weight: 500; color: #8ca0c0; cursor: pointer; text-decoration: none; transition: all 0.15s; border-left: 3px solid transparent; }
        .nav-item:hover { color: #fff; background: rgba(255,255,255,0.06); }
        .nav-item.active { color: #fff; background: rgba(255,255,255,0.1); border-left-color: #4a90d9; }
        .nav-icon { width: 18px; text-align: center; flex-shrink: 0; }
        .sidebar-bottom { padding: 0 20px; display: flex; flex-direction: column; gap: 8px; }
        .btn-report { background: #1e3a6e; color: #fff; border: none; border-radius: 8px; padding: 12px 16px; font-size: 0.85rem; font-weight: 600; cursor: pointer; width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px; transition: background 0.15s; text-decoration: none; }
        .btn-report:hover { background: #2a4f8f; }
        .sidebar-link { display: flex; align-items: center; gap: 10px; color: #8ca0c0; font-size: 0.82rem; text-decoration: none; padding: 6px 0; }

        /* Main */
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
        .action-link { font-size: 0.8rem; color: #4a90d9; text-decoration: none; font-weight: 600; white-space: nowrap; }
        .action-link:hover { text-decoration: underline; }

        /* Pagination */
        .table-footer { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-top: 1px solid #f0f4f8; background: #fafbfd; flex-wrap: wrap; gap: 12px; }
        .footer-info { font-size: 0.8rem; color: #7a8fa8; }
        .footer-note { font-size: 0.75rem; color: #aab8cc; }
        .pagination-wrap { display: flex; align-items: center; gap: 8px; }
        .page-btn { width: 32px; height: 32px; border-radius: 7px; border: 1px solid #e2e8f0; background: #fff; color: #3a4d6a; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 0.85rem; text-decoration: none; font-family: 'Inter', sans-serif; transition: all 0.12s; }
        .page-btn:hover { background: #0f2044; color: #fff; border-color: #0f2044; }
        .page-btn.disabled { opacity: 0.4; pointer-events: none; }

        /* Flash */
        .flash { padding: 13px 20px; border-radius: 8px; font-size: 0.88rem; margin-bottom: 20px; font-weight: 500; }
        .flash-success { background: #e8f8f0; color: #1d8348; border: 1px solid #a9dfbf; }
        .flash-error   { background: #fdedec; color: #922b21; border: 1px solid #f5b7b1; }

        /* Empty */
        .empty-state { padding: 60px 20px; text-align: center; color: #9daec5; }
        .empty-icon  { font-size: 2.5rem; margin-bottom: 12px; }
        .empty-text  { font-size: 0.95rem; font-weight: 600; color: #7a8fa8; }
        .empty-sub   { font-size: 0.82rem; color: #aab8cc; margin-top: 4px; }
    </style>
</head>
<body>

@php
    use App\Models\ReturnRequest;
    $totalRR      = ReturnRequest::count();
    $pendingCount = ReturnRequest::where('status', 'Pending')->count();
    $approvedCount= ReturnRequest::where('status', 'Approved')->count();
    $rejectedCount= ReturnRequest::where('status', 'Rejected')->count();
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
            <input type="text" placeholder="Search return requests...">
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

        <!-- Page header -->
        <div class="page-header">
            <div>
                <div class="page-title">Return Requests Overview</div>
                <div class="page-sub">Manage expired & damaged item returns, and supplier credit note authorisations.</div>
            </div>
            <a href="{{ route('owner.return-requests.create') }}" class="btn-primary-action">
                ＋ New Return Request
            </a>
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
                <div class="stat-hint warn">Awaiting supplier action</div>
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
        <form method="GET" action="{{ route('owner.return-requests.index') }}">
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
                <a href="{{ route('owner.return-requests.index') }}" class="filter-clear">Clear</a>

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
                            <th>Item &amp; Category</th>
                            <th>Supplier</th>
                            <th>Qty</th>
                            <th>Date</th>
                            <th>Reason</th>
                            <th>Credit Note</th>
                            <th>Status</th>
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
                                <div class="item-name">{{ $rr->item->name ?? 'N/A' }}</div>
                                <div class="item-cat">{{ $rr->item->category ?? '—' }}</div>
                            </td>
                            <td>
                                <a href="{{ route('owner.suppliers.index') }}" class="supplier-link">{{ $rr->supplier->name ?? 'N/A' }}</a>
                            </td>
                            <td>{{ number_format($rr->quantity) }}</td>
                            <td style="white-space:nowrap;color:#7a8fa8;">{{ $rr->created_at->format('M d, Y') }}</td>
                            <td>
                                @if($rr->reason === 'Expired')
                                    <span class="badge-reason badge-expired">Expired</span>
                                @elseif($rr->reason === 'Damaged')
                                    <span class="badge-reason badge-damaged">Damaged</span>
                                @else
                                    <span style="color:#9daec5;">—</span>
                                @endif
                            </td>
                            <td>
                                @if($rr->creditNote)
                                    <div class="cn-badge">
                                        <span class="cn-id">{{ $rr->creditNote->credit_note_id }}</span>
                                        <span class="cn-amt">+RM {{ number_format($rr->creditNote->amount, 2) }}</span>
                                    </div>
                                @else
                                    <span style="color:#c5cedb;font-size:0.8rem;">—</span>
                                @endif
                            </td>
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
                            <td>
                                <a href="{{ route('owner.return-requests.show', $rr) }}" class="action-link">View Details</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">
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

            <!-- Table footer / pagination -->
            <div class="table-footer">
                <div class="footer-note">Audit trail maintained for all status changes. Records kept as per system policy.</div>
                <div class="pagination-wrap">
                    @if(!request()->has('view_all') && $requests instanceof \Illuminate\Pagination\LengthAwarePaginator)
                        @if($requests->onFirstPage())
                            <span class="page-btn disabled">‹</span>
                        @else
                            <a class="page-btn" href="{{ $requests->previousPageUrl() }}">‹</a>
                        @endif

                        <span style="font-size:0.8rem;color:#7a8fa8;padding:0 8px;">Page {{ $requests->currentPage() }} / {{ $requests->lastPage() }}</span>

                        @if($requests->hasMorePages())
                            <a class="page-btn" href="{{ $requests->nextPageUrl() }}">›</a>
                        @else
                            <span class="page-btn disabled">›</span>
                        @endif
                    @endif
                    @if($requests instanceof \Illuminate\Pagination\LengthAwarePaginator && $requests->hasPages())
                        @if(request()->has('view_all'))
                            <a href="{{ route('owner.return-requests.index', request()->except('view_all')) }}" class="filter-clear" style="margin-left:8px;">Paginate</a>
                        @else
                            <a href="{{ route('owner.return-requests.index', array_merge(request()->query(), ['view_all'=>1])) }}" class="filter-clear" style="margin-left:8px;">View All</a>
                        @endif
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>
</body>
</html>