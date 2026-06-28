<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Return Requests – Supplier | 22UniMart</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: #eef2f7; display: flex; min-height: 100vh; }

        /* ── Sidebar ── */
        .sidebar { width: 210px; flex-shrink: 0; background: #0f2044; color: #fff; display: flex; flex-direction: column; padding: 0 0 24px; position: fixed; top: 0; left: 0; height: 100vh; z-index: 50; }
        .sidebar-brand { padding: 20px 20px 12px; display: flex; align-items: center; gap: 12px; border-bottom: 1px solid rgba(255,255,255,0.07); margin-bottom: 10px; }
        .brand-sq { width: 32px; height: 32px; background: #fff; color: #0f2044; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: .9rem; font-weight: 800; flex-shrink: 0; }
        .brand-name { font-size: .9rem; font-weight: 800; }
        .brand-sub  { font-size: .68rem; color: #6b84a3; }
        .sidebar-nav { flex: 1; }
        .nav-item { display: flex; align-items: center; gap: 12px; padding: 11px 20px; font-size: .88rem; font-weight: 500; color: #7a95b5; text-decoration: none; border-left: 3px solid transparent; transition: all .15s; }
        .nav-item:hover { color: #fff; background: rgba(255,255,255,0.06); }
        .nav-item.active { color: #fff; background: rgba(255,255,255,0.1); border-left-color: #10b981; font-weight: 700; }
        .nav-icon { width: 18px; text-align: center; flex-shrink: 0; }
        .sidebar-bottom { padding: 16px 20px 0; border-top: 1px solid rgba(255,255,255,0.07); margin-top: auto; display: flex; flex-direction: column; gap: 8px; }
        .btn-report { background: #1e3a6e; color: #fff; border: none; border-radius: 8px; padding: 12px 16px; font-size: 0.85rem; font-weight: 600; cursor: pointer; width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px; transition: background 0.15s; }
        .btn-report:hover { background: #2a4f8f; }

        /* ── Layout ── */
        .main { margin-left: 210px; flex: 1; display: flex; flex-direction: column; min-height: 100vh; }
        .topbar { background: #fff; border-bottom: 1px solid #e2e8f0; padding: 0 32px; height: 56px; display: flex; align-items: center; gap: 16px; position: sticky; top: 0; z-index: 10; }
        .topbar-label { font-size: .9rem; font-weight: 700; color: #0D1B2A; }
        .topbar-right { margin-left: auto; display: flex; align-items: center; gap: 20px; }
        .avatar { width: 36px; height: 36px; border-radius: 50%; background: #0f2044; display: flex; align-items: center; justify-content: center; color: #fff; font-size: .85rem; font-weight: 700; flex-shrink: 0; }
        .topbar-name { font-size: .85rem; font-weight: 600; color: #1a2744; }
        .topbar-logout { background: none; border: none; font-size: .72rem; color: #9daec5; cursor: pointer; font-family: inherit; padding: 0; }

        /* ── Content ── */
        .content { padding: 32px; }
        .page-header { margin-bottom: 24px; }
        .page-title { font-size: 1.6rem; font-weight: 800; color: #0D1B2A; }
        .page-sub   { font-size: .85rem; color: #7a8fa8; margin-top: 5px; }

        /* ── Flash ── */
        .flash { padding: 13px 20px; border-radius: 8px; font-size: .88rem; margin-bottom: 20px; font-weight: 500; }
        .flash-success { background: #e8f8f0; color: #1d8348; border: 1px solid #a9dfbf; }
        .flash-error   { background: #fdedec; color: #c0392b; border: 1px solid #f5b7b1; }

        /* ── Filter bar ── */
        .filter-bar { background: #fff; border-radius: 12px; padding: 14px 20px; box-shadow: 0 1px 6px rgba(15,32,68,0.07); margin-bottom: 20px; display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
        .filter-select { border: 1px solid #e2e8f0; border-radius: 8px; padding: 8px 14px; font-size: .85rem; font-family: 'Inter', sans-serif; color: #3a4d6a; background: #f8fafc; outline: none; cursor: pointer; }
        .filter-select:focus { border-color: #0f2044; }
        .filter-btn { background: #0f2044; color: #fff; border: none; border-radius: 8px; padding: 8px 20px; font-size: .85rem; font-weight: 600; cursor: pointer; font-family: 'Inter', sans-serif; transition: background .15s; }
        .filter-btn:hover { background: #1e3a5f; }
        .filter-clear { background: #fff; color: #7a8fa8; border: 1px solid #e2e8f0; border-radius: 8px; padding: 8px 18px; font-size: .85rem; font-weight: 500; cursor: pointer; font-family: 'Inter', sans-serif; text-decoration: none; transition: all .15s; }
        .filter-clear:hover { border-color: #94a3b8; color: #374151; }

        /* ── Table card ── */
        .table-card { background: #fff; border-radius: 12px; box-shadow: 0 1px 6px rgba(15,32,68,0.07); overflow: hidden; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        thead tr { background: #f8fafc; border-bottom: 2px solid #eef2f7; }
        thead th { padding: 16px 24px; font-size: .65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #9daec5; text-align: left; white-space: nowrap; }
        tbody tr { border-bottom: 1px solid #f4f6fb; transition: background .12s; }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: #f8fafd; }
        tbody td { padding: 20px 24px; font-size: .85rem; color: #1a2744; vertical-align: middle; }

        /* ── Cell styles ── */
        .cn-id  { font-size: .72rem; font-weight: 700; color: #3b82f6; font-family: monospace; text-decoration: none; }
        .cn-id:hover { text-decoration: underline; }
        .cn-amt { font-size: .72rem; color: #16a34a; font-weight: 600; }
        .cn-amt { font-size: .72rem; color: #16a34a; font-weight: 600; }

        /* ── Badges ── */
        .badge { display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; border-radius: 20px; font-size: .72rem; font-weight: 700; letter-spacing: .4px; text-transform: uppercase; white-space: nowrap; }
        .badge-expired     { background: #fef3e2; color: #d4870a; }
        .badge-damaged     { background: #fdedec; color: #c0392b; }
        .badge-wrong-item  { background: #f3e8ff; color: #7c3aed; }
        .badge-other       { background: #f1f5f9; color: #64748b; }
        .badge-approved    { background: #dcfce7; color: #15803d; }
        .badge-pending     { background: #fef9c3; color: #a16207; }
        .badge-rejected-st { background: #fee2e2; color: #b91c1c; }
        .badge-dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; display: inline-block; flex-shrink: 0; }

        /* ── Action buttons ── */
        .action-row { display: flex; gap: 8px; align-items: center; flex-wrap: nowrap; }
        
        .btn-action-view { background: #fff; color: #3a4d6a; border: 1px solid #d1dce8; border-radius: 7px; padding: 7px 13px; font-size: .78rem; font-weight: 600; cursor: pointer; font-family: 'Inter', sans-serif; white-space: nowrap; transition: all .15s; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
        .btn-action-view:hover { background: #f8fafc; border-color: #0f2044; color: #0f2044; }
        
        .btn-approve { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; border-radius: 7px; padding: 7px 13px; font-size: .78rem; font-weight: 600; cursor: pointer; font-family: 'Inter', sans-serif; white-space: nowrap; transition: all .15s; display: inline-flex; align-items: center; gap: 6px; }
        .btn-approve:hover { background: #dcfce7; }
        
        .btn-reject  { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; border-radius: 7px; padding: 7px 13px; font-size: .78rem; font-weight: 600; cursor: pointer; font-family: 'Inter', sans-serif; white-space: nowrap; transition: all .15s; display: inline-flex; align-items: center; gap: 6px; }
        .btn-reject:hover { background: #fee2e2; }
        
        .btn-download { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; border-radius: 7px; padding: 7px 13px; font-size: .78rem; font-weight: 600; cursor: pointer; font-family: 'Inter', sans-serif; white-space: nowrap; transition: all .15s; display: inline-flex; align-items: center; gap: 6px; }
        .btn-download:hover { background: #dcfce7; }

        /* ── Table footer ── */
        .table-footer { display: flex; align-items: center; justify-content: space-between; padding: 14px 20px; border-top: 1px solid #f0f4f8; flex-wrap: wrap; gap: 12px; }
        .pagination-bar { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-top: 1px solid #f0f4f8; font-size: .82rem; color: #7a8fa8; flex-wrap: wrap; gap: 12px; width: 100%; }
        .pagination-links { display: flex; align-items: center; gap: 4px; }
        .page-btn { width: 32px; height: 32px; border-radius: 7px; border: 1px solid #e2e8f0; background: #fff; color: #3a4d6a; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; font-size: .85rem; text-decoration: none; transition: all .12s; font-weight: 600; }
        .page-btn:hover { background: #f4f6fb; }
        .page-btn.active { background: #0f2044; color: #fff; border-color: #0f2044; }
        .page-btn.disabled { color: #c0cfe0; cursor: not-allowed; pointer-events: none; opacity: 1; }

        /* ── Empty state ── */
        .empty-state { padding: 60px 20px; text-align: center; color: #9daec5; }
        .empty-icon { font-size: 2.5rem; margin-bottom: 12px; }
        .empty-text { font-size: .95rem; font-weight: 600; color: #7a8fa8; }

        /* ── Custom Modal ── */
        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.55); z-index: 999; align-items: center; justify-content: center; padding: 20px; }
        .modal-overlay.open { display: flex; }
        .modal-box { background: #fff; border-radius: 14px; width: 100%; max-width: 500px; box-shadow: 0 24px 64px rgba(0,0,0,0.22); animation: modalIn .2s ease; overflow: hidden; }
        @keyframes modalIn { from { opacity: 0; transform: translateY(-16px) scale(.97); } to { opacity: 1; transform: none; } }
        .modal-header { display: flex; align-items: center; justify-content: space-between; padding: 20px 24px 16px; border-bottom: 1px solid #f0f4f8; }
        .modal-title { font-size: 1rem; font-weight: 800; color: #0D1B2A; }
        .modal-close-btn { background: none; border: none; font-size: 1.1rem; cursor: pointer; color: #9ca3af; line-height: 1; padding: 2px 6px; border-radius: 4px; transition: all .15s; }
        .modal-close-btn:hover { color: #374151; background: #f1f5f9; }
        .modal-subtext { font-size: .82rem; color: #6b7280; padding: 12px 24px 0; }
        .modal-body { padding: 16px 24px 20px; display: flex; flex-direction: column; gap: 14px; }
        .form-group { display: flex; flex-direction: column; gap: 6px; }
        .form-label { font-size: .78rem; font-weight: 700; color: #374151; letter-spacing: .3px; text-transform: uppercase; }
        .form-input { width: 100%; border: 1.5px solid #e5e9f0; border-radius: 8px; padding: 10px 14px; font-size: .88rem; font-family: 'Inter', sans-serif; color: #0D1B2A; outline: none; transition: border-color .15s; background: #fafbfc; }
        .form-input:focus { border-color: #0D1B2A; background: #fff; }
        textarea.form-input { resize: vertical; min-height: 80px; }
        .modal-footer { padding: 16px 24px; border-top: 1px solid #f0f4f8; display: flex; gap: 10px; }
        .btn-cancel { flex: 1; background: #fff; color: #374151; border: 1.5px solid #e5e9f0; border-radius: 8px; padding: 10px; font-size: .85rem; font-weight: 600; cursor: pointer; font-family: 'Inter', sans-serif; transition: all .15s; }
        .btn-cancel:hover { border-color: #94a3b8; }
        .btn-submit { flex: 2; background: #0D1B2A; color: #fff; border: none; border-radius: 8px; padding: 10px; font-size: .85rem; font-weight: 700; cursor: pointer; font-family: 'Inter', sans-serif; transition: background .15s; }
        .btn-submit:hover { background: #1e3a5f; }
    </style>
</head>
<body>

{{-- ── Sidebar ── --}}
<!-- Sidebar -->
@include('supplier.partials.sidebar', ['active' => 'returns'])

{{-- ── Main ── --}}
<div class="main">
    <div class="topbar">
        <div class="topbar-label">Return Requests</div>
        <div class="topbar-right" style="margin-left: auto; display: flex; align-items: center; gap: 20px;">
            @include('supplier.components.topbar-profile')
        </div>
    </div>

    <div class="content">

        {{-- Page header --}}
        <div class="page-header">
            <h1 class="page-title">Return Requests</h1>
            <p class="page-sub">Review and manage return requests submitted by owners for items you supplied.</p>
        </div>

        {{-- Flash --}}
        @if(session('success'))
            <div class="flash flash-success">✓ {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="flash flash-error">✕ {{ session('error') }}</div>
        @endif

        {{-- Filter bar --}}
        <form method="GET" action="{{ route('supplier.returns.index') }}">
            <div class="filter-bar">
                <select name="status" class="filter-select">
                    <option value="">All Statuses</option>
                    <option value="Pending"  {{ request('status') == 'Pending'  ? 'selected' : '' }}>Pending</option>
                    <option value="Approved" {{ request('status') == 'Approved' ? 'selected' : '' }}>Approved</option>
                    <option value="Rejected" {{ request('status') == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
                <select name="reason" class="filter-select">
                    <option value="">All Reasons</option>
                    <option value="Expired"    {{ request('reason') == 'Expired'    ? 'selected' : '' }}>Expired</option>
                    <option value="Damaged"    {{ request('reason') == 'Damaged'    ? 'selected' : '' }}>Damaged</option>
                </select>
                <button type="submit" class="filter-btn">Apply</button>
                <a href="{{ route('supplier.returns.index') }}" class="filter-clear">Clear</a>
            </div>
        </form>

        {{-- Table --}}
        <div class="table-card">
            <table>
                <thead>
                    <tr>
                        <th style="width: 180px;">Request ID</th>
                        <th style="width: 35%;">Items</th>
                        <th style="width: 130px;">Created At</th>
                        <th style="width: 120px;">Status</th>
                        <th style="width: 220px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($returns as $rr)
                    <tr>
                        {{-- Request ID --}}
                        <td><span style="font-size: .82rem; font-weight: 600; color: #3a4d6a; font-family: monospace;">#{{ $rr->return_number }}</span></td>

                        {{-- Items --}}
                        <td>
                            @php
                                $itemNames = $rr->lines->pluck('item.name')->filter()->unique();
                                $firstName = $itemNames->first() ?? optional($rr->item)->name ?? 'Unknown Item';
                                $itemCount = $itemNames->count();
                            @endphp
                            <div style="font-weight: 600; color: #0D1B2A; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">
                                {{ $firstName }}
                                @if($itemCount > 1)
                                    <span style="color: #7a8fa8; font-size: .8rem; font-weight: 500;">, +{{ $itemCount - 1 }} more</span>
                                @endif
                            </div>
                        </td>

                        {{-- Created At --}}
                        <td style="color:#7a8fa8;">{{ $rr->created_at->format('M d, Y') }}</td>

                        {{-- Status --}}
                        <td>
                            @if($rr->status === 'Pending')
                                <span class="badge badge-pending"><span class="badge-dot"></span> Pending</span>
                            @elseif($rr->status === 'Approved' || $rr->status === 'Credit Applied')
                                <span class="badge badge-approved"><span class="badge-dot"></span> Approved</span>
                            @else
                                <span class="badge badge-rejected-st"><span class="badge-dot"></span> Rejected</span>
                            @endif
                        </td>

                        {{-- Action --}}
                        <td>
                            @if($rr->status === 'Pending')
                                @php
                                    $itemNames = $rr->lines->pluck('item.name')->filter()->unique();
                                    $returnLines = $rr->lines->map(function($line) {
                                        return [
                                            'id' => $line->id,
                                            'name' => optional($line->item)->name ?? 'Unknown Item',
                                            'reason' => $line->reason,
                                            'quantity' => $line->quantity,
                                            'unit_price' => number_format($line->unit_price, 2, '.', ''),
                                            'subtotal' => number_format($line->subtotal, 2, '.', ''),
                                        ];
                                    });
                                @endphp
                                <div class="action-row">
                                    <button type="button" class="btn-action-view" onclick="openApprovalModal({{ $rr->id }}, '{{ $rr->return_number }}', {{ $rr->lines->sum('quantity') }}, '{{ addslashes($itemNames->join(', ')) }}', {{ number_format($rr->credit_amount, 2, '.', '') }}, this)" data-return-lines="{{ $returnLines->toJson() }}">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                        View
                                    </button>
                                    <button type="button" class="btn-approve"
                                        onclick="openApprovalModal({{ $rr->id }}, '{{ $rr->return_number }}', {{ $rr->lines->sum('quantity') }}, '{{ addslashes($itemNames->join(', ')) }}', {{ number_format($rr->credit_amount, 2, '.', '') }}, this)"
                                        data-return-lines="{{ $returnLines->toJson() }}">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg> Approve
                                    </button>
                                    <form action="{{ route('supplier.returns.status', $rr) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <input type="hidden" name="rejection_reason" value="">
                                        <button name="status" value="Rejected" class="btn-reject" onclick="return promptRejectReason(this.form)">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg> Reject
                                        </button>
                                    </form>
                                </div>
                            @elseif(in_array($rr->status, ['Approved', 'Credit Applied']) && $rr->creditNote)
                                <a href="{{ route('supplier.credit-notes.export-single-pdf', $rr->creditNote) }}" class="btn-download" style="text-decoration:none;">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2 2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg> Download
                                </a>
                            @else
                                <span style="color:#9daec5;font-size:.8rem;">Actioned</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5">
                        <div class="empty-state">
                            <div class="empty-icon">↩</div>
                            <div class="empty-text">No return requests found</div>
                        </div>
                    </td></tr>
                @endforelse
                </tbody>
            </table>

            <div class="pagination-bar">
                <span>Showing {{ $returns->firstItem() ?? 0 }} to {{ $returns->lastItem() ?? 0 }} of {{ number_format($returns->total()) }} entries</span>
                <div class="pagination-links">
                    <a href="{{ $returns->previousPageUrl() ?? '#' }}" class="page-btn {{ $returns->onFirstPage() ? 'disabled' : '' }}">‹</a>
                    @foreach($returns->getUrlRange(1, $returns->lastPage()) as $page => $url)
                        @if($page <= 3 || $page > $returns->lastPage() - 1 || abs($page - $returns->currentPage()) <= 1)
                            <a href="{{ $url }}" class="page-btn {{ $page == $returns->currentPage() ? 'active' : '' }}">{{ $page }}</a>
                        @elseif($page == 4 && $returns->currentPage() > 5)
                            <span class="page-btn disabled">…</span>
                        @endif
                    @endforeach
                    <a href="{{ $returns->nextPageUrl() ?? '#' }}" class="page-btn {{ !$returns->hasMorePages() ? 'disabled' : '' }}">›</a>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- ── Approve Modal ── --}}
<div class="modal-overlay" id="approvalModal">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-title">Approve Return Request</div>
            <button class="modal-close-btn" onclick="closeApprovalModal()">✕</button>
        </div>
        <p class="modal-subtext">Credit amount is auto-calculated from the returned quantity and item unit price. Add an optional approval note if needed.</p>
        <form id="approvalForm" method="POST">
            @csrf
            <input type="hidden" name="status" value="Approved">
            <div class="modal-body">
                <div style="background:#f8fafc;border:1px solid #e5e9f0;border-radius:8px;padding:12px 14px;">
                    <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#9ca3af;margin-bottom:4px;">Return Request</div>
                    <div style="font-size:.9rem;font-weight:700;color:#0D1B2A;" id="modalRRNumber">—</div>
                    <div style="font-size:.78rem;color:#6b7280;margin-top:2px;" id="modalRRDetail">—</div>
                </div>
                <div class="form-group">
                    <label class="form-label">Credit Amount (RM)</label>
                    <input type="hidden" id="credit_amount" name="credit_amount" required>
                    <input type="text" class="form-input" id="credit_amount_display" disabled value="RM 0.00">
                </div>
                <div class="form-group" style="overflow-x:auto;max-height:220px;">
                    <label class="form-label">Returned Item Details</label>
                    <table style="width:100%;border-collapse:collapse;font-size:.88rem;">
                        <thead>
                            <tr>
                                <th style="text-align:left;padding:8px 10px;border-bottom:1px solid #e5e9f0;color:#7a8fa8;font-weight:700;">Item</th>
                                <th style="text-align:right;padding:8px 10px;border-bottom:1px solid #e5e9f0;color:#7a8fa8;font-weight:700;">Qty</th>
                                <th style="text-align:right;padding:8px 10px;border-bottom:1px solid #e5e9f0;color:#7a8fa8;font-weight:700;">Unit</th>
                                <th style="text-align:right;padding:8px 10px;border-bottom:1px solid #e5e9f0;color:#7a8fa8;font-weight:700;">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="modalItemLines">
                            <tr>
                                <td colspan="4" style="padding:10px;color:#9ca3af;">Select a return request to preview returned item details.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="form-group">
                    <label class="form-label" for="reason">Reason / Note</label>
                    <textarea class="form-input" id="reason" name="reason" rows="3" placeholder="Optional note for this approval..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeApprovalModal()">Cancel</button>
                <button type="submit" class="btn-submit">✔ Approve & Create Credit Note</button>
            </div>
        </form>
    </div>
</div>

<script>
function openApprovalModal(rrId, rrNumber, qty, itemName, amount, btn) {
    document.getElementById('modalRRNumber').textContent = '#' + rrNumber;
    document.getElementById('modalRRDetail').textContent = itemName ? itemName + ' · ' + qty + ' unit(s)' : qty + ' unit(s)';
    document.getElementById('approvalForm').action = '/supplier/return-requests/' + rrId + '/status';
    document.getElementById('credit_amount').value = parseFloat(amount).toFixed(2);
    document.getElementById('credit_amount_display').value = 'RM ' + parseFloat(amount).toFixed(2);
    document.getElementById('reason').value = '';
    renderApprovalLines(btn?.dataset?.returnLines ? JSON.parse(btn.dataset.returnLines) : []);
    document.getElementById('approvalModal').classList.add('open');
    document.body.style.overflow = 'hidden';
}

function renderApprovalLines(lines) {
    const tbody = document.getElementById('modalItemLines');
    if (!tbody) return;
    if (!lines.length) {
        tbody.innerHTML = '<tr><td colspan="4" style="padding:10px;color:#9ca3af;">No returned items available for preview.</td></tr>';
        return;
    }

    tbody.innerHTML = lines.map((line, idx) => `
        <tr>
            <td style="padding:10px 10px 8px;font-weight:600;color:#0D1B2A;">
                <input type="hidden" name="lines[${idx}][line_id]" value="${line.id}">
                ${line.name}
            </td>
            <td style="padding:10px 10px 8px;text-align:right;color:#1a2744;">
                <input type="number" name="lines[${idx}][approved_qty]" value="${line.quantity}" min="0" max="${line.quantity}" 
                    style="width: 60px; padding: 4px; border: 1px solid #e5e9f0; border-radius: 4px; text-align: center;"
                    oninput="recalculateModalCredit()"> / ${line.quantity}
            </td>
            <td style="padding:10px 10px 8px;text-align:right;color:#1a2744;" class="modal-unit-price" data-price="${line.unit_price}">RM ${parseFloat(line.unit_price).toFixed(2)}</td>
            <td style="padding:10px 10px 8px;text-align:right;font-weight:700;color:#0D1B2A;" class="modal-subtotal">RM ${parseFloat(line.subtotal).toFixed(2)}</td>
        </tr>
    `).join('');
}

function recalculateModalCredit() {
    const tbody = document.getElementById('modalItemLines');
    let total = 0;
    
    Array.from(tbody.querySelectorAll('tr')).forEach(tr => {
        const qtyInput = tr.querySelector('input[type="number"]');
        if (!qtyInput) return;
        
        let qty = parseInt(qtyInput.value) || 0;
        const max = parseInt(qtyInput.getAttribute('max'));
        
        if (qty < 0) qty = 0;
        if (qty > max) qty = max;
        qtyInput.value = qty;
        
        const price = parseFloat(tr.querySelector('.modal-unit-price').dataset.price) || 0;
        const subtotal = qty * price;
        tr.querySelector('.modal-subtotal').textContent = 'RM ' + subtotal.toFixed(2);
        
        total += subtotal;
    });
    
    document.getElementById('credit_amount').value = total.toFixed(2);
    document.getElementById('credit_amount_display').value = 'RM ' + total.toFixed(2);
}

function closeApprovalModal() {
    document.getElementById('approvalModal').classList.remove('open');
    document.body.style.overflow = '';
}

function promptRejectReason(form) {
    const reason = window.prompt('Reason for rejection (e.g. expired too long, owner storage damage, invalid return condition):');
    if (!reason || !reason.trim()) {
        return false;
    }
    form.querySelector('input[name="rejection_reason"]').value = reason.trim();
    return true;
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
