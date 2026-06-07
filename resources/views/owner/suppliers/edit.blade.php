<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Supplier – 22UniMart</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: #f8fafc; display: flex; min-height: 100vh; color: #1a2744; }

        /* Sidebar */
        .sidebar { width: 230px; flex-shrink: 0; background: #0f2044; color: #fff; display: flex; flex-direction: column; padding: 0 0 24px 0; position: fixed; top: 0; left: 0; height: 100vh; }
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
        .btn-report { background: #c0392b; color: #fff; border: none; border-radius: 8px; padding: 12px 16px; font-size: 0.85rem; font-weight: 600; cursor: pointer; width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px; transition: background 0.15s; }
        .btn-report:hover { background: #a93226; }

        /* Main */
        .main { margin-left: 230px; flex: 1; display: flex; flex-direction: column; }

        /* Topbar */
        .topbar { background: #fff; padding: 0 32px; height: 56px; display: flex; align-items: center; gap: 16px; position: sticky; top: 0; z-index: 10; border-bottom: 1px solid #e2e8f0; }
        .breadcrumbs { font-size: 0.75rem; font-weight: 700; color: #7a8fa8; letter-spacing: 1px; text-transform: uppercase; }
        .breadcrumbs a { color: inherit; text-decoration: none; }
        .breadcrumbs a:hover { color: #0f2044; }
        .topbar-right { margin-left: auto; display: flex; align-items: center; gap: 20px; }
        .avatar { width: 32px; height: 32px; border-radius: 50%; background: #0f2044; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 0.75rem; font-weight: 700; }
        .topbar-profile { position: relative; cursor: pointer; display: flex; align-items: center; gap: 8px; }
        .profile-dropdown { position: absolute; top: 100%; right: 0; background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; box-shadow: 0 4px 16px rgba(15,32,68,0.12); min-width: 200px; margin-top: 8px; display: none; z-index: 1000; }
        .profile-dropdown.show { display: block; }
        .dropdown-item { display: flex; align-items: center; gap: 10px; padding: 12px 16px; font-size: 0.85rem; color: #334155; text-decoration: none; border-bottom: 1px solid #f1f5f9; transition: background 0.15s; }
        .dropdown-item:last-child { border-bottom: none; }
        .dropdown-item:hover { background: #f8fafc; color: #0f2044; }
        .dropdown-item.logout { color: #c0392b; }
        .dropdown-item.logout:hover { background: #fee2e2; }
        .icon-btn { background: none; border: none; cursor: pointer; color: #5a6a85; font-size: 1.1rem; }

        /* Content */
        .content { padding: 40px; max-width: 820px; }
        .page-title { font-size: 1.8rem; font-weight: 800; color: #0f2044; margin-bottom: 8px; }
        .page-sub { font-size: 0.9rem; color: #5a6a85; line-height: 1.5; margin-bottom: 32px; max-width: 600px; }

        /* Cards */
        .form-card { background: #fff; border-radius: 12px; padding: 32px; margin-bottom: 24px; box-shadow: 0 1px 15px rgba(15,32,68,0.06); border: 1px solid #f0f4f8; }
        .card-header { display: flex; align-items: center; gap: 12px; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 1px solid #f0f4f8; }
        .card-line { width: 4px; height: 22px; background: linear-gradient(180deg, #0f2044, #4a90d9); border-radius: 2px; }
        .card-title { font-size: 1.05rem; font-weight: 700; color: #0f2044; }

        /* Form elements */
        .form-row { display: flex; gap: 24px; margin-bottom: 20px; flex-wrap: wrap; }
        .form-group { flex: 1; min-width: 220px; display: flex; flex-direction: column; gap: 6px; }
        .form-label { font-size: 0.72rem; font-weight: 700; color: #5a6a85; text-transform: uppercase; letter-spacing: 0.6px; }
        .form-control { background: #f4f6fb; border: 1.5px solid transparent; border-radius: 8px; padding: 12px 16px; font-size: 0.9rem; font-family: 'Inter', sans-serif; color: #1a2744; transition: all 0.2s; outline: none; width: 100%; }
        .form-control:focus { background: #fff; border-color: #4a90d9; box-shadow: 0 0 0 3px rgba(74,144,217,0.1); }
        .form-control::placeholder { color: #9daec5; }
        .form-text { font-size: 0.72rem; color: #7a8fa8; margin-top: 3px; }
        .error-msg { font-size: 0.72rem; color: #c0392b; margin-top: 3px; font-weight: 600; }

        /* Portal info rows */
        .info-grid { display: grid; gap: 12px; margin-bottom: 16px; }
        .info-row { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 14px 16px; background: #f4f6fb; border-radius: 10px; }
        .info-label { font-size: 0.88rem; font-weight: 700; color: #0f2044; }
        .info-value { font-size: 0.88rem; color: #334155; word-break: break-all; }
        .copy-btn { border: 1px solid #e2e8f0; background: #fff; color: #0f2044; border-radius: 8px; padding: 7px 12px; cursor: pointer; font-size: 0.78rem; font-family: 'Inter', sans-serif; transition: background 0.15s; }
        .copy-btn:hover { background: #f1f5f9; }
        .status-pill { display: inline-flex; align-items: center; gap: 6px; padding: 5px 10px; border-radius: 999px; font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
        .status-pill.sent { background: #d1fae5; color: #065f46; }
        .status-pill.pending { background: #fef3c7; color: #92400e; }
        .status-pill.failed { background: #fee2e2; color: #991b1b; }
        .status-pill.missing { background: #e2e8f0; color: #334155; }
        .status-pill.disabled { background: #e2e8f0; color: #64748b; }

        /* Actions */
        .form-actions { display: flex; align-items: center; gap: 16px; margin-top: 24px; padding-top: 20px; border-top: 1px solid #f0f4f8; }
        .btn-submit { background: linear-gradient(135deg, #0f2044, #1e3a6e); color: #fff; border: none; border-radius: 8px; padding: 12px 28px; font-size: 0.9rem; font-weight: 700; cursor: pointer; transition: all 0.2s; font-family: 'Inter', sans-serif; }
        .btn-submit:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(15,32,68,0.3); }
        .btn-discard { background: #f4f6fb; color: #3a4d6a; border: 1.5px solid #e2e8f0; border-radius: 8px; padding: 12px 22px; font-size: 0.9rem; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; transition: all 0.15s; }
        .btn-discard:hover { background: #e8edf5; }

        /* Flash */
        .flash { padding: 13px 18px; border-radius: 8px; font-size: 0.88rem; margin-bottom: 20px; font-weight: 500; display: flex; align-items: center; gap: 10px; }
        .flash-success { background: #e8f8f0; color: #1d8348; border: 1px solid #a9dfbf; }
        .flash-error   { background: #fdedec; color: #c0392b; border: 1px solid #f5b7b1; }
        .flash-warning { background: #fff8e1; color: #856404; border: 1px solid #ffc107; }
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
        <a href="{{ route('owner.credit-notes.index') }}" class="nav-item {{ request()->routeIs('owner.credit-notes.*') ? 'active' : '' }}"><span class="nav-icon">📋</span> Credit Notes</a>
        <a href="{{ route('owner.invoices.index') }}" class="nav-item {{ request()->routeIs('owner.invoices.*') ? 'active' : '' }}"><span class="nav-icon">📄</span> Invoices</a>
        <a href="{{ route('owner.notifications.index') }}" class="nav-item {{ request()->routeIs('owner.notifications.*') ? 'active' : '' }}"><span class="nav-icon">🔔</span> Notifications</a>
    </nav>
    <div class="sidebar-bottom">
        <div style="color:#fff;cursor:default;font-weight:bold;font-size:0.82rem;">Role: Owner</div>
        <form action="{{ route('logout') }}" method="POST" style="margin:0;width:100%;">
            @csrf
            <button type="submit" class="btn-report">Logout</button>
        </form>
    </div>
</aside>

<div class="main">
    <div class="topbar">
        <div class="breadcrumbs">
            <a href="{{ route('owner.suppliers.index') }}">Suppliers</a> › <span style="color:#0f2044;">Edit Supplier</span>
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
    </div>

    <div class="content">
        <div class="page-title">Edit Supplier</div>
        <div class="page-sub">Update contact information for <strong>{{ $supplier->name }}</strong>. Changes will be reflected immediately across all linked records.</div>

        @if(session('success'))
            <div class="flash flash-success">✅ {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="flash flash-error">❌ {{ session('error') }}</div>
        @endif
        @if(session('warning'))
            <div class="flash flash-warning">⚠️ {!! nl2br(e(session('warning'))) !!}</div>
        @endif

        <form action="{{ route('owner.suppliers.update', $supplier) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Basic Information --}}
            <div class="form-card">
                <div class="card-header">
                    <div class="card-line"></div>
                    <div class="card-title">Basic Information</div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="name">Company Name</label>
                        <input type="text" id="name" name="name" class="form-control" placeholder="e.g. Global Tech Logistics"
                               value="{{ old('name', $supplier->name) }}" required>
                        @error('name')<div class="error-msg">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="contact_person">Contact Person</label>
                        <input type="text" id="contact_person" name="contact_person" class="form-control" placeholder="Jane Supplier"
                               value="{{ old('contact_person', $supplier->contact_person) }}">
                        @error('contact_person')<div class="error-msg">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="contact_email">Email Address</label>
                        <input type="email" id="contact_email" name="contact_email" class="form-control" placeholder="corporate@supplier.com"
                               value="{{ old('contact_email', $supplier->contact_email) }}" required>
                        @error('contact_email')<div class="error-msg">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="contact_phone">Phone Number</label>
                        <input type="text" id="contact_phone" name="contact_phone" class="form-control" placeholder="+60123456789"
                               value="{{ old('contact_phone', $supplier->contact_phone) }}">
                        <div class="form-text">Used for WhatsApp notifications.</div>
                        @error('contact_phone')<div class="error-msg">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            {{-- Address Information --}}
            <div class="form-card">
                <div class="card-header">
                    <div class="card-line"></div>
                    <div class="card-title">Address Information</div>
                </div>

                <div class="form-row">
                    <div class="form-group" style="flex:1;">
                        <label class="form-label" for="address_line_1">Address Line 1</label>
                        <input type="text" id="address_line_1" name="address_line_1" class="form-control" placeholder="123 Supply Street"
                               value="{{ old('address_line_1', $supplier->address_line_1) }}">
                        @error('address_line_1')<div class="error-msg">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group" style="flex:1;">
                        <label class="form-label" for="address_line_2">Address Line 2 <span style="font-weight:400;text-transform:none;letter-spacing:0;">(optional)</span></label>
                        <input type="text" id="address_line_2" name="address_line_2" class="form-control" placeholder="Suite 9, Floor 2"
                               value="{{ old('address_line_2', $supplier->address_line_2) }}">
                        @error('address_line_2')<div class="error-msg">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="city">City</label>
                        <input type="text" id="city" name="city" class="form-control" placeholder="Kuala Lumpur"
                               value="{{ old('city', $supplier->city) }}">
                        @error('city')<div class="error-msg">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="state">State</label>
                        <input type="text" id="state" name="state" class="form-control" placeholder="WP Kuala Lumpur"
                               value="{{ old('state', $supplier->state) }}">
                        @error('state')<div class="error-msg">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="postal_code">Postal Code</label>
                        <input type="text" id="postal_code" name="postal_code" class="form-control" placeholder="50000"
                               value="{{ old('postal_code', $supplier->postal_code) }}">
                        @error('postal_code')<div class="error-msg">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="country">Country</label>
                        <input type="text" id="country" name="country" class="form-control" placeholder="Malaysia"
                               value="{{ old('country', $supplier->country) }}">
                        @error('country')<div class="error-msg">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            {{-- Portal Access --}}
            <div class="form-card">
                <div class="card-header">
                    <div class="card-line"></div>
                    <div class="card-title">Portal Access</div>
                </div>

                <div class="form-row">
                    <div class="form-group" style="flex:1;">
                        <label class="form-label" for="portal_enabled">Portal Status</label>
                        <div style="display:flex;align-items:center;gap:10px;background:#f4f6fb;border-radius:8px;padding:14px 16px;">
                            <input type="checkbox" id="portal_enabled" name="portal_enabled" value="1"
                                   {{ old('portal_enabled', $supplier->portal_enabled) ? 'checked' : '' }}>
                            <div>
                                <div style="font-size:0.9rem;font-weight:600;color:#0f2044;">Enable supplier portal access</div>
                                <div class="form-text">Supplier can log in and manage orders and returns.</div>
                            </div>
                        </div>
                        @error('portal_enabled')<div class="error-msg">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div id="portal-password-section" class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="password">Reset Password</label>
                        <input type="password" id="password" name="password" class="form-control"
                               placeholder="Leave blank to keep current password">
                        <div class="form-text">Set a new password to reset portal access. Leave blank to keep the existing password.</div>
                        @error('password')<div class="error-msg">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="password_confirmation">Confirm Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control"
                               placeholder="Re-type new password">
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('owner.suppliers.index') }}" class="btn-discard">Cancel</a>
                <button type="submit" class="btn-submit">💾 Update Supplier</button>
            </div>
        </form>

        {{-- Portal Overview (read-only) --}}
        @if($supplier->portal_enabled && $supplier->user)
            <div class="form-card" style="margin-top:32px;">
                <div class="card-header">
                    <div class="card-line"></div>
                    <div class="card-title">Portal Access Overview</div>
                </div>

                <div class="info-grid">
                    <div class="info-row">
                        <div>
                            <div class="info-label">Portal Login Link</div>
                            <div class="info-value">
                                <a href="{{ $supplier->portal_link ?? route('supplier.login') }}" target="_blank"
                                   style="color:#0f2044;text-decoration:none;">
                                    {{ $supplier->portal_link ?? route('supplier.login') }}
                                </a>
                            </div>
                        </div>
                        <button type="button" class="copy-btn" data-copy-text="{{ $supplier->portal_link ?? route('supplier.login') }}">Copy link</button>
                    </div>

                    <div class="info-row">
                        <div>
                            <div class="info-label">Portal Email</div>
                            <div class="info-value">{{ $supplier->contact_email }}</div>
                        </div>
                        <button type="button" class="copy-btn" data-copy-text="{{ $supplier->contact_email }}">Copy email</button>
                    </div>

                    <div class="info-row">
                        <div>
                            <div class="info-label">WhatsApp Number</div>
                            <div class="info-value">{{ $whatsappPhone ?? 'Not configured' }}</div>
                        </div>
                        @if($whatsappPhone)
                            <button type="button" class="copy-btn" data-copy-text="{{ $whatsappPhone }}">Copy phone</button>
                        @endif
                    </div>

                    <div class="info-row">
                        <div style="flex:1;">
                            <div class="info-label">Temporary Password</div>
                            @if($supplier->temporary_password)
                                <input id="temporary-password-value" type="password" class="form-control"
                                       readonly value="{{ $supplier->temporary_password }}"
                                       style="padding:10px 12px;background:#fff;margin-top:6px;" />
                            @else
                                <div class="info-value">No temporary password available</div>
                            @endif
                        </div>
                        @if($supplier->temporary_password)
                            <div style="display:flex;gap:10px;align-items:center;">
                                <button type="button" class="copy-btn" data-copy-text="{{ $supplier->temporary_password }}">Copy password</button>
                                <button type="button" id="toggle-password-visibility" class="copy-btn" data-visible="false">Show</button>
                            </div>
                        @endif
                    </div>
                </div>

                <div style="display:flex;flex-wrap:wrap;gap:10px;margin-bottom:20px;">
                    <span class="status-pill {{ $supplier->invite_email_status ?? 'pending' }}">
                        Email Invite: {{ ucfirst($supplier->invite_email_status ?? 'pending') }}
                    </span>
                    <span class="status-pill {{ $supplier->invite_whatsapp_status ?? 'pending' }}">
                        WhatsApp Invite: {{ ucfirst($supplier->invite_whatsapp_status ?? 'pending') }}
                    </span>
                </div>

                <div style="font-size:0.88rem;color:#5a6a85;margin-bottom:16px;">
                    Resend portal access invitations to the supplier. A new temporary password will be generated after each resend.
                </div>

                <div style="display:flex;gap:14px;flex-wrap:wrap;">
                    <form action="{{ route('owner.suppliers.resend-email', $supplier) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-discard" style="color:#0f2044;border-color:#e2e8f0;background:#fff;">✉️ Resend Email Invite</button>
                    </form>
                    <form action="{{ route('owner.suppliers.resend-whatsapp', $supplier) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-discard" style="color:#0f2044;border-color:#e2e8f0;background:#fff;">💬 Resend WhatsApp Invite</button>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
    // Profile dropdown
    function toggleProfileDropdown() {
        const d = document.getElementById('profileDropdown');
        if (d) d.classList.toggle('show');
    }
    document.addEventListener('click', function(e) {
        const p = e.target.closest('.topbar-profile');
        const d = document.getElementById('profileDropdown');
        if (!p && d) d.classList.remove('show');
    });

    // Portal password section toggle
    document.addEventListener('DOMContentLoaded', function () {
        const portalToggle = document.getElementById('portal_enabled');
        const portalPasswordSection = document.getElementById('portal-password-section');

        if (portalToggle && portalPasswordSection) {
            const sync = () => {
                portalPasswordSection.style.display = portalToggle.checked ? 'flex' : 'none';
            };
            portalToggle.addEventListener('change', sync);
            sync();
        }

        // Copy buttons
        document.querySelectorAll('[data-copy-text]').forEach(function(btn) {
            btn.dataset.originalText = btn.textContent;
            btn.addEventListener('click', function() {
                const val = this.getAttribute('data-copy-text') || '';
                if (!val.trim()) return;
                navigator.clipboard.writeText(val).then(() => {
                    this.textContent = 'Copied!';
                    setTimeout(() => { this.textContent = this.dataset.originalText; }, 1500);
                });
            });
        });

        // Toggle temp password visibility
        const toggleBtn = document.getElementById('toggle-password-visibility');
        const pwInput   = document.getElementById('temporary-password-value');
        if (toggleBtn && pwInput) {
            toggleBtn.addEventListener('click', function() {
                const vis = this.dataset.visible === 'true';
                pwInput.type = vis ? 'password' : 'text';
                this.dataset.visible = vis ? 'false' : 'true';
                this.textContent = vis ? 'Show' : 'Hide';
            });
        }
    });
</script>
</body>
</html>
