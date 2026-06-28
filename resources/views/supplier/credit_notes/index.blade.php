<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Credit Notes – Supplier | 22UniMart</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: #eef2f7; display: flex; min-height: 100vh; }
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
        .btn-report { background: #c0392b; color: #fff; border: none; border-radius: 8px; padding: 12px 16px; font-size: 0.85rem; font-weight: 600; cursor: pointer; width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px; }
        .main { margin-left: 210px; flex: 1; display: flex; flex-direction: column; }
        .topbar { background: #fff; border-bottom: 1px solid #e2e8f0; padding: 0 32px; height: 56px; display: flex; align-items: center; gap: 16px; position: sticky; top: 0; z-index: 10; }
        .topbar-right { margin-left: auto; display: flex; align-items: center; gap: 12px; }
        .avatar { width: 36px; height: 36px; border-radius: 50%; background: #0f2044; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 0.85rem; font-weight: 700; }
        .content { padding: 32px; }
        .page-title { font-size: 1.6rem; font-weight: 800; color: #0f2044; }
        .page-sub   { font-size: 0.85rem; color: #7a8fa8; margin-top: 4px; margin-bottom: 28px; }
        .stats-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 28px; }
        .stat-card { background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 1px 6px rgba(15,32,68,0.07); }
        .stat-label { font-size: 0.65rem; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; color: #9daec5; margin-bottom: 6px; }
        .stat-value { font-size: 1.9rem; font-weight: 800; color: #0f2044; line-height: 1; margin-bottom: 4px; }
        .stat-hint { font-size: 0.72rem; color: #9daec5; }
        .table-card { background: #fff; border-radius: 12px; box-shadow: 0 1px 6px rgba(15,32,68,0.07); overflow: hidden; }
        table { width: 100%; border-collapse: collapse; }
        thead tr { background: #f8fafc; border-bottom: 2px solid #eef2f7; }
        thead th { padding: 13px 16px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: #7a8fa8; text-align: left; }
        tbody tr { border-bottom: 1px solid #f4f6fb; }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: #f8fafc; }
        tbody td { padding: 14px 16px; font-size: 0.86rem; color: #3a4d6a; vertical-align: middle; }
        .cn-number { font-size: 0.78rem; font-weight: 700; color: #4a90d9; font-family: monospace; }
        .badge-cn { display: inline-flex; align-items: center; gap: 5px; padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
        .badge-unused    { background: #e8f4fd; color: #2980b9; }
        .badge-partial   { background: #fef3e2; color: #d4870a; }
        .badge-used      { background: #e8f8f0; color: #1d8348; }
        .amount-cell { font-size: 1rem; font-weight: 700; color: #0f2044; }
        .remaining-cell { font-size: 0.9rem; font-weight: 600; color: #27ae60; }
        .table-footer { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-top: 1px solid #f0f4f8; flex-wrap: wrap; gap: 12px; }
        .footer-info { font-size: 0.8rem; color: #7a8fa8; }
        .page-btn { width: 32px; height: 32px; border-radius: 7px; border: 1px solid #e2e8f0; background: #fff; color: #3a4d6a; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; font-size: 0.85rem; text-decoration: none; }
        .page-btn:hover { background: #0f2044; color: #fff; border-color: #0f2044; }
        .page-btn.disabled { opacity: 0.4; pointer-events: none; }
        .info-box { background: #e8f4fd; border: 1px solid #b8d9f5; border-radius: 10px; padding: 14px 18px; margin-bottom: 20px; font-size: 0.85rem; color: #1a4a7a; display: flex; align-items: flex-start; gap: 10px; }
        .empty-state { padding: 60px 20px; text-align: center; color: #9daec5; }
        .empty-icon { font-size: 2.5rem; margin-bottom: 12px; }
        .empty-text { font-size: 0.95rem; font-weight: 600; color: #7a8fa8; }
        .empty-sub  { font-size: 0.82rem; color: #aab8cc; margin-top: 4px; }
    </style>
</head>
<body>

@php
    $totalCN    = $creditNotes->total();
    $unusedAmt  = \App\Models\CreditNote::where('supplier_id', auth()->user()->supplier?->id)->where('status','Unused')->sum('amount');
    $partialAmt = \App\Models\CreditNote::where('supplier_id', auth()->user()->supplier?->id)->where('status','Partially Used')->sum('amount');
@endphp

<!-- Sidebar -->
@include('supplier.partials.sidebar', ['active' => 'credit_notes', 'hideBrandSub' => true])

<div class="main">
    <div class="topbar">
        <div style="font-size:0.9rem;font-weight:600;color:#0f2044;">Credit Notes</div>
        <div class="topbar-right" style="margin-left: auto; display: flex; align-items: center; gap: 20px;">
            @include('supplier.components.topbar-profile')
        </div>
    </div>

    <div class="content">
        <div class="page-title">Credit Notes</div>
        <div class="page-sub">Credit notes are automatically issued when a return request you approved is processed. They can later be used by the owner to offset supplier invoice payment obligations.</div>

        <div class="info-box">
            ℹ️ Credit notes are issued automatically when you <strong>approve</strong> a return request. The owner can then apply them against invoice payments for return-related credit balances.
        </div>

        <!-- Stat Cards -->
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-label">Total Credit Notes</div>
                <div class="stat-value">{{ $totalCN }}</div>
                <div class="stat-hint">All issued to date</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Available Balance</div>
                <div class="stat-value" style="color:#27ae60;">RM {{ number_format($unusedAmt + $partialAmt, 2) }}</div>
                <div class="stat-hint">Unused + Partially Used</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Partially Used</div>
                <div class="stat-value" style="color:#d4870a;">RM {{ number_format($partialAmt, 2) }}</div>
                <div class="stat-hint">Remaining partial balances</div>
            </div>
        </div>

        <div class="table-card">
            <table>
                <thead>
                    <tr>
                        <th>Credit Note ID</th>
                        <th>Linked Return Request</th>
                        <th>Issue Date</th>
                        <th>Total Amount</th>
                        <th>Remaining Balance</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($creditNotes as $cn)
                    <tr onclick="window.location='{{ route('supplier.credit-notes.show', $cn->id) }}'" style="cursor: pointer;">
                        <td><span class="cn-number">{{ $cn->credit_note_id }}</span></td>
                        <td>
                            @if($cn->returnRequest)
                                <div style="font-weight:600;color:#0f2044;font-size:0.85rem;">#{{ $cn->returnRequest->return_number }}</div>
                                <div style="font-size:0.72rem;color:#9daec5;">
                                    @php
                                        $cnLines = $cn->returnRequest?->lines ?? collect();
                                        $cnFirstItem = optional($cnLines->first()?->item)->name ?? '—';
                                        $cnTotalQty = $cnLines->sum('quantity');
                                        $cnCount = $cnLines->count();
                                    @endphp
                                    {{ $cnFirstItem }}{{ $cnCount > 1 ? ' +'.($cnCount-1).' more' : '' }} · Qty {{ $cnTotalQty }}
                                </div>
                            @else
                                <span style="color:#9daec5;">—</span>
                            @endif
                        </td>
                        <td style="color:#7a8fa8;white-space:nowrap;">{{ \Carbon\Carbon::parse($cn->issue_date)->format('M d, Y') }}</td>
                        <td><span class="amount-cell">RM {{ number_format($cn->amount, 2) }}</span></td>
                        <td>
                            @if($cn->status === 'Used')
                                <span style="color:#9daec5;font-size:0.85rem;">RM 0.00</span>
                            @else
                                <span class="remaining-cell">RM {{ number_format($cn->amount, 2) }}</span>
                            @endif
                        </td>
                        <td>
                            @if($cn->status === 'Unused')
                                <span class="badge-cn badge-unused">● Unused</span>
                            @elseif($cn->status === 'Partially Used')
                                <span class="badge-cn badge-partial">● Partially Used</span>
                            @else
                                <span class="badge-cn badge-used">● Used</span>
                            @endif
                        </td>
                        <td onclick="event.stopPropagation();">
                            <a href="{{ route('supplier.credit-notes.export-single-pdf', $cn->id) }}" class="page-btn" style="width:auto;padding:0 12px;font-size:0.75rem;font-weight:600;display:inline-flex;gap:6px;">
                                ⬇ Download
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6">
                        <div class="empty-state">
                            <div class="empty-icon">📋</div>
                            <div class="empty-text">No credit notes yet</div>
                            <div class="empty-sub">Credit notes are generated when you approve a return request.</div>
                        </div>
                    </td></tr>
                @endforelse
                </tbody>
            </table>

            <div class="table-footer">
                <div class="footer-info">Showing {{ $creditNotes->firstItem() ?? 0 }}–{{ $creditNotes->lastItem() ?? 0 }} of {{ $creditNotes->total() }}</div>
                <div style="display:flex;align-items:center;gap:8px;">
                    @if($creditNotes->onFirstPage())
                        <span class="page-btn disabled">‹</span>
                    @else
                        <a class="page-btn" href="{{ $creditNotes->previousPageUrl() }}">‹</a>
                    @endif
                    <span style="font-size:0.8rem;color:#7a8fa8;">Page {{ $creditNotes->currentPage() }} / {{ $creditNotes->lastPage() }}</span>
                    @if($creditNotes->hasMorePages())
                        <a class="page-btn" href="{{ $creditNotes->nextPageUrl() }}">›</a>
                    @else
                        <span class="page-btn disabled">›</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
