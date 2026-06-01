<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>New Supplier – 22UniMart</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: #f8fafc; display: flex; min-height: 100vh; color: #1a2744; }

        /* Sidebar Standard */
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
        .sidebar-link { display: flex; align-items: center; gap: 10px; color: #8ca0c0; font-size: 0.82rem; text-decoration: none; padding: 6px 0; }
        .btn-report { background: #c0392b; color: #fff; border: none; border-radius: 8px; padding: 12px 16px; font-size: 0.85rem; font-weight: 600; cursor: pointer; width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px; transition: background 0.15s; }
        .btn-report:hover { background: #a93226; }

        .main { margin-left: 210px; flex: 1; display: flex; flex-direction: column; }
        
        /* Topbar Breadcrumb Style */
        .topbar { background: #fff; padding: 0 32px; height: 56px; display: flex; align-items: center; gap: 16px; position: sticky; top: 0; z-index: 10; border-bottom: 1px solid #e2e8f0; }
        .breadcrumbs { font-size: 0.75rem; font-weight: 700; color: #7a8fa8; letter-spacing: 1px; text-transform: uppercase; }
        .breadcrumbs a { color: inherit; text-decoration: none; }
        .breadcrumbs a:hover { color: #0f2044; }
        .topbar-right { margin-left: auto; display: flex; align-items: center; gap: 20px; }
        .avatar { width: 32px; height: 32px; border-radius: 50%; background: #0f2044; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 0.75rem; font-weight: 700; }

        .content { padding: 40px; max-width: 800px; }
        .page-title { font-size: 1.8rem; font-weight: 800; color: #0f2044; margin-bottom: 8px; }
        .page-sub { font-size: 0.9rem; color: #5a6a85; line-height: 1.5; margin-bottom: 32px; max-width: 600px; }

        .form-card { background: #fff; border-radius: 12px; padding: 32px; margin-bottom: 24px; box-shadow: 0 1px 15px rgba(15,32,68,0.03); }
        
        .card-header { display: flex; align-items: center; gap: 12px; margin-bottom: 24px; }
        .card-line { width: 24px; height: 3px; background: #0f2044; border-radius: 2px; }
        .card-title { font-size: 1.05rem; font-weight: 700; color: #0f2044; }

        .form-row { display: flex; gap: 24px; margin-bottom: 20px; flex-wrap: wrap; }
        .form-group { flex: 1; min-width: 240px; display: flex; flex-direction: column; gap: 8px; }
        .form-label { font-size: 0.75rem; font-weight: 700; color: #5a6a85; text-transform: uppercase; letter-spacing: 0.5px; }
        .form-control { background: #f4f6fb; border: 1px solid transparent; border-radius: 6px; padding: 12px 16px; font-size: 0.9rem; font-family: 'Inter', sans-serif; color: #1a2744; transition: all 0.2s; outline: none; }
        .form-control:focus { background: #fff; border-color: #4a90d9; box-shadow: 0 0 0 3px rgba(74, 144, 217, 0.1); }
        .form-control::placeholder { color: #9daec5; }
        .form-text { font-size: 0.75rem; color: #7a8fa8; margin-top: 4px; }

        .error-msg { font-size: 0.75rem; color: #c0392b; margin-top: 4px; font-weight: 500; }

        .form-actions { display: flex; align-items: center; gap: 16px; margin-top: 20px; }
        .btn-submit { background: #0f2044; color: #fff; border: none; border-radius: 6px; padding: 12px 24px; font-size: 0.9rem; font-weight: 600; cursor: pointer; transition: background 0.15s; font-family: 'Inter', sans-serif; }
        .btn-submit:hover { background: #182e5e; }
        .btn-discard { background: #e2e8f0; color: #3a4d6a; border: none; border-radius: 6px; padding: 12px 24px; font-size: 0.9rem; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-flex; transition: background 0.15s; }
        .btn-discard:hover { background: #cbd5e1; }
    </style>
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

<div class="main">
    <div class="topbar">
        <div class="breadcrumbs">
            <a href="{{ route('owner.suppliers.index') }}">Suppliers</a> › <span style="color:#0f2044;">New Supplier</span>
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
        <div class="page-title">Add New Supplier</div>
        <div class="page-sub">Register a new sourcing partner to the 22UniMart network. Ensure all contact data points are accurate for automated communication and portal access.</div>

        <form action="{{ route('owner.suppliers.store') }}" method="POST">
            @csrf

            <!-- Basic Info Card -->
            <div class="form-card">
                <div class="card-header">
                    <div class="card-line"></div>
                    <div class="card-title">Basic Info</div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="name">Company Name</label>
                        <input type="text" id="name" name="name" class="form-control" placeholder="e.g. Global Tech Logistics" value="{{ old('name') }}" required>
                        @error('name')<div class="error-msg">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="contact_person">Contact Person</label>
                        <input type="text" id="contact_person" name="contact_person" class="form-control" placeholder="Jane Supplier" value="{{ old('contact_person') }}">
                        @error('contact_person')<div class="error-msg">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="contact_phone">Phone</label>
                        <input type="text" id="contact_phone" name="contact_phone" class="form-control" placeholder="+60123456789" value="{{ old('contact_phone') }}">
                        <div class="form-text">This phone number is used for supplier WhatsApp notifications.</div>
                        @error('contact_phone')<div class="error-msg">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="contact_email">Email</label>
                        <input type="email" id="contact_email" name="contact_email" class="form-control" placeholder="corporate@supplier.com" value="{{ old('contact_email') }}" required>
                        @error('contact_email')<div class="error-msg">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <!-- Address Card -->
            <div class="form-card">
                <div class="card-header">
                    <div class="card-line"></div>
                    <div class="card-title">Address</div>
                </div>

                <div class="form-row">
                    <div class="form-group" style="flex:1;">
                        <label class="form-label" for="address_line_1">Address</label>
                        <input type="text" id="address_line_1" name="address_line_1" class="form-control" placeholder="123 Supply Street" value="{{ old('address_line_1') }}">
                        @error('address_line_1')<div class="error-msg">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="city">City</label>
                        <input type="text" id="city" name="city" class="form-control" placeholder="Kuala Lumpur" value="{{ old('city') }}">
                        @error('city')<div class="error-msg">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="state">State</label>
                        <input type="text" id="state" name="state" class="form-control" placeholder="WP Kuala Lumpur" value="{{ old('state') }}">
                        @error('state')<div class="error-msg">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="postal_code">Postal Code</label>
                        <input type="text" id="postal_code" name="postal_code" class="form-control" placeholder="50000" value="{{ old('postal_code') }}">
                        @error('postal_code')<div class="error-msg">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="country">Country</label>
                        <input type="text" id="country" name="country" class="form-control" placeholder="Malaysia" value="{{ old('country') }}">
                        @error('country')<div class="error-msg">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <!-- Portal Access Card -->
            <div class="form-card">
                <div class="card-header">
                    <div class="card-line"></div>
                    <div class="card-title">Portal Access</div>
                </div>

                <div class="form-row">
                    <div class="form-group" style="flex:1;">
                        <label class="form-label" for="portal_enabled">Enable Portal</label>
                        <div style="display:flex;align-items:center;gap:10px;background:#f4f6fb;border-radius:8px;padding:14px 16px;">
                            <input type="checkbox" id="portal_enabled" name="portal_enabled" value="1" {{ old('portal_enabled', true) ? 'checked' : '' }}>
                            <div>
                                <div style="font-size:0.9rem;font-weight:600;color:#0f2044;">Enable supplier portal access</div>
                                <div class="form-text">When enabled, the supplier receives portal access and a WhatsApp invite.</div>
                            </div>
                        </div>
                        @error('portal_enabled')<div class="error-msg">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div id="portal-password-section" class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="password">Password</label>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Minimum 8 characters">
                        <div class="form-text">Leave blank to auto-generate a secure temporary password if portal access is enabled.</div>
                        @error('password')<div class="error-msg">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="password_confirmation">Confirm Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Re-type password">
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('owner.suppliers.index') }}" class="btn-discard">🗑 Discard Draft</a>
                <button type="submit" class="btn-submit">Register Supplier</button>
            </div>
        </form>
    </div>
</div>

<script>
    (function () {
        const portalToggle = document.getElementById('portal_enabled');
        const passwordSection = document.getElementById('portal-password-section');
        const passwordField = document.getElementById('password');
        const confirmField = document.getElementById('password_confirmation');

        function syncPortalAccess() {
            const enabled = portalToggle.checked;
            passwordSection.style.display = enabled ? 'flex' : 'none';
            passwordField.disabled = !enabled;
            confirmField.disabled = !enabled;

            if (!enabled) {
                passwordField.value = '';
                confirmField.value = '';
            }
        }

        syncPortalAccess();
        portalToggle.addEventListener('change', syncPortalAccess);
    })();
</script>
</body>
</html>
