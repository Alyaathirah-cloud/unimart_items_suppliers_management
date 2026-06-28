<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Supplier Portal')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: #eef2f7; display: flex; min-height: 100vh; color: #1a2744; }

        /* Sidebar styles */
        .sidebar { width: 210px; flex-shrink: 0; background: #0f2044; color: #fff; display: flex; flex-direction: column; padding: 0 0 24px 0; position: fixed; top: 0; left: 0; height: 100vh; }
        .sidebar-brand { padding: 24px 20px 8px; }
        .sidebar-brand .brand-name { font-size: 1rem; font-weight: 800; letter-spacing: 0.5px; }
        .sidebar-brand .brand-sub  { font-size: 0.7rem; color: #8ca0c0; margin-top: 2px; }
        .sidebar-nav { flex: 1; margin-top: 20px; }
        .nav-item { display: flex; align-items: center; gap: 12px; padding: 11px 20px; font-size: 0.88rem; font-weight: 500; color: #8ca0c0; text-decoration: none; border-left: 3px solid transparent; transition: all 0.15s; }
        .nav-item:hover { color: #fff; background: rgba(255,255,255,0.06); }
        .nav-item.active { color: #fff; background: rgba(255,255,255,0.1); border-left-color: #4a90d9; }
        .nav-icon { width: 18px; text-align: center; }
        .sidebar-bottom { padding: 0 20px; display: flex; flex-direction: column; gap: 8px; }
        .btn-report { background: #1e3a6e; color: #fff; border: none; border-radius: 8px; padding: 12px 16px; font-size: 0.85rem; font-weight: 600; cursor: pointer; width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px; transition: background 0.15s; }
        .btn-report:hover { background: #2a4f8f; }
        .sidebar-link { display: flex; align-items: center; gap: 10px; color: #8ca0c0; font-size: 0.82rem; text-decoration: none; padding: 6px 0; }
        .sidebar-link:hover { color: #fff; }

        /* Main layout */
        .main { margin-left: 210px; flex: 1; display: flex; flex-direction: column; }
        .topbar { background: #fff; border-bottom: 1px solid #e2e8f0; padding: 0 32px; height: 56px; display: flex; align-items: center; position: sticky; top: 0; z-index: 10; }
        .topbar-right { margin-left: auto; display: flex; align-items: center; gap: 20px; }
        
        .avatar { width: 36px; height: 36px; border-radius: 50%; background: #0f2044; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 0.85rem; font-weight: 700; }
        .topbar-profile { display: flex; align-items: center; gap: 12px; cursor: pointer; }
        .profile-name { font-size: 0.85rem; font-weight: 600; color: #1a2744; }
        .profile-role { font-size: 0.72rem; color: #9daec5; }
        
        .content { padding: 32px; width: 100%; }
        
        .breadcrumbs-container { margin-right: auto; }
    </style>
    @stack('styles')
</head>
<body>

@php
    // Automatically determine active state based on route
    $active = $active ?? '';
    if (empty($active)) {
        if (request()->routeIs('supplier.dashboard')) $active = 'dashboard';
        elseif (request()->routeIs('supplier.purchase-orders.*')) $active = 'purchase_orders';
        elseif (request()->routeIs('supplier.returns.*')) $active = 'returns';
        elseif (request()->routeIs('supplier.invoices.*')) $active = 'invoices';
        elseif (request()->routeIs('supplier.credit-notes.*')) $active = 'credit_notes';
        elseif (request()->routeIs('supplier.notifications.*')) $active = 'notifications';
    }
@endphp

@include('supplier.partials.sidebar', ['active' => $active])

<div class="main">
    <div class="topbar">
        @hasSection('topbar')
            @yield('topbar')
        @else
            @hasSection('breadcrumbs')
                <div class="breadcrumbs-container">
                    @yield('breadcrumbs')
                </div>
            @endif
            <div class="topbar-right">
                <a href="{{ route('supplier.notifications.index') }}" style="text-decoration:none;position:relative;">
                    <span style="font-size:1.2rem;">🔔</span>
                    @if(auth()->check() && auth()->user()->unreadNotifications->count() > 0)
                        <span style="position:absolute;top:-2px;right:-2px;background:#e74c3c;color:#fff;font-size:0.6rem;font-weight:700;padding:2px 5px;border-radius:10px;">
                            {{ auth()->user()->unreadNotifications->count() }}
                        </span>
                    @endif
                </a>
                
                @include('supplier.components.topbar-profile')
            </div>
        @endif
    </div>

    <div class="content">
        @yield('content')
    </div>
</div>

@stack('scripts')
</body>
</html>
