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
        .stat-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 32px; }
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
        <div class="topbar-right" style="margin-left: auto; display: flex; align-items: center; gap: 20px;">
            <!-- Notification Bell -->
            <a href="{{ route('supplier.notifications.index') }}" style="text-decoration:none;position:relative;">
                <span style="font-size:1.2rem;">🔔</span>
                @if(auth()->user()->unreadNotifications->count() > 0)
                    <span style="position:absolute;top:-2px;right:-2px;background:#e74c3c;color:#fff;font-size:0.6rem;font-weight:700;padding:2px 5px;border-radius:10px;">
                        {{ auth()->user()->unreadNotifications->count() }}
                    </span>
                @endif
            </a>
            
            @include('supplier.components.topbar-profile')
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

        <!-- Stat Cards -->
        <div class="stat-row">
            <div class="stat-card" style="border-left: 4px solid #4a90d9;">
                <div class="stat-label">PENDING POS <span style="float:right;font-size:1rem;">📦</span></div>
                <div class="stat-value">{{ $orders->where('status','Pending')->count() }}</div>
                <div class="stat-hint">Total orders assigned to you</div>
            </div>
            <div class="stat-card" style="border-left: 4px solid #e74c3c;">
                <div class="stat-label">PENDING RETURNS <span style="float:right;font-size:1rem;">🔄</span></div>
                <div class="stat-value">{{ $pendingReturnsCount }}</div>
                <div class="stat-hint">Requires attention</div>
            </div>
            <div class="stat-card" style="border-left: 4px solid #f39c12;">
                <div class="stat-label">UNPAID INVOICES <span style="float:right;font-size:1rem;">📄</span></div>
                <div class="stat-value">{{ $invoicesAwaitingCount }}</div>
                <div class="stat-hint">Awaiting payment</div>
            </div>
            <div class="stat-card" style="border-left: 4px solid #27ae60;">
                <div class="stat-label">CREDIT NOTES <span style="float:right;font-size:1rem;">📄</span></div>
                <div class="stat-value">{{ \App\Models\CreditNote::where('supplier_id', auth()->user()->supplier?->id)->count() }}</div>
                <div class="stat-hint">Issued</div>
            </div>
        </div>

        <!-- My Purchase Orders -->
        <div class="section">
            <div class="section-header">
                <div class="section-title">
                    <svg width="18" height="18" fill="none" stroke="#0f2044" stroke-width="2" viewBox="0 0 24 24"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                    Recent Purchase Orders
                </div>
                <a href="{{ route('supplier.purchase-orders.index') }}" class="view-all">View All →</a>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>PO ID</th>
                        <th>Created At</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td><strong>#{{ $order->po_number }}</strong></td>
                            <td>{{ $order->created_at->format('d M Y') }}</td>
                            <td><strong>RM {{ number_format($order->final_amount ?? $order->getComputedTotal(), 2) }}</strong></td>
                            <td>
                                @php
                                    $s = $order->status;
                                    $cls = $s === 'Pending' ? 'badge-pending' : ($s === 'Approved' ? 'badge-approved' : (in_array($s, ['Delivered', 'Received']) ? 'badge-approved' : 'badge-rejected'));
                                @endphp
                                <span class="badge {{ $cls }}">{{ strtoupper($s) }}</span>
                            </td>
                            <td style="text-align: right; white-space: nowrap;">
                                <button class="btn btn-outline btn-sm" onclick="openReviewModal({{ $order->id }})">Review</button>
                                @if(in_array($order->status, ['Pending', 'Approved']) && !$order->delivery)
                                    <button class="btn btn-sm" style="background:#e8f0fb;color:#1e3a6e;border:1px solid #b5d4f4;margin-left:8px;" onclick="openDeliveryModal({{ $order->id }})">📅 Set Delivery</button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="empty-state">No purchase orders assigned to you yet.</td></tr>
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
                        @php
                            $totalQty = $return->lines->sum('quantity');
                            $itemNames = $return->lines->pluck('item.name')->filter()->unique()->join(', ') ?: 'Unknown Item';
                            $creditAmt = $return->lines->sum('subtotal');
                        @endphp
                        <div class="rr-meta">{{ Str::limit($itemNames, 40) }} · Qty: {{ number_format($totalQty) }} · {{ $return->reason }}</div>
                    </div>
                    @php
                        $rs = $return->status;
                        $rc = $rs === 'Pending' ? 'badge-pending' : ($rs === 'Approved' ? 'badge-approved' : 'badge-rejected');
                    @endphp
                    <span class="badge {{ $rc }}">{{ strtoupper($rs) }}</span>
                    @if($return->status === 'Pending')
                        <div style="display:flex;gap:8px;margin-left:12px">
                            <button onclick="openApprovalModal({{ $return->id }}, '{{ $return->return_number }}', {{ $totalQty }}, '{{ addslashes($itemNames) }}', '{{ number_format($creditAmt, 2, '.', '') }}')" class="btn btn-sm btn-primary">Accept</button>
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

{{-- ── PO Review Modal ── --}}
@foreach($orders as $order)
<div id="reviewModal-{{ $order->id }}" class="modal-overlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.55);z-index:999;align-items:center;justify-content:center;padding:20px;">
    <div style="background:#fff;border-radius:14px;width:100%;max-width:800px;max-height:90vh;overflow-y:auto;box-shadow:0 24px 64px rgba(0,0,0,0.22);animation:modalIn 0.2s ease;">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:20px 24px 16px;border-bottom:1px solid #f0f4f8;position:sticky;top:0;background:#fff;z-index:10;">
            <div style="font-size:1.1rem;font-weight:800;color:#0D1B2A;">Purchase Order Details</div>
            <button style="background:none;border:none;font-size:1.2rem;cursor:pointer;color:#9ca3af;line-height:1;padding:2px 6px;border-radius:4px;" onclick="closeReviewModal({{ $order->id }})">✕</button>
        </div>
        
        <div style="padding:24px;">
            <!-- Purchase Order Information -->
            <div style="background:#f8fafc;border:1px solid #e5e9f0;border-radius:8px;padding:16px;margin-bottom:24px;">
                <h4 style="margin:0 0 12px;font-size:0.85rem;text-transform:uppercase;color:#7a8fa8;letter-spacing:0.5px;">Purchase Order Information</h4>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;font-size:0.9rem;">
                    <div><span style="color:#6b7280;">PO Number:</span> <strong style="color:#0f2044;">#{{ $order->po_number }}</strong></div>
                    <div><span style="color:#6b7280;">Status:</span> 
                        @php
                            $s = $order->status;
                            $cls = $s === 'Pending' ? 'badge-pending' : ($s === 'Approved' ? 'badge-approved' : (in_array($s, ['Delivered', 'Received']) ? 'badge-approved' : 'badge-rejected'));
                        @endphp
                        <span class="badge {{ $cls }}">{{ strtoupper($s) }}</span>
                    </div>
                    <div><span style="color:#6b7280;">Supplier:</span> <strong style="color:#0f2044;">{{ optional($order->supplier)->name ?? '—' }}</strong></div>
                    <div><span style="color:#6b7280;">Created At:</span> <strong style="color:#0f2044;">{{ $order->created_at->format('d M Y, h:i A') }}</strong></div>
                    <div><span style="color:#6b7280;">Last Updated:</span> <strong style="color:#0f2044;">{{ $order->updated_at->format('d M Y, h:i A') }}</strong></div>
                    <div><span style="color:#6b7280;">Delivery Schedule:</span> 
                        <strong style="color:#0f2044;">
                            @if($order->delivery)
                                {{ $order->delivery->delivery_date?->format('d M Y') }} at {{ $order->delivery->delivery_time ? \Carbon\Carbon::parse($order->delivery->delivery_time)->format('h:i A') : 'TBD' }}
                            @else
                                Not Assigned
                            @endif
                        </strong>
                    </div>
                </div>
            </div>

            <!-- Ordered Items Table -->
            <h4 style="margin:0 0 12px;font-size:0.85rem;text-transform:uppercase;color:#7a8fa8;letter-spacing:0.5px;">Ordered Items</h4>
            <div style="border:1px solid #e5e9f0;border-radius:8px;overflow:hidden;margin-bottom:24px;">
                <table style="margin:0;">
                    <thead style="background:#f8fafc;">
                        <tr>
                            <th style="padding:10px 16px;">Item Name</th>
                            <th style="padding:10px 16px;text-align:center;">Quantity</th>
                            <th style="padding:10px 16px;text-align:right;">Unit Price</th>
                            <th style="padding:10px 16px;text-align:right;">Line Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($order->orderItems as $item)
                        <tr>
                            <td style="padding:12px 16px;border-bottom:1px solid #f0f4f8;font-weight:600;">{{ optional($item->item)->name ?? 'Unknown Item' }}</td>
                            <td style="padding:12px 16px;border-bottom:1px solid #f0f4f8;text-align:center;">{{ $item->quantity }}</td>
                            <td style="padding:12px 16px;border-bottom:1px solid #f0f4f8;text-align:right;">RM {{ number_format($item->unit_price, 2) }}</td>
                            <td style="padding:12px 16px;border-bottom:1px solid #f0f4f8;text-align:right;font-weight:600;">RM {{ number_format($item->subtotal, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="empty-state">No items found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Financial Summary -->
            <div style="display:flex;justify-content:flex-end;">
                <div style="width:300px;background:#f8fafc;border:1px solid #e5e9f0;border-radius:8px;padding:16px;">
                    <div style="display:flex;justify-content:space-between;margin-bottom:8px;font-size:0.9rem;">
                        <span style="color:#6b7280;">Subtotal:</span>
                        <strong style="color:#0f2044;">RM {{ number_format($order->getComputedTotal(), 2) }}</strong>
                    </div>
                    @if($order->creditNote)
                    <div style="display:flex;justify-content:space-between;margin-bottom:8px;font-size:0.9rem;">
                        <span style="color:#e74c3c;">Credit Deduction:</span>
                        <strong style="color:#e74c3c;">-RM {{ number_format($order->creditNote->amount, 2) }}</strong>
                    </div>
                    @endif
                    <div style="display:flex;justify-content:space-between;margin-top:12px;padding-top:12px;border-top:1px solid #e5e9f0;font-size:1.1rem;">
                        <strong style="color:#0f2044;">Final Amount:</strong>
                        <strong style="color:#1d8348;">RM {{ number_format($order->final_amount ?? $order->getComputedTotal(), 2) }}</strong>
                    </div>
                </div>
            </div>

            <!-- Approve/Reject Actions inside Modal -->
            @if($order->status === 'Pending')
            <div style="margin-top:24px;padding-top:24px;border-top:1px solid #f0f4f8;display:flex;flex-direction:column;gap:16px;">
                <form action="{{ route('supplier.orders.confirm', $order) }}" method="POST" onsubmit="return validateDeliveryForm(this, 'modal_delivery_date_{{ $order->id }}', 'modal_delivery_time_{{ $order->id }}')">
                    @csrf
                    <input type="hidden" name="status" value="Approved">
                    
                    <div style="background:#f8fafc; border:1px solid #e5e9f0; border-radius:8px; padding:16px; margin-bottom:16px; text-align:left;">
                        <h4 style="margin:0 0 12px; font-size:0.85rem; text-transform:uppercase; color:#7a8fa8; letter-spacing:0.5px;">Set Delivery Schedule</h4>
                        <div style="font-size:0.8rem; color:#9daec5; margin-bottom:12px; line-height:1.4;">
                            Mon–Fri: 08:00–17:00 · Saturday: 08:00–12:00<br>Sunday & Public Holidays: not allowed
                        </div>
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                            <div class="form-group">
                                <label class="form-label" style="font-size:0.75rem;">Delivery Date <span style="color:#e74c3c;">*</span></label>
                                <input type="date" name="delivery_date" class="form-input" id="modal_delivery_date_{{ $order->id }}" min="{{ now()->addDay()->toDateString() }}" required onchange="validateDeliveryDate(this, 'modal_delivery_time_{{ $order->id }}')" style="width:100%;">
                            </div>
                            <div class="form-group">
                                <label class="form-label" style="font-size:0.75rem;">Delivery Time</label>
                                <input type="time" name="delivery_time" class="form-input" id="modal_delivery_time_{{ $order->id }}" min="08:00" max="17:00" onchange="validateDeliveryTime(this, 'modal_delivery_date_{{ $order->id }}')" style="width:100%;">
                            </div>
                        </div>
                        <div class="form-group" style="margin-top:12px;">
                            <label class="form-label" style="font-size:0.75rem;">Delivery Notes / Comments</label>
                            <textarea name="notes" class="form-input" rows="2" placeholder="Optional notes for the delivery..." style="width:100%; resize:vertical;"></textarea>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:12px;font-size:0.95rem;">Approve Purchase Order</button>
                </form>

                <form action="{{ route('supplier.orders.confirm', $order) }}" method="POST">
                    @csrf
                    <input type="hidden" name="status" value="Rejected">
                    <button type="submit" class="btn" style="width:100%;justify-content:center;background:#fdedec;color:#c0392b;border:1px solid #f5b7b1;padding:12px;font-size:0.95rem;">Reject Order</button>
                </form>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- ── PO Set Delivery Modal ── --}}
@if(in_array($order->status, ['Pending', 'Approved']) && !$order->delivery)
<div id="deliveryModal-{{ $order->id }}" class="modal-overlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.55);z-index:999;align-items:center;justify-content:center;padding:20px;">
    <div style="background:#fff;border-radius:14px;width:100%;max-width:450px;box-shadow:0 24px 64px rgba(0,0,0,0.22);animation:modalIn 0.2s ease;overflow:hidden;">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:20px 24px 16px;border-bottom:1px solid #f0f4f8;">
            <div style="font-size:1.1rem;font-weight:800;color:#0D1B2A;">Set Delivery Schedule</div>
            <button style="background:none;border:none;font-size:1.2rem;cursor:pointer;color:#9ca3af;line-height:1;padding:2px 6px;border-radius:4px;" onclick="closeDeliveryModal({{ $order->id }})">✕</button>
        </div>
        
        <form action="{{ route('supplier.orders.delivery', $order) }}" method="POST" onsubmit="return validateDeliveryForm(this, 'dash_modal_delivery_date_{{ $order->id }}', 'dash_modal_delivery_time_{{ $order->id }}')">
            @csrf
            <div style="padding:20px 24px;">
                <p style="font-size:0.85rem;color:#6b7280;margin:0 0 16px;">Set the delivery date and time for PO <strong>#{{ $order->po_number }}</strong>.</p>
                <div style="font-size:0.8rem;color:#9daec5;margin-bottom:16px;background:#f8fafc;padding:12px;border-radius:6px;border:1px solid #e5e9f0;">
                    Mon–Fri: 08:00–17:00 · Saturday: 08:00–12:00<br>Sunday & Public Holidays: not allowed
                </div>

                <div class="form-group" style="margin-bottom:16px;">
                    <label class="form-label" style="font-size:0.85rem;font-weight:600;margin-bottom:6px;display:block;">Delivery Date</label>
                    <input type="date" name="delivery_date" class="form-input" id="dash_modal_delivery_date_{{ $order->id }}" min="{{ now()->addDay()->toDateString() }}" required onchange="validateDeliveryDate(this, 'dash_modal_delivery_time_{{ $order->id }}')" style="width:100%;padding:10px;border:1px solid #d1dce8;border-radius:6px;">
                </div>
                
                <div class="form-group" style="margin-bottom:16px;">
                    <label class="form-label" style="font-size:0.85rem;font-weight:600;margin-bottom:6px;display:block;">Delivery Time</label>
                    <input type="time" name="delivery_time" class="form-input" id="dash_modal_delivery_time_{{ $order->id }}" min="08:00" max="17:00" onchange="validateDeliveryTime(this, 'dash_modal_delivery_date_{{ $order->id }}')" style="width:100%;padding:10px;border:1px solid #d1dce8;border-radius:6px;">
                </div>
            </div>
            <div style="padding:16px 24px;border-top:1px solid #f0f4f8;display:flex;gap:10px;background:#fafbfc;">
                <button type="button" class="btn btn-cancel" style="flex:1;justify-content:center;padding:10px;" onclick="closeDeliveryModal({{ $order->id }})">Cancel</button>
                <button type="submit" class="btn btn-primary" style="flex:2;justify-content:center;padding:10px;">Save Delivery</button>
            </div>
        </form>
    </div>
</div>
@endif
@endforeach

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
                    <input type="text" style="width:100%;border:1.5px solid #e5e9f0;border-radius:8px;padding:10px 14px;font-size:.88rem;font-family:'Inter',sans-serif;color:#0D1B2A;outline:none;background:#f3f4f6;" id="credit_amount_display" disabled placeholder="0.00">
                    <input type="hidden" id="credit_amount" name="credit_amount">
                    <p style="font-size:0.75rem; color:#6b7280; margin-top:4px;">To adjust credit amounts per line item, go to the <a href="{{ route('supplier.returns.index') }}" style="color:#4a90d9;text-decoration:none;">Return Requests page</a>.</p>
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
function openReviewModal(id) {
    document.getElementById('reviewModal-' + id).style.display = 'flex';
}
function closeReviewModal(id) {
    document.getElementById('reviewModal-' + id).style.display = 'none';
}

function openDeliveryModal(id) {
    document.getElementById('deliveryModal-' + id).style.display = 'flex';
}
function closeDeliveryModal(id) {
    document.getElementById('deliveryModal-' + id).style.display = 'none';
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
    document.getElementById('credit_amount_display').value = suggestedCredit ? 'RM ' + suggestedCredit : '';
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