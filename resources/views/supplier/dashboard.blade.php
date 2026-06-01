<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Supplier Dashboard – 22UniMart</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: #eef2f7; display: flex; min-height: 100vh; }

        /* ── Sidebar ── */
        .sidebar {
            width: 210px; flex-shrink: 0;
            background: #0f2044; color: #fff;
            display: flex; flex-direction: column;
            padding: 0 0 24px 0; position: fixed; top: 0; left: 0; height: 100vh;
        }
        .sidebar-brand { padding: 24px 20px 8px; }
        .sidebar-brand .brand-name { font-size: 1rem; font-weight: 800; letter-spacing: 0.5px; }
        .sidebar-brand .brand-sub  { font-size: 0.7rem; color: #8ca0c0; margin-top: 2px; }
        .sidebar-nav { flex: 1; margin-top: 20px; }
        .nav-item { display: flex; align-items: center; gap: 12px; padding: 11px 20px; font-size: 0.88rem; font-weight: 500; color: #8ca0c0; cursor: pointer; text-decoration: none; transition: all 0.15s; border-left: 3px solid transparent; }
        .nav-item:hover { color: #fff; background: rgba(255,255,255,0.06); }
        .nav-item.active { color: #fff; background: rgba(255,255,255,0.1); border-left-color: #4a90d9; }
        .nav-icon { width: 18px; text-align: center; flex-shrink: 0; }
        .sidebar-bottom { padding: 0 20px; display: flex; flex-direction: column; gap: 8px; }
        .btn-report { background: #1e3a6e; color: #fff; border: none; border-radius: 8px; padding: 12px 16px; font-size: 0.85rem; font-weight: 600; cursor: pointer; width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px; transition: background 0.15s; }
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
        .topbar-icons { margin-left: auto; display: flex; align-items: center; gap: 20px; }
        .icon-btn { background: none; border: none; cursor: pointer; color: #5a6a85; font-size: 1.1rem; position: relative; padding: 4px; }
        .badge-dot { position: absolute; top: 0; right: 0; width: 8px; height: 8px; background: #e74c3c; border-radius: 50%; border: 2px solid #fff; }
        .topbar-profile { display: flex; align-items: center; gap: 8px; }
        .profile-info { text-align: right; }
        .profile-name { font-size: 0.85rem; font-weight: 600; color: #1a2744; }
        .profile-role { font-size: 0.72rem; color: #9daec5; }
        .avatar { width: 36px; height: 36px; border-radius: 50%; background: #0f2044; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 0.85rem; font-weight: 700; }
        .status-online { display: flex; align-items: center; gap: 6px; font-size: 0.78rem; font-weight: 600; color: #27ae60; background: #eafaf1; border: 1px solid #a9dfbf; border-radius: 20px; padding: 4px 12px; }
        .dot-green { width: 7px; height: 7px; background: #27ae60; border-radius: 50%; }

        /* Content */
        .content { padding: 32px; }
        .page-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 28px; }
        .page-title { font-size: 1.6rem; font-weight: 800; color: #0f2044; }
        .page-sub   { font-size: 0.85rem; color: #7a8fa8; margin-top: 4px; }

        /* Stat cards */
        .stat-row { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 32px; }
        .stat-card { background: #fff; border-radius: 12px; padding: 24px; box-shadow: 0 1px 6px rgba(15,32,68,0.07); position: relative; }
        .stat-label { font-size: 0.72rem; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; color: #7a8fa8; }
        .stat-value { font-size: 2.4rem; font-weight: 800; color: #0f2044; margin: 8px 0 4px; line-height: 1; }
        .stat-hint  { font-size: 0.78rem; color: #9daec5; }
        .stat-icon  { position: absolute; top: 20px; right: 20px; color: #c0cfe0; }
        .stat-hint .attn { color: #e74c3c; }

        /* Section */
        .section { background: #fff; border-radius: 12px; box-shadow: 0 1px 6px rgba(15,32,68,0.07); margin-bottom: 28px; overflow: hidden; }
        .section-header { display: flex; align-items: center; justify-content: space-between; padding: 20px 24px 16px; border-bottom: 1px solid #f0f4f8; }
        .section-title { display: flex; align-items: center; gap: 10px; font-size: 1rem; font-weight: 700; color: #0f2044; }
        .view-all { font-size: 0.82rem; color: #3a7bd5; text-decoration: none; font-weight: 600; }
        .view-all:hover { text-decoration: underline; }

        /* Table */
        table { width: 100%; border-collapse: collapse; }
        thead th { padding: 10px 20px; font-size: 0.7rem; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; color: #9daec5; text-align: left; background: #f8fafc; border-bottom: 1px solid #edf2f7; }
        tbody td { padding: 14px 20px; font-size: 0.88rem; color: #1a2744; border-bottom: 1px solid #f0f4f8; vertical-align: middle; }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr:hover td { background: #fafbfd; }

        /* Badges */
        .badge { display: inline-flex; align-items: center; padding: 4px 12px; border-radius: 20px; font-size: 0.72rem; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; }
        .badge-pending  { background: #fef3e2; color: #d4870a; border: 1px solid #fde0a0; }
        .badge-approved { background: #e8f8f0; color: #1d8348; border: 1px solid #a9dfbf; }
        .badge-rejected { background: #fdedec; color: #c0392b; border: 1px solid #f5b7b1; }

        /* Buttons */
        .btn { display: inline-flex; align-items: center; gap: 6px; padding: 7px 16px; border-radius: 7px; font-size: 0.82rem; font-weight: 600; cursor: pointer; border: 1px solid transparent; transition: all 0.15s; font-family: 'Inter', sans-serif; text-decoration: none; }
        .btn-outline { background: #fff; border-color: #d1dce8; color: #3a4d6a; }
        .btn-outline:hover { background: #f4f6fb; }
        .btn-primary { background: #0f2044; color: #fff; border-color: #0f2044; }
        .btn-primary:hover { background: #182e5e; }
        .btn-sm { padding: 5px 12px; font-size: 0.78rem; }
        .btn-cancel { background: #f4f6fb; color: #5a6a85; border-color: #e2e8f0; }
        .btn-cancel:hover { background: #e8ecf4; }

        /* Inline approval form */
        .approve-form-row { background: #f8fafd; border-bottom: 1px solid #e8edf5; }
        .approve-form-inner { padding: 20px 24px; display: flex; align-items: flex-end; gap: 20px; flex-wrap: wrap; }
        .approve-form-title { font-size: 0.85rem; font-weight: 700; color: #0f2044; margin-bottom: 14px; }
        .form-group { display: flex; flex-direction: column; gap: 5px; }
        .form-label { font-size: 0.7rem; font-weight: 600; letter-spacing: 0.5px; text-transform: uppercase; color: #7a8fa8; }
        .form-input { border: 1px solid #d1dce8; border-radius: 7px; padding: 8px 12px; font-family: 'Inter', sans-serif; font-size: 0.85rem; color: #1a2744; outline: none; }
        .form-input:focus { border-color: #0f2044; }

        .delivery-col { font-size: 0.82rem; color: #5a6a85; line-height: 1.4; }

        /* Return requests */
        .rr-item { display: flex; align-items: center; gap: 16px; padding: 14px 24px; border-bottom: 1px solid #f0f4f8; }
        .rr-item:last-child { border-bottom: none; }
        .rr-icon { width: 36px; height: 36px; background: #fdedec; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #e74c3c; font-size: 1rem; flex-shrink: 0; }
        .rr-info { flex: 1; }
        .rr-id   { font-size: 0.82rem; font-weight: 700; color: #0f2044; }
        .rr-meta { font-size: 0.78rem; color: #9daec5; margin-top: 2px; }
        .empty-state { padding: 32px; text-align: center; color: #9daec5; font-size: 0.9rem; }

        /* Flash */
        .flash { padding: 12px 20px; border-radius: 8px; font-size: 0.88rem; margin-bottom: 20px; }
        .flash-success { background: #e8f8f0; color: #1d8348; border: 1px solid #a9dfbf; }
    </style>
</head>
<body>

<!-- Sidebar -->
@include('supplier.partials.sidebar', ['active' => 'dashboard'])

<!-- Main -->
<div class="main">
    <!-- Topbar -->
    <div class="topbar">
        <div class="search-box">
            <svg width="15" height="15" fill="none" stroke="#9daec5" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            <input type="text" placeholder="Search orders, items...">
        </div>
        <div class="topbar-icons">
            <button class="icon-btn">🔔<span class="badge-dot"></span></button>
            <button class="icon-btn">⚙</button>
            <button class="icon-btn">⋯</button>
            <div class="topbar-profile">
                <div class="profile-info">
                    <div class="profile-name">{{ auth()->user()->name }}</div>
                    <form action="{{ route('logout') }}" method="POST" style="display:inline">
                        @csrf
                        <button type="submit" style="background:none;border:none;font-size:0.72rem;color:#9daec5;cursor:pointer;font-family:inherit;padding:0;">Logout</button>
                    </form>
                </div>
                <div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="content">
        <!-- Header -->
        <div class="page-header">
            <div>
                <div class="page-title">Supplier Dashboard</div>
                <div class="page-sub">Overview of pending orders and return requests.</div>
            </div>
            <div class="status-online"><span class="dot-green"></span> ONLINE</div>
        </div>

        @if(session('success'))
            <div class="flash flash-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="flash" style="background:#fdedec; color:#c0392b; border: 1px solid #f5b7b1;">{{ session('error') }}</div>
        @endif

        <!-- Stat Cards (2 cards only — no Total Value Pending) -->
        <div class="stat-row">
            <div class="stat-card">
                <div class="stat-label">Pending POs</div>
                <div class="stat-value">{{ $orders->where('status','Pending')->count() }}</div>
                <div class="stat-hint">Total orders assigned to you</div>
                <div class="stat-icon">
                    <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Pending Returns</div>
                <div class="stat-value">{{ $returns->count() }}</div>
                <div class="stat-hint">Requires attention</div>
                <div class="stat-icon">
                    <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3"/></svg>
                </div>
            </div>
        </div>

        <!-- My Purchase Orders -->
        <div class="section">
            <div class="section-header">
                <div class="section-title">
                    <svg width="18" height="18" fill="none" stroke="#0f2044" stroke-width="2" viewBox="0 0 24 24"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                    My Purchase Orders
                </div>
                <a href="{{ route('supplier.purchase-orders.index') }}" class="view-all">View All →</a>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Item Details</th>
                        <th>Quantity</th>
                        <th>Delivery</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td><strong>#{{ $order->po_number }}</strong></td>
                            <td>{{ optional($order->item)->name ?? '—' }}</td>
                            <td>{{ $order->quantity }} Units</td>
                            <td class="delivery-col">
                                @if($order->delivery)
                                    {{ $order->delivery->delivery_date?->format('M d, Y') }}<br>
                                    {{ $order->delivery->delivery_date?->format('h:i A') }}
                                @else
                                    <span style="color:#9daec5">—</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $s = $order->status;
                                    $cls = $s === 'Pending' ? 'badge-pending' : ($s === 'Approved' ? 'badge-approved' : 'badge-rejected');
                                @endphp
                                <span class="badge {{ $cls }}">{{ strtoupper($s) }}</span>
                            </td>
                            <td>
                                @if($order->status === 'Pending')
                                    <button class="btn btn-outline btn-sm" onclick="toggleApproval({{ $order->id }})">Review</button>
                                @elseif($order->status === 'Approved' && !$order->delivery)
                                    <button class="btn btn-sm" style="background:#e8f0fb;color:#1e3a6e;border:1px solid #b5d4f4;" onclick="toggleDelivery({{ $order->id }})">📅 Set Delivery</button>
                                @elseif($order->status === 'Approved' && $order->delivery)
                                    <span style="color:#27ae60;font-size:.78rem;font-weight:600;">✓ Scheduled</span>
                                @else
                                    <span style="color:#9daec5;font-size:.82rem;">—</span>
                                @endif
                            </td>
                        </tr>
                        {{-- Inline approval form --}}
                        @if($order->status === 'Pending')
                        <tr class="approve-form-row" id="approval-{{ $order->id }}" style="display:none">
                            <td colspan="6">
                                <div class="approve-form-inner">
                                    <div style="width:100%">
                                        <div class="approve-form-title">Approve Order #{{ $order->po_number }}</div>
                                    </div>
                                    <form action="{{ route('supplier.orders.confirm', $order) }}" method="POST" style="display:flex;align-items:flex-end;gap:16px;flex-wrap:wrap;width:100%" onsubmit="return validateDeliveryForm(this, 'approve_date_{{ $order->id }}', 'approve_time_{{ $order->id }}')">
                                        @csrf
                                        <input type="hidden" name="status" value="Approved">
                                        <div class="form-group">
                                            <label class="form-label">Scheduled Delivery Date</label>
                                            <input type="date" name="delivery_date" class="form-input" id="approve_date_{{ $order->id }}" min="{{ now()->addDay()->toDateString() }}" required onchange="validateDeliveryDate(this, 'approve_time_{{ $order->id }}')">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Scheduled Delivery Time</label>
                                            <input type="time" name="delivery_time" class="form-input" id="approve_time_{{ $order->id }}" min="08:00" max="17:00" onchange="validateDeliveryTime(this, 'approve_date_{{ $order->id }}')">
                                        </div>
                                        <button type="button" class="btn btn-cancel" onclick="toggleApproval({{ $order->id }})">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Confirm Approval</button>
                                    </form>
                                </div>
                                <!-- Reject separately -->
                                <div style="padding: 0 24px 16px; display:flex; gap:10px;">
                                    <form action="{{ route('supplier.orders.confirm', $order) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="status" value="Rejected">
                                        <button type="submit" class="btn btn-sm" style="background:#fdedec;color:#c0392b;border-color:#f5b7b1;">Reject Order</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endif

                        {{-- Inline Set Delivery Form (Approved, no delivery yet) --}}
                        @if($order->status === 'Approved' && !$order->delivery)
                        <tr class="approve-form-row" id="delivery-{{ $order->id }}" style="display:none">
                            <td colspan="6">
                                <div class="approve-form-inner">
                                    <div style="width:100%">
                                        <div class="approve-form-title">📅 Set Delivery Schedule for #{{ $order->po_number }}</div>
                                        <div style="font-size:.78rem;color:#9daec5;margin-bottom:12px;">Mon–Fri: 08:00–17:00 · Saturday: 08:00–12:00 · Sunday: not allowed</div>
                                    </div>
                                    <form action="{{ route('supplier.orders.delivery', $order) }}" method="POST" style="display:flex;align-items:flex-end;gap:16px;flex-wrap:wrap;width:100%" onsubmit="return validateDeliveryForm(this, 'dash_delivery_date_{{ $order->id }}', 'dash_delivery_time_{{ $order->id }}')">
                                        @csrf
                                        <div class="form-group">
                                            <label class="form-label">Delivery Date</label>
                                            <input type="date" name="delivery_date" class="form-input" id="dash_delivery_date_{{ $order->id }}" min="{{ now()->addDay()->toDateString() }}" required onchange="validateDeliveryDate(this, 'dash_delivery_time_{{ $order->id }}')">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Delivery Time</label>
                                            <input type="time" name="delivery_time" class="form-input" id="dash_delivery_time_{{ $order->id }}" min="08:00" max="17:00" onchange="validateDeliveryTime(this, 'dash_delivery_date_{{ $order->id }}')">
                                        </div>
                                        <button type="button" class="btn btn-cancel" onclick="toggleDelivery({{ $order->id }})">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Save Delivery</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endif
                    @empty
                        <tr><td colspan="6" class="empty-state">No purchase orders assigned to you yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- My Return Requests -->
        <div class="section">
            <div class="section-header">
                <div class="section-title">
                    <svg width="18" height="18" fill="none" stroke="#e74c3c" stroke-width="2" viewBox="0 0 24 24"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3"/></svg>
                    My Return Requests
                </div>
                <a href="{{ route('supplier.returns.index') }}" class="view-all">View All →</a>
            </div>
            @forelse($returns as $return)
                <div class="rr-item">
                    <div class="rr-icon">↩</div>
                    <div class="rr-info">
                        <div class="rr-id">#{{ $return->return_number }}</div>
                        <div class="rr-meta">{{ optional($return->item)->name ?? '—' }} · Qty: {{ $return->quantity }} · {{ $return->reason }}</div>
                    </div>
                    @php
                        $rs = $return->status;
                        $rc = $rs === 'Pending' ? 'badge-pending' : ($rs === 'Approved' ? 'badge-approved' : 'badge-rejected');
                    @endphp
                    <span class="badge {{ $rc }}">{{ strtoupper($rs) }}</span>
                    @if($return->status === 'Pending')
                        <div style="display:flex;gap:8px;margin-left:12px">
                            <button onclick="openApprovalModal({{ $return->id }}, '{{ $return->return_number }}', {{ $return->quantity }}, '{{ addslashes(optional($return->item)->name ?? '') }}', '{{ number_format($return->credit_amount, 2, '.', '') }}')" class="btn btn-sm btn-primary">Accept</button>
                            <form action="{{ route('supplier.returns.status', $return) }}" method="POST" style="display:inline;">
                                @csrf
                                <button name="status" value="Rejected" class="btn btn-sm btn-outline">Reject</button>
                            </form>
                        </div>
                    @endif
                </div>
            @empty
                <div class="empty-state">No pending return requests at this time.</div>
            @endforelse
        </div>
    </div>
</div>

{{-- ── Approve Return Request Modal ── --}}
<div id="approvalModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.55);z-index:999;align-items:center;justify-content:center;padding:20px;">
    <div style="background:#fff;border-radius:14px;width:100%;max-width:500px;box-shadow:0 24px 64px rgba(0,0,0,0.22);animation:modalIn 0.2s ease;overflow:hidden;">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:20px 24px 16px;border-bottom:1px solid #f0f4f8;">
            <div style="font-size:1rem;font-weight:800;color:#0D1B2A;">Approve Return Request</div>
            <button style="background:none;border:none;font-size:1.1rem;cursor:pointer;color:#9ca3af;line-height:1;padding:2px 6px;border-radius:4px;" onclick="closeApprovalModal()">✕</button>
        </div>
        <p style="font-size:0.82rem;color:#6b7280;padding:12px 24px 0;">Please provide the credit amount and reason before approving.</p>
        <form id="approvalForm" method="POST">
            @csrf
            <input type="hidden" name="status" value="Approved">
            <div style="padding:16px 24px 20px;display:flex;flex-direction:column;gap:14px;">
                <div style="background:#f8fafc;border:1px solid #e5e9f0;border-radius:8px;padding:12px 14px;">
                    <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#9ca3af;margin-bottom:4px;">Return Request</div>
                    <div style="font-size:.9rem;font-weight:700;color:#0D1B2A;" id="modalRRNumber">—</div>
                    <div style="font-size:.78rem;color:#6b7280;margin-top:2px;" id="modalRRDetail">—</div>
                </div>
                <div style="display:flex;flex-direction:column;gap:6px;">
                    <label style="font-size:.78rem;font-weight:700;color:#374151;letter-spacing:.3px;text-transform:uppercase;" for="credit_amount">Credit Amount (RM)</label>
                    <input type="number" step="0.01" min="0" style="width:100%;border:1.5px solid #e5e9f0;border-radius:8px;padding:10px 14px;font-size:.88rem;font-family:'Inter',sans-serif;color:#0D1B2A;outline:none;background:#fafbfc;" id="credit_amount" name="credit_amount" placeholder="0.00" required>
                </div>
                <div style="display:flex;flex-direction:column;gap:6px;">
                    <label style="font-size:.78rem;font-weight:700;color:#374151;letter-spacing:.3px;text-transform:uppercase;" for="reason">Reason / Note</label>
                    <textarea style="width:100%;border:1.5px solid #e5e9f0;border-radius:8px;padding:10px 14px;font-size:.88rem;font-family:'Inter',sans-serif;color:#0D1B2A;outline:none;background:#fafbfc;resize:vertical;min-height:80px;" id="reason" name="reason" rows="3" placeholder="Optional note for this approval..."></textarea>
                </div>
            </div>
            <div style="padding:16px 24px;border-top:1px solid #f0f4f8;display:flex;gap:10px;">
                <button type="button" style="flex:1;background:#fff;color:#374151;border:1.5px solid #e5e9f0;border-radius:8px;padding:10px;font-size:.85rem;font-weight:600;cursor:pointer;font-family:'Inter',sans-serif;transition:all 0.15s;" onclick="closeApprovalModal()">Cancel</button>
                <button type="submit" style="flex:2;background:#0D1B2A;color:#fff;border:none;border-radius:8px;padding:10px;font-size:.85rem;font-weight:700;cursor:pointer;font-family:'Inter',sans-serif;transition:background 0.15s;">✔ Approve & Create Credit Note</button>
            </div>
        </form>
    </div>
</div>

<style>
@keyframes modalIn { from { opacity: 0; transform: translateY(-16px) scale(.97); } to { opacity: 1; transform: none; } }
#approvalModal.open { display: flex !important; }
#credit_amount:focus, #reason:focus { border-color: #0D1B2A; background: #fff; }
</style>

<script>
function toggleApproval(id) {
    const row = document.getElementById('approval-' + id);
    row.style.display = row.style.display === 'none' ? 'table-row' : 'none';
}

function toggleDelivery(id) {
    const row = document.getElementById('delivery-' + id);
    row.style.display = row.style.display === 'none' ? 'table-row' : 'none';
}

function validateDeliveryDate(dateInput, timeInputId) {
    if (!dateInput.value) return;

    const selected = new Date(dateInput.value + 'T00:00:00');
    const day = selected.getDay();
    const timeInput = document.getElementById(timeInputId);

    if (day === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Invalid Delivery Day',
            text: '⚠ Deliveries are not allowed on Sundays. Please select another delivery date.',
            confirmButtonColor: '#0f2044',
            confirmButtonText: 'OK, I will choose another date'
        });
        dateInput.value = '';
        if (timeInput) {
            timeInput.value = '';
            timeInput.min = '08:00';
            timeInput.max = '17:00';
        }
        return;
    }

    if (day === 6) {
        if (timeInput) {
            timeInput.min = '08:00';
            timeInput.max = '12:00';
            if (timeInput.value && timeInput.value > '12:00') {
                timeInput.value = '';
                Swal.fire({
                    icon: 'warning',
                    title: 'Time Adjusted',
                    text: '⚠ Saturday deliveries are only allowed until 12:00 PM. Please re-select delivery time.',
                    confirmButtonColor: '#0f2044'
                });
            }
        }
        return;
    }

    if (timeInput) {
        timeInput.min = '08:00';
        timeInput.max = '17:00';
        if (timeInput.value && timeInput.value > '17:00') {
            timeInput.value = '';
        }
    }
}

function validateDeliveryTime(timeInput, dateInputId) {
    if (!timeInput.value) return;

    const dateInput = document.getElementById(dateInputId);
    if (!dateInput || !dateInput.value) return;

    const selected = new Date(dateInput.value + 'T00:00:00');
    const day = selected.getDay();
    const time = timeInput.value;

    if (day === 6 && time > '12:00') {
        Swal.fire({
            icon: 'warning',
            title: 'Invalid Delivery Time',
            text: '⚠ Saturday deliveries are only allowed until 12:00 PM (noon). Please select an earlier time.',
            confirmButtonColor: '#0f2044'
        });
        timeInput.value = '';
        return;
    }

    if (day !== 6 && day !== 0 && (time < '08:00' || time > '17:00')) {
        Swal.fire({
            icon: 'warning',
            title: 'Invalid Delivery Time',
            text: '⚠ Weekday delivery time must be between 8:00 AM and 5:00 PM.',
            confirmButtonColor: '#0f2044'
        });
        timeInput.value = '';
    }
}

function validateDeliveryForm(formEl, dateInputId, timeInputId) {
    const dateInput = document.getElementById(dateInputId);
    const timeInput = document.getElementById(timeInputId);

    if (!dateInput || !dateInput.value) {
        Swal.fire({
            icon: 'error',
            title: 'Missing Delivery Date',
            text: 'Please select a delivery date before submitting.',
            confirmButtonColor: '#0f2044'
        });
        return false;
    }

    const day = new Date(dateInput.value + 'T00:00:00').getDay();
    if (day === 0) {
        Swal.fire({
            icon: 'error',
            title: 'Invalid Delivery Day',
            text: '⚠ Deliveries are not allowed on Sundays.',
            confirmButtonColor: '#0f2044'
        });
        return false;
    }

    if (timeInput && timeInput.value) {
        const time = timeInput.value;
        if (day === 6 && time > '12:00') {
            Swal.fire({
                icon: 'error',
                title: 'Invalid Time for Saturday',
                text: '⚠ Saturday deliveries must be before 12:00 PM.',
                confirmButtonColor: '#0f2044'
            });
            return false;
        }
        if (day !== 6 && (time < '08:00' || time > '17:00')) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid Delivery Time',
                text: '⚠ Weekday delivery time must be between 8:00 AM and 5:00 PM.',
                confirmButtonColor: '#0f2044'
            });
            return false;
        }
    }

    return true;
}

function openApprovalModal(rrId, rrNumber, qty, itemName, suggestedCredit) {
    document.getElementById('modalRRNumber').textContent = '#' + rrNumber;
    document.getElementById('modalRRDetail').textContent = itemName ? itemName + ' · ' + qty + ' unit(s)' : qty + ' unit(s)';
    document.getElementById('approvalForm').action = '/supplier/return-requests/' + rrId + '/status';
    document.getElementById('credit_amount').value = suggestedCredit || '';
    document.getElementById('reason').value = '';
    document.getElementById('approvalModal').classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeApprovalModal() {
    document.getElementById('approvalModal').classList.remove('open');
    document.body.style.overflow = '';
}

// Close on backdrop click
document.getElementById('approvalModal').addEventListener('click', function(e) {
    if (e.target === this) closeApprovalModal();
});

// Close on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeApprovalModal();
});
</script>
</body>
</html>