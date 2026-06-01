<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Purchase Orders – 22UniMart</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: #eef2f7; display: flex; min-height: 100vh; }

        /* ── Sidebar ── */
        .sidebar { width: 210px; flex-shrink: 0; background: #0f2044; color: #fff; display: flex; flex-direction: column; padding: 0 0 24px 0; position: fixed; top: 0; left: 0; height: 100vh; overflow-y: auto; }
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
        .sidebar-link:hover { color: #fff; }

        /* ── Main ── */
        .main { margin-left: 210px; flex: 1; display: flex; flex-direction: column; }

        /* Topbar */
        .topbar { background: #fff; border-bottom: 1px solid #e2e8f0; padding: 0 32px; height: 56px; display: flex; align-items: center; gap: 16px; position: sticky; top: 0; z-index: 10; }
        .search-box { display: flex; align-items: center; gap: 8px; background: #f4f6fb; border-radius: 8px; padding: 8px 14px; flex: 1; max-width: 360px; }
        .search-box input { border: none; background: transparent; outline: none; font-family: 'Inter', sans-serif; font-size: 0.88rem; color: #3a4d6a; width: 100%; }
        .search-box input::placeholder { color: #9daec5; }
        .topbar-right { margin-left: auto; display: flex; align-items: center; gap: 20px; }
        .icon-btn { background: none; border: none; cursor: pointer; color: #5a6a85; font-size: 1.1rem; }
        .btn-switch { background: #fff; border: 1px solid #d1dce8; border-radius: 7px; padding: 7px 14px; font-size: 0.82rem; font-weight: 600; color: #3a4d6a; cursor: pointer; font-family: 'Inter', sans-serif; }
        .topbar-profile { display: flex; align-items: center; gap: 8px; }
        .profile-name { font-size: 0.85rem; font-weight: 600; color: #1a2744; }
        .profile-sub { font-size: 0.72rem; color: #9daec5; }
        .avatar { width: 36px; height: 36px; border-radius: 50%; background: #0f2044; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 0.85rem; font-weight: 700; }

        /* Content */
        .content { padding: 32px; }
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
        .stat-hint .up { color: #1d8348; font-weight: 600; }

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
        .filter-select:focus { border-color: #0f2044; }
        .btn-apply-filter { background: #0f2044; color: #fff; border: none; border-radius: 8px; padding: 9px 20px; font-size: 0.82rem; font-weight: 600; cursor: pointer; font-family: 'Inter', sans-serif; text-decoration: none; transition: background 0.15s; }
        .btn-apply-filter:hover { background: #182e5e; }
        .btn-clear-filter { background: #fff; color: #5a6a85; border: 1px solid #d1dce8; border-radius: 8px; padding: 8px 20px; font-size: 0.82rem; font-weight: 600; cursor: pointer; font-family: 'Inter', sans-serif; text-decoration: none; transition: background 0.15s; }
        .btn-clear-filter:hover { background: #f4f6fb; }

        /* Table */
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        thead th { padding: 10px 20px; font-size: 0.65rem; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; color: #9daec5; text-align: left; background: #f8fafc; border-bottom: 1px solid #edf2f7; white-space: nowrap; }
        tbody td { padding: 16px 20px; font-size: 0.88rem; color: #1a2744; border-bottom: 1px solid #f0f4f8; vertical-align: middle; }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr:hover td { background: #fafbfd; }
        .po-id { font-weight: 700; font-size: 0.82rem; color: #0f2044; white-space: nowrap; }
        .item-name { font-weight: 600; color: #0f2044; font-size: 0.88rem; }
        .item-qty  { font-size: 0.75rem; color: #9daec5; margin-top: 2px; }
        .supplier-name { font-size: 0.85rem; color: #3a4d6a; }
        .date-issued { font-size: 0.82rem; color: #5a6a85; white-space: nowrap; }
        .delivery-chip { display: inline-flex; align-items: flex-start; gap: 6px; background: #eef2f7; border-radius: 7px; padding: 6px 10px; font-size: 0.78rem; color: #3a4d6a; line-height: 1.4; white-space: nowrap; }
        .delivery-chip svg { flex-shrink: 0; margin-top: 1px; }
        .delivery-pending { color: #9daec5; font-size: 0.82rem; }

        /* Badges */
        .badge { display: inline-flex; align-items: center; padding: 5px 14px; border-radius: 20px; font-size: 0.72rem; font-weight: 700; letter-spacing: 0.5px; text-transform: capitalize; white-space: nowrap; }
        .badge-pending  { background: #fef3e2; color: #d4870a; border: 1px solid #fde0a0; }
        .badge-approved { background: #e8f8f0; color: #1d8348; border: 1px solid #a9dfbf; }
        .badge-received { background: #e8f8f0; color: #1d8348; border: 1px solid #a9dfbf; }
        .badge-rejected { background: #fdedec; color: #c0392b; border: 1px solid #f5b7b1; }

        /* Pagination */
        .pagination-bar { display: flex; align-items: center; justify-content: space-between; padding: 14px 24px; border-top: 1px solid #f0f4f8; font-size: 0.82rem; color: #7a8fa8; }
        .page-btns { display: flex; align-items: center; gap: 4px; }
        .page-btn { width: 30px; height: 30px; border-radius: 6px; border: 1px solid #e2e8f0; background: #fff; color: #3a4d6a; font-size: 0.82rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.15s; font-family: 'Inter', sans-serif; }
        .page-btn:hover { background: #f4f6fb; }
        .page-btn.active { background: #0f2044; color: #fff; border-color: #0f2044; }
        .page-btn:disabled { opacity: 0.4; cursor: not-allowed; }

        /* Flash */
        .flash { padding: 12px 20px; border-radius: 8px; font-size: 0.88rem; margin-bottom: 20px; }
        .flash-success { background: #e8f8f0; color: #1d8348; border: 1px solid #a9dfbf; }
        .flash-error   { background: #fdedec; color: #c0392b; border: 1px solid #f5b7b1; }
        .empty-state { padding: 48px; text-align: center; color: #9daec5; font-size: 0.9rem; }
    </style>
</head>
<body>
@php
    $totalPOs      = $orders->count();
    $pendingCount  = $orders->where('status','Pending')->count();
    $approvedCount = $orders->where('status','Approved')->count();
    $rejectedCount = $orders->where('status','Rejected')->count();
    $receivedCount = $orders->whereIn('status', ['Received', 'received'])->count();
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
            <input type="text" placeholder="Search orders..." id="topbarSearch">
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

    <!-- Content -->
    <div class="content">
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
                <a href="{{ route('owner.purchase-orders.export-csv') }}" class="btn-export">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Export to CSV
                </a>
            </div>

            <!-- Filters -->
            <div class="filter-bar">
                <form method="GET" action="{{ route('owner.purchase-orders.index') }}" style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap; width: 100%;">
                    <div class="filter-search">
                        <svg width="14" height="14" fill="none" stroke="#9daec5" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
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
                    <a href="{{ route('owner.purchase-orders.index') }}" class="btn-clear-filter">Clear</a>
                </form>
            </div>

            <div class="table-wrap">
                <table id="ordersTable">
                    <thead>
                        <tr>
                            <th>PO ID</th>
                            <th>Item Detail</th>
                            <th>Supplier</th>
                            <th>Date Issued</th>
                            <th>Delivery Schedule</th>
                            <th>Qty</th>
                            <th>Total Amount</th>
                            <th>Status</th>
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
                                <td>
                                    <div class="item-name">{{ optional($order->item)->name ?? 'N/A' }}</div>
                                    <div class="item-qty">Qty: {{ number_format($order->quantity) }} units</div>
                                </td>
                                <td><div class="supplier-name">{{ optional($order->supplier)->name ?? 'N/A' }}</div></td>
                                <td>
                                    <div class="date-issued">
                                        @if($order->order_date)
                                            {{ $order->order_date->format('M') }}<br>
                                            {{ $order->order_date->format('d,') }}<br>
                                            {{ $order->order_date->format('Y') }}
                                        @else
                                            —
                                        @endif
                                    </div>
                                </td>
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
                                <td style="font-weight:600;color:#0f2044;">{{ number_format($order->quantity) }}</td>
                                <td>RM {{ number_format($order->total_amount, 2) }}</td>
                                <td><span class="badge {{ $cls }}">{{ ucfirst($s) }}</span></td>
                                <td>
                                    <a href="{{ route('owner.purchase-orders.show', $order) }}" style="display:inline-flex;align-items:center;gap:6px;padding:8px 12px;border-radius:8px;background:#0f2044;color:#fff;text-decoration:none;font-size:0.82rem;">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="empty-state">No purchase orders found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination or View All -->
            @if(request()->has('view_all'))
                <div class="pagination-bar">
                    <div>Showing all {{ $orders->count() }} results</div>
                    <div class="page-btns">
                        <a href="{{ route('owner.purchase-orders.index', request()->except('view_all')) }}" class="btn btn-outline">Show Paginated</a>
                    </div>
                </div>
            @else
                <div class="pagination-bar">
                    <div>Showing {{ $orders->firstItem() ?? 0 }} to {{ $orders->lastItem() ?? 0 }} of {{ $orders->total() }} results</div>
                    <div class="page-btns">
                        {{ $orders->appends(request()->query())->links() }}
                        @if($orders->hasPages())
                            <a href="{{ route('owner.purchase-orders.index', array_merge(request()->query(), ['view_all' => 1])) }}" class="btn btn-outline">View All</a>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
</body>
</html>