@extends('layouts.owner')

@section('title', 'Owner Dashboard – 22UniMart')

@push('styles')
<style>
    .topbar { padding: 0 32px; }
    .search-box { display: flex; align-items: center; gap: 8px; background: #f4f6fb; border-radius: 8px; padding: 8px 14px; flex: 1; max-width: 360px; }
    .search-box input { border: none; background: transparent; outline: none; font-family: 'Inter', sans-serif; font-size: 0.88rem; color: #3a4d6a; width: 100%; }
    .search-box input::placeholder { color: #9daec5; }
    .btn-switch { background: #fff; border: 1px solid #d1dce8; border-radius: 7px; padding: 7px 14px; font-size: 0.82rem; font-weight: 600; color: #3a4d6a; cursor: pointer; font-family: 'Inter', sans-serif; }
    .topbar-profile .dropdown-item { white-space: nowrap; }
    .page-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 28px; }
    .page-title { font-size: 1.6rem; font-weight: 800; color: #0f2044; }
    .page-sub { font-size: 0.85rem; color: #7a8fa8; margin-top: 4px; }
    .refresh-tag { display: flex; align-items: center; gap: 6px; font-size: 0.75rem; color: #7a8fa8; background: #f4f6fb; border: 1px solid #e2e8f0; border-radius: 20px; padding: 5px 12px; }
    .stats-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 24px; }
    .stat-card { background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 1px 6px rgba(15,32,68,0.07); position: relative; border-left: 4px solid transparent; text-decoration: none; display: block; transition: transform 0.15s, box-shadow 0.15s; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(15,32,68,0.1); }
    .stat-card.blue { border-left-color: #3498db; }
    .stat-card.red { border-left-color: #e74c3c; }
    .stat-card.orange { border-left-color: #e67e22; }
    .stat-card.yellow { border-left-color: #f1c40f; }
    .stat-card.grey { border-left-color: #95a5a6; }
    .stat-label { font-size: 0.7rem; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; color: #9daec5; }
    .stat-value { font-size: 2rem; font-weight: 800; color: #0f2044; margin: 6px 0 4px; line-height: 1; }
    .stat-hint { font-size: 0.72rem; color: #9daec5; }
    .stat-icon { position: absolute; top: 20px; right: 20px; font-size: 1.8rem; opacity: 0.8; }
    
    .quick-actions { display: flex; gap: 16px; margin-bottom: 32px; }
    .btn-qa { flex: 1; display: flex; align-items: center; justify-content: center; gap: 8px; background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px; font-size: 0.9rem; font-weight: 600; color: #0f2044; text-decoration: none; transition: all 0.15s; box-shadow: 0 1px 4px rgba(15,32,68,0.04); }
    .btn-qa:hover { background: #f8fafc; border-color: #cbd5e1; }
    .btn-qa-primary { background: #0f2044; color: #fff; border-color: #0f2044; }
    .btn-qa-primary:hover { background: #182e5e; border-color: #182e5e; }

    .critical-header { display: flex; align-items: center; gap: 8px; font-size: 1rem; font-weight: 700; color: #0f2044; margin-bottom: 16px; }
    .critical-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; align-items: start; padding-bottom: 32px; }
    .crit-card { background: #fff; border-radius: 12px; box-shadow: 0 1px 6px rgba(15,32,68,0.07); overflow: hidden; display: flex; flex-direction: column; min-height: 250px; }
    .crit-header { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-bottom: 1px solid #f0f4f8; }
    .crit-header.orange { background: #fffaf0; border-bottom-color: #ffe0b2; }
    .crit-header.yellow { background: #fffde7; border-bottom-color: #fff59d; }
    .crit-header.blue { background: #f0f8ff; border-bottom-color: #bfdbfe; }
    .crit-title { font-size: 0.9rem; font-weight: 700; color: #0f2044; }
    .view-all { font-size: 0.78rem; color: #3a7bd5; text-decoration: none; font-weight: 600; }
    .view-all:hover { text-decoration: underline; }
    .crit-body { flex: 1; display: flex; flex-direction: column; }
    .ls-item, .ne-item { display: flex; align-items: center; gap: 14px; padding: 12px 20px; border-bottom: 1px solid #f4f6fb; }
    .ls-item:last-child, .ne-item:last-child { border-bottom: none; }
    .ls-icon, .ne-icon { width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1rem; flex-shrink: 0; }
    .ls-icon { background: #fef3e2; }
    .ne-icon { background: #fdedec; color: #e74c3c; }
    .pu-icon { background: #e8f4ff; color: #3498db; width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1rem; flex-shrink: 0; }
    .ls-info, .ne-info { flex: 1; }
    .ls-name, .ne-name { font-size: 0.85rem; font-weight: 600; color: #0f2044; }
    .ls-sku, .ne-batch { font-size: 0.72rem; color: #9daec5; }
    .ls-qty { font-size: 0.82rem; font-weight: 700; color: #d4870a; margin-right: 4px; white-space: nowrap; }
    .ne-expiry { font-size: 0.78rem; font-weight: 700; color: #d4870a; margin-right: 8px; white-space: nowrap; }
    .ne-qty { font-size: 0.72rem; color: #9daec5; white-space: nowrap; display: block; margin-top: 2px; }
    .pu-date { font-size: 0.78rem; font-weight: 600; color: #3a4d6a; margin-right: 4px; white-space: nowrap; }
    .flash { padding: 12px 20px; border-radius: 8px; font-size: 0.88rem; margin-bottom: 20px; }
    .flash-success { background: #e8f8f0; color: #1d8348; border: 1px solid #a9dfbf; }
    .empty-state { padding: 24px; text-align: center; color: #9daec5; font-size: 0.85rem; margin: auto; }
</style>
@endpush

@section('breadcrumbs')
    <a href="{{ route('owner.dashboard') }}">Dashboard</a> › <span style="color:#0f2044;">Overview</span>
@endsection

@section('topbar')
    <div class="search-box">
        <svg width="15" height="15" fill="none" stroke="#9daec5" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
        <input type="text" placeholder="Search inventory, POs...">
    </div>
    <div class="topbar-right">
        <a href="{{ route('owner.notifications.index') }}" class="icon-btn" style="text-decoration:none;">🔔</a>
        <div class="topbar-profile" onclick="toggleProfileDropdown()">
            <div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            <div>
                <div style="font-size:0.85rem;font-weight:600;color:#1a2744">{{ auth()->user()->name }}</div>
                <div style="font-size:0.72rem;color:#9daec5;">Owner</div>
            </div>
            <div class="profile-dropdown" id="profileDropdown">
                <a href="{{ route('owner.profile.edit') }}" class="dropdown-item">👤 My Profile</a>
                <a href="{{ route('owner.password.change') }}" class="dropdown-item">🔐 Change Password</a>
                <form action="{{ route('logout') }}" method="POST" style="display:contents;">
                    @csrf
                    <button type="submit" class="dropdown-item logout" style="border:none;background:none;width:100%;text-align:left;cursor:pointer;">🚪 Logout</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('content')
    @php
        use App\Models\Item;
        use App\Models\PurchaseOrder;
        use App\Models\ReturnRequest;
        use App\Models\Invoice;

        $totalItems = Item::count();
        $outOfStock = Item::where('quantity', 0)->count();
        $lowStock = Item::where('quantity', '>', 0)->whereColumn('quantity', '<=', 'reorder_point')->count();
        $expired = Item::where('quantity', '>', 0)->whereNotNull('expiry_date')->whereDate('expiry_date', '<', now())->count();
        $expiringSoon = Item::where('quantity', '>', 0)
                            ->whereNotNull('expiry_date')
                            ->whereDate('expiry_date', '<=', now()->addDays(7))
                            ->whereDate('expiry_date', '>=', now())
                            ->count();
        $directPurchase = Item::whereNull('supplier_id')->count();

        // Panel lists
        $lowStockItemsList = Item::with('supplier')->where('quantity', '>', 0)->whereColumn('quantity', '<=', 'reorder_point')->take(5)->get();
        $expiringSoonItemsList = Item::with('supplier')->where('quantity', '>', 0)
                                    ->whereNotNull('expiry_date')
                                    ->whereDate('expiry_date', '<=', now()->addDays(7))
                                    ->whereDate('expiry_date', '>=', now())
                                    ->orderBy('expiry_date', 'asc')
                                    ->take(5)->get();

        // Pending Updates: POs, Return Requests, Unpaid Invoices
        $pendingPOs = PurchaseOrder::with('supplier')->whereIn('status', ['Pending', 'Awaiting Supplier Approval'])->orderBy('created_at', 'desc')->take(5)->get()->map(fn($po) => [
            'type' => 'Purchase Order',
            'ref' => $po->po_number ?? 'PO-' . $po->id,
            'date' => $po->created_at,
            'desc' => optional($po->supplier)->name ?? 'Supplier'
        ]);
        $pendingReturns = ReturnRequest::with('supplier')->where('status', 'Pending')->orderBy('created_at', 'desc')->take(5)->get()->map(fn($rr) => [
            'type' => 'Return Request',
            'ref' => $rr->return_number ?? 'RR-' . $rr->id,
            'date' => $rr->created_at,
            'desc' => optional($rr->supplier)->name ?? 'Supplier'
        ]);
        $unpaidInvoices = Invoice::with('supplier')->where('status', 'Unpaid')->orderBy('payment_due_date', 'asc')->take(5)->get()->map(fn($inv) => [
            'type' => 'Invoice (Unpaid)',
            'ref' => $inv->invoice_number ?? 'INV-' . $inv->id,
            'date' => $inv->payment_due_date,
            'desc' => optional($inv->supplier)->name ?? 'Supplier'
        ]);

        $pendingUpdates = collect()
            ->merge($pendingPOs)
            ->merge($pendingReturns)
            ->merge($unpaidInvoices)
            ->sortByDesc('date')
            ->take(5)
            ->values();
    @endphp

    <div class="page-header">
        <div>
            <div class="page-title">Overview</div>
            <div class="page-sub">Real-time inventory metrics and critical alerts.</div>
        </div>
        <div class="refresh-tag">↻ Last updated: Just now</div>
    </div>

    @if(session('success'))
        <div class="flash flash-success">{{ session('success') }}</div>
    @endif

    <div class="stats-row">
        <a href="{{ route('owner.items.index') }}" class="stat-card blue">
            <div class="stat-label">Total Items</div>
            <div class="stat-value">{{ number_format($totalItems) }}</div>
            <div class="stat-hint">All inventory items</div>
            <div class="stat-icon">📦</div>
        </a>
        <a href="{{ route('owner.items.index', ['status' => 'out_of_stock']) }}" class="stat-card red">
            <div class="stat-label">Out of Stock</div>
            <div class="stat-value" style="color: #e74c3c;">{{ number_format($outOfStock) }}</div>
            <div class="stat-hint">Zero quantity items</div>
            <div class="stat-icon">🚫</div>
        </a>
        <a href="{{ route('owner.items.index', ['status' => 'low_stock']) }}" class="stat-card orange">
            <div class="stat-label">Low Stock</div>
            <div class="stat-value" style="color: #e67e22;">{{ number_format($lowStock) }}</div>
            <div class="stat-hint">Requires attention</div>
            <div class="stat-icon">⚠️</div>
        </a>
        <a href="{{ route('owner.items.index', ['status' => 'expired']) }}" class="stat-card red">
            <div class="stat-label">Expired</div>
            <div class="stat-value" style="color: #e74c3c;">{{ number_format($expired) }}</div>
            <div class="stat-hint">Action required</div>
            <div class="stat-icon">🔴</div>
        </a>
        <a href="{{ route('owner.items.index', ['status' => 'near_expiry']) }}" class="stat-card yellow">
            <div class="stat-label">Expiring Soon</div>
            <div class="stat-value" style="color: #f39c12;">{{ number_format($expiringSoon) }}</div>
            <div class="stat-hint">Within 7 days</div>
            <div class="stat-icon">⏳</div>
        </a>
        <a href="{{ route('owner.items.index') }}" class="stat-card grey">
            <div class="stat-label">Direct Purchase</div>
            <div class="stat-value" style="color: #7f8c8d;">{{ number_format($directPurchase) }}</div>
            <div class="stat-hint">No assigned supplier</div>
            <div class="stat-icon">🏢</div>
        </a>
    </div>

    <div class="quick-actions">
        <a href="{{ route('owner.purchase-orders.create') }}" class="btn-qa btn-qa-primary">➕ Create Purchase Order</a>
        <a href="{{ route('owner.invoices.index') }}" class="btn-qa">📄 View Invoices</a>
        <a href="{{ route('owner.return-requests.index') }}" class="btn-qa">↩ View Return Requests</a>
        <a href="{{ route('owner.credit-notes.index') }}" class="btn-qa">💳 View Credit Notes</a>
    </div>

    <div class="critical-header">Critical Inventory Alerts</div>
    <div class="critical-grid">
        <div class="crit-card">
            <div class="crit-header orange">
                <div class="crit-title">Low Stock Items</div>
                <a href="{{ route('owner.items.index', ['status' => 'low_stock']) }}" class="view-all">View all</a>
            </div>
            <div class="crit-body">
                @forelse($lowStockItemsList as $item)
                    <div class="ls-item">
                        <div class="ls-icon">⚠️</div>
                        <div class="ls-info">
                            <div class="ls-name">{{ $item->name }}</div>
                            <div class="ls-sku">{{ optional($item->supplier)->name ?? 'Unknown supplier' }}</div>
                        </div>
                        <div class="ls-qty">{{ $item->quantity }} left</div>
                    </div>
                @empty
                    <div class="empty-state">No low stock items right now.</div>
                @endforelse
            </div>
        </div>
        <div class="crit-card">
            <div class="crit-header yellow">
                <div class="crit-title">Expiring Soon</div>
                <a href="{{ route('owner.items.index', ['status' => 'near_expiry']) }}" class="view-all">View all</a>
            </div>
            <div class="crit-body">
                @forelse($expiringSoonItemsList as $item)
                    <div class="ne-item">
                        <div class="ne-icon">⏳</div>
                        <div class="ne-info">
                            <div class="ne-name">{{ $item->name }}</div>
                            <div class="ne-batch">{{ optional($item->supplier)->name ?? 'Unknown supplier' }}</div>
                        </div>
                        <div>
                            <div class="ne-expiry">{{ $item->expiry_date?->format('Y-m-d') ?? 'Unknown' }}</div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">No items expiring soon.</div>
                @endforelse
            </div>
        </div>
        <div class="crit-card">
            <div class="crit-header blue">
                <div class="crit-title">Pending Updates</div>
                <a href="{{ route('owner.purchase-orders.index') }}" class="view-all">View all</a>
            </div>
            <div class="crit-body">
                @forelse($pendingUpdates as $entry)
                    <div class="ls-item">
                        <div class="pu-icon">📌</div>
                        <div class="ls-info">
                            <div class="ls-name">{{ $entry['type'] }}</div>
                            <div class="ls-sku">{{ $entry['desc'] }} • {{ $entry['ref'] }}</div>
                        </div>
                        <div class="pu-date">{{ \Carbon\Carbon::parse($entry['date'])->format('Y-m-d') }}</div>
                    </div>
                @empty
                    <div class="empty-state">No pending updates.</div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
