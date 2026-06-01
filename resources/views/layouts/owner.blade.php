<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', '22UniMart Owner')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: #eef2f7; color: #1a2744; display: flex; min-height: 100vh; }

        .sidebar { width: 230px; flex-shrink: 0; background: #0f2044; color: #fff; display: flex; flex-direction: column; padding: 0 0 24px 0; position: fixed; top: 0; left: 0; height: 100vh; }
        .sidebar-brand { padding: 20px 20px 4px; display: flex; align-items: center; gap: 12px; }
        .brand-square { width: 32px; height: 32px; background: #fff; color: #0f2044; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 0.9rem; font-weight: 800; flex-shrink: 0; }
        .brand-text .brand-name { font-size: 0.9rem; font-weight: 800; }
        .brand-text .brand-sub { font-size: 0.68rem; color: #8ca0c0; }
        .sidebar-nav { flex: 1; margin-top: 20px; }
        .nav-item { display: flex; align-items: center; gap: 12px; padding: 11px 20px; font-size: 0.88rem; font-weight: 500; color: #8ca0c0; cursor: pointer; text-decoration: none; transition: all 0.15s; border-left: 3px solid transparent; }
        .nav-item:hover { color: #fff; background: rgba(255,255,255,0.06); }
        .nav-item.active { color: #fff; background: rgba(255,255,255,0.1); border-left-color: #4a90d9; }
        .nav-icon { width: 18px; text-align: center; flex-shrink: 0; }
        .sidebar-bottom { padding: 0 20px; display: flex; flex-direction: column; gap: 8px; }
        .sidebar-link { display: flex; align-items: center; gap: 10px; color: #8ca0c0; font-size: 0.82rem; text-decoration: none; padding: 6px 0; }
        .btn-report { background: #1e3a6e; color: #fff; border: none; border-radius: 8px; padding: 12px 16px; font-size: 0.85rem; font-weight: 600; cursor: pointer; width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px; transition: background 0.15s; }
        .btn-report:hover { background: #2a4f8f; }

        .main { margin-left: 230px; flex: 1; display: flex; flex-direction: column; }
        .topbar { background: #fff; border-bottom: 1px solid #e2e8f0; padding: 0 32px; height: 56px; display: flex; align-items: center; gap: 16px; position: sticky; top: 0; z-index: 10; }
        .breadcrumbs { font-size: 0.75rem; font-weight: 700; color: #7a8fa8; letter-spacing: 1px; text-transform: uppercase; }
        .breadcrumbs a { color: inherit; text-decoration: none; }
        .breadcrumbs a:hover { color: #0f2044; }
        .topbar-right { margin-left: auto; display: flex; align-items: center; gap: 20px; }
        .icon-btn { background: none; border: none; cursor: pointer; color: #5a6a85; font-size: 1.1rem; }
        .topbar-profile { position: relative; cursor: pointer; display: flex; align-items: center; gap: 8px; }
        .avatar { width: 32px; height: 32px; border-radius: 50%; background: #0f2044; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 0.75rem; font-weight: 700; }
        .profile-dropdown { position: absolute; top: 100%; right: 0; background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; box-shadow: 0 4px 16px rgba(15,32,68,0.12); min-width: 200px; margin-top: 8px; display: none; z-index: 1000; }
        .profile-dropdown.show { display: block; }
        .dropdown-item { display: flex; align-items: center; gap: 10px; padding: 12px 16px; font-size: 0.85rem; color: #334155; text-decoration: none; border-bottom: 1px solid #f1f5f9; transition: background 0.15s; }
        .dropdown-item:last-child { border-bottom: none; }
        .dropdown-item:hover { background: #f8fafc; color: #0f2044; }
        .dropdown-item.logout { color: #c0392b; }
        .dropdown-item.logout:hover { background: #fee2e2; }

        .content { padding: 32px; }
    </style>
    @stack('styles')
</head>
<body>
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
        <a href="{{ route('owner.credit-notes.index') }}" class="nav-item {{ request()->routeIs('owner.credit-notes.*') ? 'active' : '' }}"><span class="nav-icon">📋</span> Credit Notes</a>
        <a href="{{ route('owner.invoices.index') }}" class="nav-item {{ request()->routeIs('owner.invoices.*') ? 'active' : '' }}"><span class="nav-icon">📄</span> Invoices</a>
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
<div class="main">
    <div class="topbar">
        @hasSection('topbar')
            @yield('topbar')
        @else
            <div class="breadcrumbs">
                @yield('breadcrumbs')
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
        @endif
    </div>
    <div class="content">
        @yield('content')
    </div>
</div>
@stack('scripts')
<script>
function toggleProfileDropdown() {
    const dropdown = document.getElementById('profileDropdown');
    dropdown.classList.toggle('show');
}

document.addEventListener('click', function(event) {
    const profile = event.target.closest('.topbar-profile');
    const dropdown = document.getElementById('profileDropdown');
    if (!profile && dropdown && !dropdown.contains(event.target)) {
        dropdown.classList.remove('show');
    }
});
</script>
</body>
</html>
