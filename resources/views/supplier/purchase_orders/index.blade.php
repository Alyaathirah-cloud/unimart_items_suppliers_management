<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Purchase Orders – Supplier Portal</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'Inter',sans-serif;background:#eef2f7;display:flex;min-height:100vh}
        .sidebar{width:210px;flex-shrink:0;background:#0f2044;color:#fff;display:flex;flex-direction:column;padding:0 0 24px;position:fixed;top:0;left:0;height:100vh}
        .sidebar-brand{padding:24px 20px 8px}
        .brand-name{font-size:1rem;font-weight:800;letter-spacing:.5px}
        .brand-sub{font-size:.7rem;color:#8ca0c0;margin-top:2px}
        .sidebar-nav{flex:1;margin-top:20px}
        .nav-item{display:flex;align-items:center;gap:12px;padding:11px 20px;font-size:.88rem;font-weight:500;color:#8ca0c0;text-decoration:none;transition:all .15s;border-left:3px solid transparent}
        .nav-item:hover{color:#fff;background:rgba(255,255,255,.06)}
        .nav-item.active{color:#fff;background:rgba(255,255,255,.1);border-left-color:#4a90d9}
        .nav-icon{width:18px;text-align:center;flex-shrink:0}
        .sidebar-bottom{padding:0 20px;display:flex;flex-direction:column;gap:8px}
        .btn-report{background:#1e3a6e;color:#fff;border:none;border-radius:8px;padding:12px 16px;font-size:.85rem;font-weight:600;cursor:pointer;width:100%;display:flex;align-items:center;justify-content:center;gap:8px;transition:background .15s;text-decoration:none}
        .btn-report:hover{background:#2a4f8f}
        .sidebar-link{display:flex;align-items:center;gap:10px;color:#8ca0c0;font-size:.82rem;text-decoration:none;padding:6px 0}
        .sidebar-link:hover{color:#fff}
        .main{margin-left:210px;flex:1;display:flex;flex-direction:column}
        .topbar{background:#fff;border-bottom:1px solid #e2e8f0;padding:0 32px;height:56px;display:flex;align-items:center;gap:16px;position:sticky;top:0;z-index:10}
        .search-box{display:flex;align-items:center;gap:8px;background:#f4f6fb;border-radius:8px;padding:8px 14px;flex:1;max-width:360px}
        .search-box input{border:none;background:transparent;outline:none;font-family:'Inter',sans-serif;font-size:.88rem;color:#3a4d6a;width:100%}
        .search-box input::placeholder{color:#9daec5}
        .topbar-icons{margin-left:auto;display:flex;align-items:center;gap:20px}
        .icon-btn{background:none;border:none;cursor:pointer;color:#5a6a85;font-size:1.1rem;position:relative;padding:4px}
        .badge-dot{position:absolute;top:0;right:0;width:8px;height:8px;background:#e74c3c;border-radius:50%;border:2px solid #fff}
        .topbar-profile{display:flex;align-items:center;gap:8px}
        .profile-name{font-size:.85rem;font-weight:600;color:#1a2744}
        .profile-sub{font-size:.72rem;color:#9daec5;cursor:pointer;background:none;border:none;font-family:'Inter',sans-serif;padding:0}
        .avatar{width:36px;height:36px;border-radius:50%;background:#0f2044;display:flex;align-items:center;justify-content:center;color:#fff;font-size:.85rem;font-weight:700}
        .content{padding:32px}
        .page-header{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:28px}
        .page-title{font-size:1.6rem;font-weight:800;color:#0f2044}
        .page-sub{font-size:.85rem;color:#7a8fa8;margin-top:4px}
        .stats-row{display:grid;grid-template-columns:repeat(3,1fr);gap:20px;margin-bottom:28px}
        .stat-card{background:#fff;border-radius:12px;padding:24px;box-shadow:0 1px 6px rgba(15,32,68,.07)}
        .stat-label{font-size:.65rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#9daec5;margin-bottom:6px}
        .stat-value{font-size:2.2rem;font-weight:800;color:#0f2044;line-height:1;margin-bottom:4px}
        .stat-value.orange{color:#d4870a}
        .stat-value.green{color:#1d8348}
        .stat-value.red{color:#c0392b}
        .stat-hint{font-size:.75rem;color:#9daec5}
        .section{background:#fff;border-radius:12px;box-shadow:0 1px 6px rgba(15,32,68,.07);overflow:hidden}
        .section-header{display:flex;align-items:center;justify-content:space-between;padding:20px 24px 16px;border-bottom:1px solid #f0f4f8}
        .section-title{font-size:1rem;font-weight:700;color:#0f2044}
        .filter-bar{display:flex;align-items:center;gap:12px;padding:14px 24px;border-bottom:1px solid #f0f4f8;flex-wrap:wrap}
        .filter-search{display:flex;align-items:center;gap:8px;background:#f4f6fb;border-radius:8px;padding:8px 14px;flex:1;min-width:200px;max-width:320px;border:1px solid #edf2f7}
        .filter-search input{border:none;background:transparent;outline:none;font-family:'Inter',sans-serif;font-size:.85rem;color:#3a4d6a;width:100%}
        .filter-search input::placeholder{color:#9daec5}
        .filter-select{border:1px solid #d1dce8;border-radius:8px;padding:8px 14px;font-size:.82rem;font-family:'Inter',sans-serif;color:#3a4d6a;background:#fff;outline:none;cursor:pointer}
        .table-wrap{overflow-x:auto}
        table{width:100%;border-collapse:collapse}
        thead th{padding:10px 20px;font-size:.65rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#9daec5;text-align:left;background:#f8fafc;border-bottom:1px solid #edf2f7;white-space:nowrap}
        tbody td{padding:14px 20px;font-size:.88rem;color:#1a2744;border-bottom:1px solid #f0f4f8;vertical-align:middle}
        tbody tr:last-child td{border-bottom:none}
        tbody tr:hover td{background:#fafbfd}
        .po-id{font-weight:700;font-size:.82rem;color:#0f2044}
        .item-name{font-weight:600;color:#0f2044;font-size:.88rem}
        .item-qty{font-size:.75rem;color:#9daec5;margin-top:2px}
        .date-issued{font-size:.82rem;color:#5a6a85;white-space:nowrap}
        .delivery-chip{display:inline-flex;align-items:flex-start;gap:6px;background:#eef2f7;border-radius:7px;padding:6px 10px;font-size:.78rem;color:#3a4d6a;line-height:1.4}
        .delivery-pending{color:#9daec5;font-size:.82rem}
        .badge{display:inline-flex;align-items:center;padding:5px 14px;border-radius:20px;font-size:.72rem;font-weight:700;letter-spacing:.5px;text-transform:capitalize;white-space:nowrap}
        .badge-pending{background:#fef3e2;color:#d4870a;border:1px solid #fde0a0}
        .badge-approved{background:#e8f8f0;color:#1d8348;border:1px solid #a9dfbf}
        .badge-received{background:#e8f8f0;color:#1d8348;border:1px solid #a9dfbf}
        .badge-rejected{background:#fdedec;color:#c0392b;border:1px solid #f5b7b1}
        .action-btns{display:flex;align-items:center;gap:8px;flex-wrap:wrap}
        .btn{display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border-radius:7px;font-size:.78rem;font-weight:600;cursor:pointer;border:1px solid transparent;transition:all .15s;font-family:'Inter',sans-serif;text-decoration:none;white-space:nowrap}
        .btn-approve{background:#e8f8f0;color:#1d8348;border-color:#a9dfbf}
        .btn-approve:hover{background:#d0f0e0}
        .btn-reject{background:#fdedec;color:#c0392b;border-color:#f5b7b1}
        .btn-reject:hover{background:#fad7d2}
        .btn-delivery{background:#eef2fb;color:#3a7bd5;border-color:#b3c6ef}
        .btn-delivery:hover{background:#dce8f8}
        .btn-primary{background:#0f2044;color:#fff;border-color:#0f2044}
        .btn-primary:hover{background:#182e5e}
        .btn-cancel{background:#f4f6fb;color:#5a6a85;border-color:#e2e8f0}
        .btn-cancel:hover{background:#e8ecf4}
        /* Inline action row */
        .action-row{background:#f8fafd}
        .action-inner{padding:20px 24px;display:flex;flex-direction:column;gap:16px}
        .action-title{font-size:.88rem;font-weight:700;color:#0f2044}
        .form-row{display:flex;align-items:flex-end;gap:16px;flex-wrap:wrap}
        .form-group{display:flex;flex-direction:column;gap:5px}
        .form-label{font-size:.68rem;font-weight:600;letter-spacing:.5px;text-transform:uppercase;color:#7a8fa8}
        .form-input{border:1px solid #d1dce8;border-radius:7px;padding:8px 12px;font-family:'Inter',sans-serif;font-size:.85rem;color:#1a2744;outline:none;background:#fff}
        .form-input:focus{border-color:#0f2044}
        .action-sep{height:1px;background:#edf2f7;margin:0 24px}
        .reject-row{padding:0 24px 16px;display:flex;gap:10px;align-items:center}
        .pagination-bar{display:flex;align-items:center;justify-content:space-between;padding:14px 24px;border-top:1px solid #f0f4f8;font-size:.82rem;color:#7a8fa8}
        .flash{padding:12px 20px;border-radius:8px;font-size:.88rem;margin-bottom:20px}
        .flash-success{background:#e8f8f0;color:#1d8348;border:1px solid #a9dfbf}
        .flash-error{background:#fdedec;color:#c0392b;border:1px solid #f5b7b1}
        .empty-state{padding:48px;text-align:center;color:#9daec5;font-size:.9rem}
    </style>
</head>
<body>

<!-- Sidebar -->
@include('supplier.partials.sidebar', ['active' => 'purchase_orders'])

<!-- Main -->
<div class="main">
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
                <div>
                    <div class="profile-name">{{ auth()->user()->name }}</div>
                    <form action="{{ route('logout') }}" method="POST" style="display:inline">@csrf
                        <button type="submit" class="profile-sub">Logout</button>
                    </form>
                </div>
                <div class="avatar">{{ strtoupper(substr(auth()->user()->name,0,1)) }}</div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="page-header">
            <div>
                <div class="page-title">Purchase Orders</div>
                <div class="page-sub">All purchase orders assigned to your supplier account.</div>
            </div>
        </div>

        @if(session('success'))
            <div class="flash flash-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="flash flash-error">{{ session('error') }}</div>
        @endif

        <!-- Stats -->
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-label">Pending</div>
                <div class="stat-value orange">{{ $orders->where('status','Pending')->count() }}</div>
                <div class="stat-hint">Awaiting your response</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Approved</div>
                <div class="stat-value green">{{ $orders->where('status','Approved')->count() }}</div>
                <div class="stat-hint">In processing</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Rejected</div>
                <div class="stat-value red">{{ $orders->where('status','Rejected')->count() }}</div>
                <div class="stat-hint">Declined orders</div>
            </div>
        </div>

        <!-- Table -->
        <div class="section">
            <div class="section-header">
                <div class="section-title">My Purchase Orders</div>
            </div>
            <div class="filter-bar">
                <div class="filter-search">
                    <svg width="14" height="14" fill="none" stroke="#9daec5" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                    <input type="text" placeholder="Search PO ID or Item..." id="tableSearch">
                </div>
                <select class="filter-select" id="statusFilter">
                    <option value="">All Status</option>
                    <option value="Pending">Pending</option>
                    <option value="Approved">Approved</option>
                    <option value="Rejected">Rejected</option>
                    <option value="Received">Received</option>
                </select>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>PO ID</th>
                            <th>Item Detail</th>
                            <th>Date Issued</th>
                            <th>Delivery Schedule</th>
                            <th>Final Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                    @forelse($orders as $order)
                        @php
                            $s   = $order->status;
                            $cls = match(strtolower($s)) {
                                'pending' => 'badge-pending',
                                'approved' => 'badge-approved',
                                'received' => 'badge-received',
                                default => 'badge-rejected',
                            };
                            $hasDelivery = $order->delivery && $order->delivery->delivery_date;
                        @endphp
                        <tr data-po="{{ strtolower($order->po_number) }}" data-item="{{ strtolower(optional($order->item)->name ?? '') }}" data-status="{{ $s }}">
                            <td><div class="po-id">#{{ $order->po_number }}</div></td>
                            <td>
                                <div class="item-name">{{ optional($order->item)->name ?? 'N/A' }}</div>
                                <div class="item-qty">Qty: {{ number_format($order->quantity) }} units</div>
                            </td>
                            <td>
                                <div class="date-issued">
                                    {{ $order->order_date ? $order->order_date->format('M d, Y') : '—' }}
                                </div>
                            </td>
                            <td>
                                @if($hasDelivery)
                                    <div class="delivery-chip">
                                        <svg width="13" height="13" fill="none" stroke="#4a90d9" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                        <div>
                                            {{ $order->delivery->delivery_date->format('M d,') }}<br>
                                            {{ $order->delivery->delivery_time ? \Carbon\Carbon::parse($order->delivery->delivery_time)->format('g:i A') : '' }}
                                        </div>
                                    </div>
                                @else
                                    <span class="delivery-pending">{{ $s === 'Pending' ? 'Pending' : 'Not set' }}</span>
                                @endif
                            </td>
                            <td>RM {{ number_format($order->total_amount ?? 0, 2) }}</td>
                            <td><span class="badge {{ $cls }}">{{ ucfirst($s) }}</span></td>
                            <td>
                                    @if($s === 'Pending')
                                        <button class="btn btn-approve" onclick="toggleRow('approve-{{ $order->id }}')">✓ Approve</button>
                                        <button class="btn btn-reject" onclick="toggleRow('reject-{{ $order->id }}')">✕ Reject</button>
                                    @elseif($s === 'Approved' || $s === 'Received')
                                        @if($s === 'Approved' && !$hasDelivery)
                                            <button class="btn btn-delivery" onclick="toggleRow('delivery-{{ $order->id }}')">📅 Set Delivery</button>
                                        @endif
                                        @if($order->invoice)
                                            <a href="{{ route('supplier.invoices.show', $order->invoice) }}" class="btn" style="border: 1px solid #d1dce8; color: #3a4d6a; background: #fff;">👁 View Invoice</a>
                                        @endif
                                    @else
                                        <span style="color:#9daec5;font-size:.82rem">—</span>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        {{-- Approve Form --}}
                        @if($s === 'Pending')
                        <tr id="approve-{{ $order->id }}" class="action-row" style="display:none">
                            <td colspan="7">
                                <div class="action-inner">
                                    <div class="action-title">✓ Approve Order #{{ $order->po_number }} — Set Delivery Schedule</div>
                                    <form action="{{ route('supplier.orders.confirm', $order) }}" method="POST"
                                          onsubmit="return validateDeliveryForm(this,
                                              'confirm_delivery_date_{{ $order->id }}',
                                              'confirm_delivery_time_{{ $order->id }}')">
                                        @csrf
                                        <input type="hidden" name="status" value="Approved">
                                        <div class="form-row">
                                            <div class="form-group">
                                                <label class="form-label">Delivery Date</label>
                                                <input type="date"
                                                       name="delivery_date"
                                                       class="form-input"
                                                       id="confirm_delivery_date_{{ $order->id }}"
                                                       min="{{ now()->addDay()->toDateString() }}"
                                                       required
                                                       onchange="validateDeliveryDate(this, 'confirm_delivery_time_{{ $order->id }}')">
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label">Delivery Time</label>
                                                <input type="time"
                                                       name="delivery_time"
                                                       class="form-input"
                                                       id="confirm_delivery_time_{{ $order->id }}"
                                                       min="08:00"
                                                       max="17:00"
                                                       onchange="validateDeliveryTime(this, 'confirm_delivery_date_{{ $order->id }}')">
                                            </div>
                                            <button type="button" class="btn btn-cancel" onclick="toggleRow('approve-{{ $order->id }}')">Cancel</button>
                                            <button type="submit" class="btn btn-primary">Confirm Approval</button>
                                        </div>
                                        <small style="color:#9daec5;font-size:.72rem;display:block;margin-top:4px;">
                                            Mon–Fri: 08:00–17:00 · Saturday: 08:00–12:00 · Sunday: not allowed
                                        </small>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        {{-- Reject Form --}}
                        <tr id="reject-{{ $order->id }}" class="action-row" style="display:none">
                            <td colspan="7">
                                <div class="action-inner">
                                    <div class="action-title" style="color:#c0392b">✕ Reject Order #{{ $order->po_number }}</div>
                                    <form action="{{ route('supplier.orders.confirm', $order) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="status" value="Rejected">
                                        <div class="form-row">
                                            <button type="button" class="btn btn-cancel" onclick="toggleRow('reject-{{ $order->id }}')">Cancel</button>
                                            <button type="submit" class="btn btn-reject">Confirm Rejection</button>
                                        </div>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endif

                        {{-- Set Delivery for already-Approved orders --}}
                        @if($s === 'Approved' && !$hasDelivery)
                        <tr id="delivery-{{ $order->id }}" class="action-row" style="display:none">
                            <td colspan="7">
                                <div class="action-inner">
                                    <div class="action-title">📅 Set Delivery for Order #{{ $order->po_number }}</div>
                                    <form action="{{ route('supplier.orders.delivery', $order) }}" method="POST"
                                          onsubmit="return validateDeliveryForm(this,
                                              'set_delivery_date_{{ $order->id }}',
                                              'set_delivery_time_{{ $order->id }}')">
                                        @csrf
                                        <div class="form-row">
                                            <div class="form-group">
                                                <label class="form-label">Delivery Date</label>
                                                <input type="date"
                                                       name="delivery_date"
                                                       class="form-input"
                                                       id="set_delivery_date_{{ $order->id }}"
                                                       min="{{ now()->addDay()->toDateString() }}"
                                                       required
                                                       onchange="validateDeliveryDate(this, 'set_delivery_time_{{ $order->id }}')">
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label">Delivery Time</label>
                                                <input type="time"
                                                       name="delivery_time"
                                                       class="form-input"
                                                       id="set_delivery_time_{{ $order->id }}"
                                                       min="08:00"
                                                       max="17:00"
                                                       onchange="validateDeliveryTime(this, 'set_delivery_date_{{ $order->id }}')">
                                            </div>
                                            <button type="button" class="btn btn-cancel" onclick="toggleRow('delivery-{{ $order->id }}')">Cancel</button>
                                            <button type="submit" class="btn btn-primary">Save Delivery</button>
                                        </div>
                                        <small style="color:#9daec5;font-size:.72rem;display:block;margin-top:4px;">
                                            Mon–Fri: 08:00–17:00 · Saturday: 08:00–12:00 · Sunday: not allowed
                                        </small>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endif

                    @empty
                        <tr><td colspan="7" class="empty-state">No purchase orders found.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="pagination-bar">
                <div id="paginationInfo">Showing {{ $orders->count() }} result{{ $orders->count() !== 1 ? 's' : '' }}</div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleRow(id) {
    const el = document.getElementById(id);
    if (!el) return;
    document.querySelectorAll('.action-row').forEach(r => { if (r.id !== id) r.style.display = 'none'; });
    el.style.display = el.style.display === 'none' ? 'table-row' : 'none';
}

const tableSearch  = document.getElementById('tableSearch');
const statusFilter = document.getElementById('statusFilter');

function filterRows() {
    const q = tableSearch.value.toLowerCase();
    const status = statusFilter.value;
    document.querySelectorAll('#tableBody tr:not(.action-row)').forEach(row => {
        const po   = row.dataset.po || '';
        const item = row.dataset.item || '';
        const s    = row.dataset.status || '';
        const matchQ = !q || po.includes(q) || item.includes(q);
        const matchS = !status || s === status;
        row.style.display = matchQ && matchS ? '' : 'none';
    });
}

tableSearch.addEventListener('input', filterRows);
statusFilter.addEventListener('change', filterRows);

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

    if (day !== 6 && day !== 0) {
        if (time < '08:00' || time > '17:00') {
            Swal.fire({
                icon: 'warning',
                title: 'Invalid Delivery Time',
                text: '⚠ Weekday delivery time must be between 8:00 AM and 5:00 PM.',
                confirmButtonColor: '#0f2044'
            });
            timeInput.value = '';
            return;
        }
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

    const selected = new Date(dateInput.value + 'T00:00:00');
    const day = selected.getDay();

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
</script>
</body>
</html>
