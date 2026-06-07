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
    .stats-row { display: grid; grid-template-columns: repeat(6, 1fr); gap: 16px; margin-bottom: 32px; }
    .stat-card { background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 1px 6px rgba(15,32,68,0.07); position: relative; }
    .stat-card.danger { background: #c0392b; color: #fff; }
    .stat-label { font-size: 0.65rem; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; color: #9daec5; }
    .stat-card.danger .stat-label { color: rgba(255,255,255,0.7); }
    .stat-value { font-size: 2rem; font-weight: 800; color: #0f2044; margin: 6px 0 4px; line-height: 1; }
    .stat-card.danger .stat-value { color: #fff; }
    .stat-hint { font-size: 0.72rem; color: #9daec5; }
    .stat-card.danger .stat-hint { color: rgba(255,255,255,0.75); }
    .stat-icon { position: absolute; top: 16px; right: 16px; opacity: 0.25; }
    .stat-card.danger .stat-icon { opacity: 0.6; color: #fff; }
    .stat-warn .stat-value { color: #d4870a; }
    .stat-expired .stat-value { color: #c0392b; }
    .critical-header { display: flex; align-items: center; gap: 8px; font-size: 1rem; font-weight: 700; color: #0f2044; margin-bottom: 16px; }
    .critical-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; }
    .crit-card { background: #fff; border-radius: 12px; box-shadow: 0 1px 6px rgba(15,32,68,0.07); overflow: hidden; }
    .crit-header { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px 12px; border-bottom: 1px solid #f0f4f8; }
    .crit-title { font-size: 0.88rem; font-weight: 700; color: #0f2044; }
    .view-all { font-size: 0.78rem; color: #3a7bd5; text-decoration: none; font-weight: 600; }
    .view-all:hover { text-decoration: underline; }
    .ls-item, .ne-item { display: flex; align-items: center; gap: 14px; padding: 12px 20px; border-bottom: 1px solid #f4f6fb; }
    .ls-item:last-child, .ne-item:last-child { border-bottom: none; }
    .ls-icon, .ne-icon { width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1rem; flex-shrink: 0; }
    .ls-icon { background: #fef3e2; }
    .ne-icon { background: #fdedec; color: #e74c3c; }
    .ls-info, .ne-info { flex: 1; }
    .ls-name, .ne-name { font-size: 0.85rem; font-weight: 600; color: #0f2044; }
    .ls-sku, .ne-batch { font-size: 0.72rem; color: #9daec5; }
    .ls-qty { font-size: 0.82rem; font-weight: 700; color: #d4870a; margin-right: 4px; white-space: nowrap; }
    .ne-expiry { font-size: 0.78rem; font-weight: 700; color: #e74c3c; margin-right: 8px; white-space: nowrap; }
    .ne-qty { font-size: 0.72rem; color: #9daec5; white-space: nowrap; display: block; margin-top: 2px; }
    .btn-reorder, .btn-expire { display: inline-flex; align-items: center; justify-content: center; border-radius: 6px; padding: 6px 14px; font-size: 0.78rem; font-weight: 600; cursor: pointer; font-family: 'Inter', sans-serif; white-space: nowrap; transition: background 0.15s; text-decoration: none; }
    .btn-reorder { background: #0f2044; color: #fff; }
    .btn-reorder:hover { background: #182e5e; }
    .btn-expire { background: #fff; color: #c0392b; border: 1px solid #f5b7b1; }
    .btn-expire:hover { background: #fdedec; }
    .flash { padding: 12px 20px; border-radius: 8px; font-size: 0.88rem; margin-bottom: 20px; }
    .flash-success { background: #e8f8f0; color: #1d8348; border: 1px solid #a9dfbf; }
    .empty-state { padding: 24px; text-align: center; color: #9daec5; font-size: 0.85rem; }
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
        use App\Models\Setting;

        $warningDays = Setting::get('expiry_warning_days', 7);
        $totalItems  = Item::count();
        $activePOs   = PurchaseOrder::where('status','Pending')->count();
        $lowStockItems = Item::whereColumn('quantity','<=','reorder_point')->get();
        $lowStockCount = $lowStockItems->count();
        $expiredCount  = Item::whereNotNull('expiry_date')->whereDate('expiry_date','<',now())->count();
        $highRisk      = Item::whereColumn('quantity','<=','reorder_point')
                             ->whereNotNull('expiry_date')
                             ->whereDate('expiry_date','<',now())
                             ->count();
        $nearExpiryItems = Item::whereNotNull('expiry_date')
                               ->whereDate('expiry_date','<=', now()->addDays($warningDays))
                               ->whereDate('expiry_date','>=',now())
                               ->get();

        $pendingUpdateItems = PurchaseOrder::with(['item', 'supplier'])
            ->whereIn('status', ['Received', 'received'])
            ->where('updated_at', '>=', now()->subDays(7))
            ->whereHas('item', function ($q) {
                $q->whereNull('expiry_date')
                  ->orWhereColumn('items.updated_at', '<', 'purchase_orders.updated_at');
            })
            ->get()
            ->map(fn($po) => [
                'item'          => $po->item,
                'supplier'      => $po->supplier,
                'received_date' => $po->updated_at,
                'po_number'     => $po->po_number,
            ])
            ->unique(fn($entry) => optional($entry['item'])->id)
            ->filter(fn($entry) => $entry['item'] !== null)
            ->values();

        $pendingUpdateCount = $pendingUpdateItems->count();
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
        <div class="stat-card">
            <div class="stat-label">Total Items</div>
            <div class="stat-value">{{ number_format($totalItems) }}</div>
            <div class="stat-hint">Active inventory items</div>
            <div class="stat-icon"><svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg></div>
        </div>
        <div class="stat-card stat-warn">
            <div class="stat-label">Low Stock</div>
            <div class="stat-value">{{ $lowStockCount }}</div>
            <div class="stat-hint">Requires attention</div>
            <div class="stat-icon">⚠️</div>
        </div>
        <div class="stat-card stat-expired">
            <div class="stat-label">Expired</div>
            <div class="stat-value">{{ $expiredCount }}</div>
            <div class="stat-hint">→ Action required</div>
            <div class="stat-icon">🔴</div>
        </div>
    </div>

    <div class="critical-header">Critical Inventory Alerts</div>
    <div class="critical-grid">
        <div class="crit-card">
            <div class="crit-header">
                <div class="crit-title">Low stock items</div>
                <a href="{{ route('owner.items.index', ['status' => 'low_stock']) }}" class="view-all">View all</a>
            </div>
            @forelse($lowStockItems->take(3) as $item)
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
        <div class="crit-card">
            <div class="crit-header">
                <div class="crit-title">Expiring soon</div>
                <a href="{{ route('owner.items.index', ['status' => 'near_expiry']) }}" class="view-all">View all</a>
            </div>
            @forelse($nearExpiryItems->take(3) as $item)
                <div class="ne-item">
                    <div class="ne-icon">⏳</div>
                    <div class="ne-info">
                        <div class="ne-name">{{ $item->name }}</div>
                        <div class="ne-batch">{{ optional($item->supplier)->name ?? 'Unknown supplier' }}</div>
                    </div>
                    <div>
                        <div class="ne-expiry">{{ $item->expiry_date?->format('Y-m-d') ?? 'Unknown' }}</div>
                        <div class="ne-qty">{{ $item->quantity }} left</div>
                    </div>
                </div>
            @empty
                <div class="empty-state">No items expiring soon.</div>
            @endforelse
        </div>
        <div class="crit-card">
            <div class="crit-header">
                <div class="crit-title">Recent updates</div>
                <a href="{{ route('owner.purchase-orders.index') }}" class="view-all">View POs</a>
            </div>
            @forelse($pendingUpdateItems->take(3) as $entry)
                <div class="ls-item">
                    <div class="ls-icon">📌</div>
                    <div class="ls-info">
                        <div class="ls-name">{{ optional($entry['item'])->name ?? 'Unknown item' }}</div>
                        <div class="ls-sku">Updated by {{ optional($entry['supplier'])->name ?? 'supplier' }}</div>
                    </div>
                    <div class="ls-qty">{{ $entry['received_date']->diffForHumans() }}</div>
                </div>
            @empty
                <div class="empty-state">No recent updates.</div>
            @endforelse
        </div>
    </div>
@endsection
