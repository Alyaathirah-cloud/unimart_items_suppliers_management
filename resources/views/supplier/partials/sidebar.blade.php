<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="brand-name">22UNIMART</div>
        <div class="brand-sub">Supplier Portal</div>
    </div>
    <nav class="sidebar-nav">
        <a href="{{ route('supplier.dashboard') }}" class="nav-item {{ ($active ?? '') === 'dashboard' ? 'active' : '' }}">
            <span class="nav-icon">⊞</span> Dashboard
        </a>
        <a href="{{ route('supplier.purchase-orders.index') }}" class="nav-item {{ ($active ?? '') === 'purchase_orders' ? 'active' : '' }}">
            <span class="nav-icon">🛒</span> Purchase Orders
        </a>
        <a href="{{ route('supplier.returns.index') }}" class="nav-item {{ ($active ?? '') === 'returns' ? 'active' : '' }}">
            <span class="nav-icon">↩</span> Return Requests
        </a>
        <a href="{{ route('supplier.invoices.index') }}" class="nav-item {{ ($active ?? '') === 'invoices' ? 'active' : '' }}">
            <span class="nav-icon">🧾</span> Invoices
        </a>
        <a href="{{ route('supplier.credit-notes.index') }}" class="nav-item {{ ($active ?? '') === 'credit_notes' ? 'active' : '' }}">
            <span class="nav-icon">📋</span> Credit Notes
        </a>
        <a href="{{ route('supplier.notifications.index') }}" class="nav-item {{ ($active ?? '') === 'notifications' ? 'active' : '' }}">
            <span class="nav-icon">🔔</span> Notifications
        </a>
    </nav>
    <div class="sidebar-bottom">
        <div class="sidebar-link" style="color: #fff; cursor: default; font-weight: bold;">Role: Supplier</div>
        <form action="{{ route('logout') }}" method="POST" style="margin: 0; width: 100%;">
            @csrf
            <button type="submit" class="btn-report" style="background: #c0392b;">Logout</button>
        </form>
    </div>
</aside>
