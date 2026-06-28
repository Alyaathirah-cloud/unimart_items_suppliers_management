<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Credit Note {{ $creditNote->credit_note_id }} – 22UniMart</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: #eef2f7; display: flex; min-height: 100vh; }

        /* ── Sidebar ── */
        .sidebar { width: 210px; flex-shrink: 0; background: #0f2044; color: #fff; display: flex; flex-direction: column; padding: 0 0 24px 0; position: fixed; top: 0; left: 0; height: 100vh; }
        .sidebar-brand { padding: 20px 20px 4px; display: flex; align-items: center; gap: 12px; }
        .brand-square { width: 32px; height: 32px; background: #fff; color: #0f2044; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 0.9rem; font-weight: 800; flex-shrink: 0; }
        .brand-text .brand-name { font-size: 0.9rem; font-weight: 800; }
        .brand-text .brand-sub  { font-size: 0.68rem; color: #8ca0c0; }
        .sidebar-nav { flex: 1; margin-top: 20px; overflow-y: auto; }
        .nav-item { display: flex; align-items: center; gap: 12px; padding: 11px 20px; font-size: 0.88rem; font-weight: 500; color: #8ca0c0; cursor: pointer; text-decoration: none; transition: all 0.15s; border-left: 3px solid transparent; }
        .nav-item:hover { color: #fff; background: rgba(255,255,255,0.06); }
        .nav-item.active { color: #fff; background: rgba(255,255,255,0.1); border-left-color: #4a90d9; }
        .nav-icon { width: 18px; text-align: center; flex-shrink: 0; }
        .sidebar-bottom { padding: 0 20px; display: flex; flex-direction: column; gap: 8px; }
        .btn-logout { background: #c0392b; color: #fff; border: none; border-radius: 8px; padding: 12px 16px; font-size: 0.85rem; font-weight: 600; cursor: pointer; width: 100%; display: flex; align-items: center; justify-content: center; transition: background 0.15s; }
        .btn-logout:hover { background: #a93226; }

        /* ── Main ── */
        .main { margin-left: 210px; flex: 1; display: flex; flex-direction: column; }

        /* Topbar */
        .topbar { background: #fff; border-bottom: 1px solid #e2e8f0; padding: 0 32px; height: 56px; display: flex; align-items: center; gap: 16px; position: sticky; top: 0; z-index: 10; }
        .topbar-right { margin-left: auto; display: flex; align-items: center; gap: 20px; }
        .avatar { width: 36px; height: 36px; border-radius: 50%; background: #0f2044; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 0.85rem; font-weight: 700; }

        /* Content */
        .content { padding: 32px; }
        .page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 28px; flex-wrap: wrap; gap: 12px; }
        .page-title { font-size: 1.6rem; font-weight: 800; color: #0f2044; }
        .page-sub   { font-size: 0.85rem; color: #7a8fa8; margin-top: 4px; }
        .header-actions { display: flex; gap: 10px; align-items: center; }

        /* Buttons */
        .btn { display: inline-flex; align-items: center; gap: 7px; border-radius: 8px; padding: 9px 18px; font-size: 0.85rem; font-weight: 600; cursor: pointer; font-family: 'Inter', sans-serif; text-decoration: none; border: none; transition: all 0.15s; white-space: nowrap; }
        .btn-dark   { background: #0f2044; color: #fff; }
        .btn-dark:hover { background: #1e3a6e; }
        .btn-outline { background: #fff; color: #3a4d6a; border: 1px solid #e2e8f0; }
        .btn-outline:hover { background: #f4f6fb; border-color: #c5cedb; }
        .btn-back   { background: #fff; color: #7a8fa8; border: 1px solid #e2e8f0; }
        .btn-back:hover { background: #f4f6fb; color: #3a4d6a; }

        /* ── Credit Note Document Card ── */
        .doc-card { background: #fff; border-radius: 16px; box-shadow: 0 2px 16px rgba(15,32,68,0.10); overflow: hidden; max-width: 860px; margin: 0 auto; }

        /* Document Header */
        .doc-header { background: linear-gradient(135deg, #0f2044 0%, #1e3a6e 100%); padding: 36px 40px 28px; color: #fff; display: flex; justify-content: space-between; align-items: flex-start; }
        .doc-brand { }
        .doc-brand-name { font-size: 1.6rem; font-weight: 800; letter-spacing: -0.5px; }
        .doc-brand-tag  { font-size: 0.75rem; color: rgba(255,255,255,0.6); margin-top: 4px; letter-spacing: 0.5px; }
        .doc-title-block { text-align: right; }
        .doc-title { font-size: 1.5rem; font-weight: 800; letter-spacing: 2px; color: #fff; }
        .doc-subtitle { font-size: 0.72rem; color: rgba(255,255,255,0.55); letter-spacing: 1px; text-transform: uppercase; margin-top: 4px; }

        /* Divider Strip */
        .doc-strip { height: 4px; background: linear-gradient(90deg, #4a90d9, #27ae60, #f39c12); }

        /* Meta section: supplier + CN details */
        .doc-meta { display: grid; grid-template-columns: 1fr 1fr; gap: 0; padding: 28px 40px; border-bottom: 1px solid #eef2f7; }
        .meta-block { }
        .meta-block:last-child { text-align: right; }
        .meta-label { font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #9daec5; margin-bottom: 6px; }
        .meta-name  { font-size: 1rem; font-weight: 700; color: #0f2044; margin-bottom: 4px; }
        .meta-contact { font-size: 0.82rem; color: #7a8fa8; line-height: 1.6; }
        .meta-field { margin-bottom: 8px; }
        .meta-field .mf-label { font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #9daec5; }
        .meta-field .mf-value { font-size: 0.9rem; font-weight: 700; color: #0f2044; margin-top: 2px; }
        .meta-field .mf-value.cn-id-val { color: #4a90d9; font-family: monospace; font-size: 1rem; letter-spacing: 1px; }

        /* Items Table */
        .doc-table-section { padding: 24px 40px; }
        .section-title { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #9daec5; margin-bottom: 14px; }
        .doc-table { width: 100%; border-collapse: collapse; }
        .doc-table thead tr { background: #f8fafc; }
        .doc-table thead th { padding: 11px 14px; font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: #7a8fa8; text-align: left; border-bottom: 2px solid #eef2f7; }
        .doc-table thead th:last-child { text-align: right; }
        .doc-table tbody tr { border-bottom: 1px solid #f4f6fb; }
        .doc-table tbody tr:last-child { border-bottom: none; }
        .doc-table tbody td { padding: 14px 14px; font-size: 0.88rem; color: #3a4d6a; vertical-align: middle; }
        .doc-table tbody td:last-child { text-align: right; font-weight: 700; color: #0f2044; }
        .doc-table tfoot tr { border-top: 2px solid #eef2f7; }
        .doc-table tfoot td { padding: 16px 14px; }

        /* Summary */
        .summary-row { display: flex; justify-content: flex-end; }
        .summary-box { background: #f8fafc; border-radius: 10px; padding: 16px 24px; min-width: 260px; border: 1px solid #eef2f7; }
        .summary-line { display: flex; justify-content: space-between; align-items: center; padding: 6px 0; font-size: 0.85rem; color: #7a8fa8; border-bottom: 1px solid #eef2f7; }
        .summary-line:last-child { border-bottom: none; }
        .summary-line.total { font-size: 1rem; font-weight: 800; color: #0f2044; padding-top: 12px; }
        .summary-line.remaining { color: #1d8348; font-weight: 700; }
        .summary-line .s-label { }
        .summary-line .s-val   { font-weight: 600; color: #0f2044; }
        .summary-line.total .s-val { font-size: 1.15rem; color: #0f2044; }

        /* Additional Info Section */
        .doc-info { padding: 20px 40px 28px; border-top: 1px solid #eef2f7; display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
        .info-item { }
        .info-label { font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #9daec5; margin-bottom: 6px; }
        .info-value { font-size: 0.88rem; font-weight: 600; color: #0f2044; }
        .info-value a { color: #4a90d9; text-decoration: none; }
        .info-value a:hover { text-decoration: underline; }

        /* Status badges */
        .badge { display: inline-flex; align-items: center; gap: 5px; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; letter-spacing: 0.4px; text-transform: uppercase; }
        .badge-dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; display: inline-block; }
        .badge-unused    { background: #e8f8f0; color: #1d8348; }
        .badge-partial   { background: #fef3e2; color: #d4870a; }
        .badge-used      { background: #f4f6fb; color: #7a8fa8; }

        /* Reason badges */
        .badge-reason { display: inline-flex; align-items: center; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
        .badge-expired { background: #fef3e2; color: #d4870a; }
        .badge-damaged { background: #fdedec; color: #c0392b; }

        /* Doc Footer */
        .doc-footer { padding: 16px 40px; background: #f8fafc; border-top: 1px solid #eef2f7; display: flex; align-items: center; justify-content: space-between; }
        .doc-footer-note { font-size: 0.75rem; color: #aab8cc; }
        .doc-footer-brand { font-size: 0.75rem; color: #c5cedb; font-weight: 600; letter-spacing: 0.5px; }

        /* Print styles */
        @media print {
            .sidebar, .topbar, .page-header, .print-hide { display: none !important; }
            body { background: #fff; }
            .main { margin-left: 0; }
            .content { padding: 0; }
            .doc-card { box-shadow: none; border-radius: 0; max-width: 100%; }
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="brand-square">22</div>
        <div class="brand-text">
            <div class="brand-name">22UNIMART</div>
            <div class="brand-sub">Inventory Control</div>
        </div>
    </div>
        <nav class="sidebar-nav">
        <a href="{{ route('staff.dashboard') }}" class="nav-item {{ request()->routeIs('owner.dashboard') ? 'active' : '' }}"><span class="nav-icon">⊞</span> Dashboard</a>
        <a href="{{ route('staff.inventory.index') }}" class="nav-item {{ request()->routeIs('owner.items.*') ? 'active' : '' }}"><span class="nav-icon">📦</span> Inventory</a>
        @if(auth()->user()->isOwner())
            <a href="{{ route('owner.suppliers.index') }}" class="nav-item {{ request()->routeIs('owner.suppliers.*') ? 'active' : '' }}"><span class="nav-icon">🏢</span> Suppliers</a>
            <a href="{{ route('owner.staff.index') }}" class="nav-item {{ request()->routeIs('owner.staff.*') ? 'active' : '' }}"><span class="nav-icon">👥</span> Staff</a>
        @endif
        <a href="{{ route('staff.po.index') }}" class="nav-item {{ request()->routeIs('owner.purchase-orders.*') ? 'active' : '' }}"><span class="nav-icon">🛒</span> Purchase Orders</a>
        <a href="{{ route('staff.rr.index') }}" class="nav-item {{ request()->routeIs('owner.return-requests.*') ? 'active' : '' }}"><span class="nav-icon">↩</span> Return Requests</a>
        <a href="{{ route('staff.invoice.index') }}" class="nav-item {{ request()->routeIs('owner.invoices.*') ? 'active' : '' }}"><span class="nav-icon">📄</span> Invoices</a>
        <a href="{{ route('staff.cn.index') }}" class="nav-item {{ request()->routeIs('owner.credit-notes.*') ? 'active' : '' }}"><span class="nav-icon">📋</span> Credit Notes</a>
        <a href="{{ route('staff.notif.index') }}" class="nav-item {{ request()->routeIs('owner.notifications.*') ? 'active' : '' }}"><span class="nav-icon">🔔</span> Notifications</a>
    </nav>
        <div class="sidebar-bottom">
        <div class="sidebar-link" style="color: #fff; cursor: default; font-weight: bold;">Role: Owner</div>
        <form action="{{ route('logout') }}" method="POST" style="margin: 0; width: 100%;">
            @csrf
            <button type="submit" class="btn-report" style="background: #c0392b;">Logout</button>
        </form>
    </div>
</aside>

<!-- Main -->
<div class="main">
    <!-- Topbar -->
    <div class="topbar">
        <div style="font-size:0.85rem;color:#7a8fa8;">
            <a href="{{ route('staff.cn.index') }}" style="color:#4a90d9;text-decoration:none;font-weight:600;">Credit Notes</a>
            <span style="margin:0 8px;">›</span>
            <span style="color:#0f2044;font-weight:700;">{{ $creditNote->credit_note_id }}</span>
        </div>
                <div class="topbar-right">
            <a href="{{ route('staff.notif.index') }}" class="icon-btn" style="text-decoration:none;">🔔</a>
            @include('staff.components.topbar-profile')
        </div>
    </div>

    <div class="content">

        <!-- Page Header -->
        <div class="page-header print-hide">
            <div>
                <div class="page-title">Credit Note Detail</div>
                <div class="page-sub">Read-only document — auto-generated from approved return request.</div>
            </div>
            <div class="header-actions">
                <a href="{{ route('staff.cn.index') }}" class="btn btn-back">
                    ← Back
                </a>
                <button onclick="window.print()" class="btn btn-dark">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                    Print
                </button>
            </div>
        </div>

        <!-- Credit Note Document -->
        <div class="doc-card">

            <!-- Document Header -->
            <div class="doc-header">
                <div class="doc-brand">
                    <div class="doc-brand-name">22UniMart</div>
                    <div class="doc-brand-tag">Inventory & Supplier Management</div>
                    <div style="margin-top:16px;font-size:0.8rem;color:rgba(255,255,255,0.6);line-height:1.8;">
                        Issued To:<br>
                        <span style="color:#fff;font-weight:600;font-size:0.92rem;">{{ $creditNote->supplier->name ?? 'N/A' }}</span>
                    </div>
                    @if($creditNote->supplier)
                    <div style="font-size:0.78rem;color:rgba(255,255,255,0.5);margin-top:4px;line-height:1.7;">
                        @if($creditNote->supplier->contact_phone)
                            📞 {{ $creditNote->supplier->contact_phone }}<br>
                        @endif
                        @if($creditNote->supplier->contact_email)
                            ✉ {{ $creditNote->supplier->contact_email }}
                        @endif
                    </div>
                    @endif
                </div>
                <div class="doc-title-block">
                    <div class="doc-title">CREDIT NOTE</div>
                    <div class="doc-subtitle">Official Document</div>
                    <div style="margin-top:20px;text-align:right;">
                        <div style="font-size:0.65rem;color:rgba(255,255,255,0.5);text-transform:uppercase;letter-spacing:1px;margin-bottom:4px;">Credit Note No.</div>
                        <div style="font-size:1rem;font-weight:800;color:#fff;font-family:monospace;letter-spacing:1px;">{{ $creditNote->credit_note_id }}</div>
                        <div style="margin-top:10px;font-size:0.65rem;color:rgba(255,255,255,0.5);text-transform:uppercase;letter-spacing:1px;">Date Issued</div>
                        <div style="font-size:0.88rem;font-weight:700;color:#fff;margin-top:4px;">{{ $creditNote->issue_date->format('d F Y') }}</div>
                    </div>
                </div>
            </div>

            <!-- Colour strip -->
            <div class="doc-strip"></div>

            <!-- Items Table -->
            <div class="doc-table-section">
                <div class="section-title">Credit Note Items</div>
                <table class="doc-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Item Name / Description</th>
                            <th style="text-align:right;">Reason</th>
                            <th style="text-align:right;">Unit Price (RM)</th>
                            <th style="text-align:right;">Qty Returned</th>
                            <th style="text-align:right;">Total Amount (RM)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $items = $creditNote->items ?? collect();
                        @endphp
                        @if($items->isEmpty())
                            @php
                                $fallbackLines = $creditNote->returnRequest?->lines ?? collect();
                                $fallbackFirst = $fallbackLines->first();
                                $item = $fallbackFirst?->item ?? null;
                                $qty  = $fallbackLines->sum('quantity');
                                $unitPrice = $fallbackFirst ? (float)($fallbackFirst->unit_price ?? 0) : 0;
                            @endphp
                            <tr>
                                <td style="color:#9daec5;font-size:0.8rem;">01</td>
                                <td>
                                    <div style="font-weight:600;color:#0f2044;">{{ $item?->name ?? '—' }}</div>
                                    @if($item && $item->category)
                                        <div style="font-size:0.75rem;color:#9daec5;margin-top:2px;">{{ $item->category }}</div>
                                    @endif
                                </td>
                                <td style="text-align:right;">{{ optional($fallbackFirst)->reason ?? '—' }}</td>
                                <td style="text-align:right;">{{ number_format($unitPrice, 2) }}</td>
                                <td style="text-align:right;">{{ number_format($qty) }}</td>
                                <td>RM {{ number_format($creditNote->amount, 2) }}</td>
                            </tr>
                        @else
                            @foreach($items as $index => $creditItem)
                                <tr>
                                    <td style="color:#9daec5;font-size:0.8rem;">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</td>
                                    <td>
                                        <div style="font-weight:600;color:#0f2044;">{{ optional($creditItem->item)->name ?? '—' }}</div>
                                        @if($creditItem->item && $creditItem->item->category)
                                            <div style="font-size:0.75rem;color:#9daec5;margin-top:2px;">{{ $creditItem->item->category }}</div>
                                        @endif
                                    </td>
                                    <td style="text-align:right;text-transform:capitalize;">{{ optional($creditItem->returnRequestLine)->reason ?? '—' }}</td>
                                    <td style="text-align:right;">{{ number_format($creditItem->unit_price, 2) }}</td>
                                    <td style="text-align:right;">{{ number_format($creditItem->quantity) }}</td>
                                    <td>RM {{ number_format($creditItem->subtotal, 2) }}</td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>

                <!-- Summary -->
                <div class="summary-row" style="margin-top:20px;">
                    <div class="summary-box">
                        <div class="summary-line">
                            <span class="s-label">Original Invoice Total</span>
                            <span class="s-val">RM {{ number_format(optional($creditNote->invoice)->total_amount ?? 0, 2) }}</span>
                        </div>
                        <div class="summary-line">
                            <span class="s-label">Total Credit Amount</span>
                            <span class="s-val" style="color: #c0392b;">- RM {{ number_format($creditNote->totalAmount(), 2) }}</span>
                        </div>
                        <div class="summary-line total">
                            <span class="s-label">Net to Pay</span>
                            <span class="s-val">RM {{ number_format(max(0, (optional($creditNote->invoice)->total_amount ?? 0) - $creditNote->totalAmount()), 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="doc-info">
                <div class="info-item">
                    <div class="info-label">Linked Return Request</div>
                    <div class="info-value">
                        <a href="{{ route('staff.rr.index') }}">
                            #{{ $creditNote->returnRequest->return_number ?? 'N/A' }}
                        </a>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Return Reason</div>
                    <div class="info-value">
                        @if($creditNote->returnRequest)
                            @php
                                $ownerLineReasons = $creditNote->returnRequest->lines->pluck('reason')->filter()->unique();
                                $ownerReasonDisplay = $ownerLineReasons->count() === 1 ? ucfirst($ownerLineReasons->first()) : ($ownerLineReasons->count() > 1 ? 'Mixed' : null);
                            @endphp
                            @if($ownerReasonDisplay === 'Expired')
                                <span class="badge-reason badge-expired">⏰ Expired</span>
                            @elseif($ownerReasonDisplay === 'Damaged')
                                <span class="badge-reason badge-damaged">⚠ Damaged</span>
                            @elseif($ownerReasonDisplay)
                                <span style="color:#5a6a85;">{{ $ownerReasonDisplay }}</span>
                            @else
                                <span style="color:#9daec5;">—</span>
                            @endif
                        @else
                            <span style="color:#9daec5;">—</span>
                        @endif
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Status</div>
                    <div class="info-value">
                        <span class="badge badge-unused"><span class="badge-dot"></span> {{ $creditNote->status }}</span>
                    </div>
                </div>
            </div>

            <!-- Document Footer -->
            <div class="doc-footer">
                <div class="doc-footer-note">
                    This credit note is auto-generated and is a read-only document. For queries, contact your system administrator.
                </div>
                <div class="doc-footer-brand">22UNIMART</div>
            </div>

        </div>{{-- /.doc-card --}}

    </div>
</div>
</body>
</html>
