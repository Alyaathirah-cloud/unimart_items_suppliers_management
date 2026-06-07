<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Return Request {{ $returnRequest->return_number }} – 22UniMart</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Inter, sans-serif; background: #eef2f7; display: flex; min-height: 100vh; color: #1a2744; }
        .sidebar { width: 210px; flex-shrink: 0; background: #0f2044; color: #fff; display: flex; flex-direction: column; padding: 0 0 24px 0; position: fixed; top: 0; left: 0; height: 100vh; }
        .sidebar-brand { padding: 20px 20px 4px; display: flex; align-items: center; gap: 12px; }
        .brand-square { width: 32px; height: 32px; background: #fff; color: #0f2044; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 0.9rem; font-weight: 800; }
        .brand-name { font-size: 0.9rem; font-weight: 800; }
        .brand-sub { font-size: 0.68rem; color: #8ca0c0; }
        .sidebar-nav { flex: 1; margin-top: 20px; overflow-y: auto; }
        .nav-item { display: flex; align-items: center; gap: 12px; padding: 11px 20px; font-size: 0.88rem; font-weight: 500; color: #8ca0c0; text-decoration: none; transition: all 0.15s; border-left: 3px solid transparent; }
        .nav-item:hover { color: #fff; background: rgba(255,255,255,0.06); }
        .nav-item.active { color: #fff; background: rgba(255,255,255,0.1); border-left-color: #4a90d9; }
        .sidebar-bottom { padding: 0 20px; display: flex; flex-direction: column; gap: 8px; }
        .btn-report { background: #c0392b; color: #fff; border: none; border-radius: 8px; padding: 12px 16px; font-size: 0.85rem; font-weight: 600; cursor: pointer; width: 100%; }
        .btn-report:hover { background: #a93226; }
        .main { margin-left: 210px; flex: 1; display: flex; flex-direction: column; }
        .topbar { background: #fff; padding: 0 32px; height: 56px; display: flex; align-items: center; gap: 16px; position: sticky; top: 0; z-index: 10; border-bottom: 1px solid #e2e8f0; }
        .breadcrumbs { font-size: 0.75rem; font-weight: 700; color: #7a8fa8; letter-spacing: 1px; text-transform: uppercase; }
        .breadcrumbs a { color: inherit; text-decoration: none; }
        .breadcrumbs a:hover { color: #0f2044; }
        .topbar-right { margin-left: auto; display: flex; align-items: center; gap: 20px; }
        .avatar { width: 32px; height: 32px; border-radius: 50%; background: #0f2044; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 0.75rem; font-weight: 700; }
        .content { padding: 40px; max-width: 960px; width: 100%; }
        .page-title { font-size: 2rem; font-weight: 800; color: #0f2044; margin-bottom: 8px; }
        .page-sub { font-size: 0.95rem; color: #5a6a85; line-height: 1.6; margin-bottom: 24px; max-width: 760px; }
        .card { background: #fff; border-radius: 16px; box-shadow: 0 1px 24px rgba(15,32,68,0.08); margin-bottom: 24px; overflow: hidden; }
        .card-body { padding: 28px; }
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Return Request {{ $returnRequest->return_number }} – 22UniMart</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Inter, sans-serif; background: #eef2f7; display: flex; min-height: 100vh; color: #1a2744; }
        .sidebar { width: 210px; flex-shrink: 0; background: #0f2044; color: #fff; display: flex; flex-direction: column; padding: 0 0 24px 0; position: fixed; top: 0; left: 0; height: 100vh; }
        .sidebar-brand { padding: 20px 20px 4px; display: flex; align-items: center; gap: 12px; }
        .brand-square { width: 32px; height: 32px; background: #fff; color: #0f2044; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 0.9rem; font-weight: 800; }
        .brand-name { font-size: 0.9rem; font-weight: 800; }
        .brand-sub { font-size: 0.68rem; color: #8ca0c0; }
        .sidebar-nav { flex: 1; margin-top: 20px; overflow-y: auto; }
        .nav-item { display: flex; align-items: center; gap: 12px; padding: 11px 20px; font-size: 0.88rem; font-weight: 500; color: #8ca0c0; text-decoration: none; transition: all 0.15s; border-left: 3px solid transparent; }
        .nav-item:hover { color: #fff; background: rgba(255,255,255,0.06); }
        .nav-item.active { color: #fff; background: rgba(255,255,255,0.1); border-left-color: #4a90d9; }
        .sidebar-bottom { padding: 0 20px; display: flex; flex-direction: column; gap: 8px; }
        .btn-report { background: #c0392b; color: #fff; border: none; border-radius: 8px; padding: 12px 16px; font-size: 0.85rem; font-weight: 600; cursor: pointer; width: 100%; }
        .btn-report:hover { background: #a93226; }
        .main { margin-left: 210px; flex: 1; display: flex; flex-direction: column; }
        .topbar { background: #fff; padding: 0 32px; height: 56px; display: flex; align-items: center; gap: 16px; position: sticky; top: 0; z-index: 10; border-bottom: 1px solid #e2e8f0; }
        .breadcrumbs { font-size: 0.75rem; font-weight: 700; color: #7a8fa8; letter-spacing: 1px; text-transform: uppercase; }
        .breadcrumbs a { color: inherit; text-decoration: none; }
        .breadcrumbs a:hover { color: #0f2044; }
        .topbar-right { margin-left: auto; display: flex; align-items: center; gap: 20px; }
        .avatar { width: 32px; height: 32px; border-radius: 50%; background: #0f2044; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 0.75rem; font-weight: 700; }
        .content { padding: 40px; max-width: 960px; width: 100%; }
        .page-title { font-size: 2rem; font-weight: 800; color: #0f2044; margin-bottom: 8px; }
        .page-sub { font-size: 0.95rem; color: #5a6a85; line-height: 1.6; margin-bottom: 24px; max-width: 760px; }
        .card { background: #fff; border-radius: 16px; box-shadow: 0 1px 24px rgba(15,32,68,0.08); margin-bottom: 24px; overflow: hidden; }
        .card-body { padding: 28px; }
        .card-title { font-size: 1.05rem; font-weight: 700; margin-bottom: 16px; }
        .field-row { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px; margin-bottom: 18px; }
        .field { display: flex; flex-direction: column; gap: 6px; }
        .field-label { font-size: 0.78rem; font-weight: 700; color: #7a8fa8; text-transform: uppercase; letter-spacing: 0.6px; }
        .field-value { font-size: 0.95rem; color: #1a2744; }
        .badge { display: inline-flex; align-items: center; padding: 8px 14px; border-radius: 999px; font-size: 0.82rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
        .badge-draft { background: #f4f6fb; color: #5a6a85; }
        .badge-pending { background: #e8f4fd; color: #2980b9; }
        .badge-approved { background: #e8f8f0; color: #1d8348; }
        .badge-rejected { background: #fdedec; color: #c0392b; }
        .btn-secondary { display: inline-flex; align-items: center; justify-content: center; background: #f4f6fb; color: #1a2744; border: 1px solid #d1dce8; border-radius: 10px; padding: 12px 18px; text-decoration: none; font-weight: 700; }
        .btn-secondary:hover { background: #e8eff6; }
    </style>
</head>
<body>
<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="brand-square">22</div>
        <div>
            <div class="brand-name">22UNIMART</div>
            <div class="brand-sub">Inventory Control</div>
        </div>
    </div>
    <nav class="sidebar-nav">
        <a href="{{ route('owner.dashboard') }}" class="nav-item {{ request()->routeIs('owner.dashboard') ? 'active' : '' }}">⊞ Dashboard</a>
        <a href="{{ route('owner.items.index') }}" class="nav-item {{ request()->routeIs('owner.items.*') ? 'active' : '' }}">📦 Inventory</a>
        <a href="{{ route('owner.suppliers.index') }}" class="nav-item {{ request()->routeIs('owner.suppliers.*') ? 'active' : '' }}">🏢 Suppliers</a>
        <a href="{{ route('owner.purchase-orders.index') }}" class="nav-item {{ request()->routeIs('owner.purchase-orders.*') ? 'active' : '' }}">🛒 Purchase Orders</a>
        <a href="{{ route('owner.return-requests.index') }}" class="nav-item {{ request()->routeIs('owner.return-requests.*') ? 'active' : '' }}">↩ Return Requests</a>
        <a href="{{ route('owner.credit-notes.index') }}" class="nav-item {{ request()->routeIs('owner.credit-notes.*') ? 'active' : '' }}">📋 Credit Notes</a>
        <a href="{{ route('owner.notifications.index') }}" class="nav-item {{ request()->routeIs('owner.notifications.*') ? 'active' : '' }}">🔔 Notifications</a>
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
        <div class="breadcrumbs"><a href="{{ route('owner.return-requests.index') }}">Return Requests</a> › <span style="color:#0f2044;">{{ $returnRequest->return_number }}</span></div>
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
        <div class="page-title">Return Request {{ $returnRequest->return_number }}</div>
        <div class="page-sub">Review the status, item details, and credit note history for this return request.</div>

        <div class="card">
            <div class="card-body">
                <div class="field-row">
                    <div class="field">
                        <div class="field-label">Status</div>
                        <div class="field-value">
                            <span class="badge badge-{{ strtolower($returnRequest->status) === 'draft' ? 'draft' : (strtolower($returnRequest->status) === 'pending' ? 'pending' : (strtolower($returnRequest->status) === 'approved' ? 'approved' : 'rejected')) }}">{{ $returnRequest->status }}</span>
                        </div>
                    </div>
                    <div class="field">
                        <div class="field-label">Requested On</div>
                        <div class="field-value">{{ optional($returnRequest->request_date)->format('M d, Y') ?? $returnRequest->created_at->format('M d, Y') }}</div>
                    </div>
                    <div class="field">
                        <div class="field-label">Total Lines</div>
                        <div class="field-value">{{ $returnRequest->lines->count() }} item(s)</div>
                    </div>
                    <div class="field">
                        <div class="field-label">Total Qty Requested</div>
                        <div class="field-value">{{ number_format($returnRequest->lines->sum('quantity')) }}</div>
                    </div>
                </div>

                <div class="field-row">
                    <div class="field">
                        <div class="field-label">Supplier</div>
                        <div class="field-value">{{ optional($returnRequest->supplier)->name ?? 'N/A' }}</div>
                    </div>
                    <div class="field">
                        <div class="field-label">Invoice Reference</div>
                        <div class="field-value">
                            @if($returnRequest->invoice)
                                <a href="{{ route('owner.invoices.show', $returnRequest->invoice) }}" style="color:#4a90d9;text-decoration:none;font-weight:700;">{{ $returnRequest->invoice->invoice_number }}</a>
                            @else
                                {{ $returnRequest->invoice_number ?? '—' }}
                            @endif
                        </div>
                    </div>
                </div>

                <div class="field-row">
                    <div class="field" style="grid-column: span 2;">
                        <div class="field-label">Notes</div>
                        <div class="field-value">{{ $returnRequest->notes ?? 'No additional notes provided.' }}</div>
                    </div>
                    <div class="field">
                        <div class="field-label">Purchase Order</div>
                        <div class="field-value">
                            @if($returnRequest->purchaseOrder)
                                <a href="{{ route('owner.purchase-orders.show', $returnRequest->purchaseOrder) }}" style="color:#4a90d9;text-decoration:none;font-weight:700;">#{{ $returnRequest->purchaseOrder->po_number }}</a>
                            @else
                                —
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h3 class="card-title">Returned Items</h3>
                @if($returnRequest->lines->isEmpty())
                    <p class="field-value">This return request has no itemized lines recorded.</p>
                @else
                    <div style="overflow-x:auto;">
                        <table style="width:100%;border-collapse:collapse;">
                            <thead>
                                <tr style="background:#f8fafc;">
                                    <th style="padding:12px 14px;text-align:left;font-size:.82rem;color:#7a8fa8;">Item</th>
                                    <th style="padding:12px 14px;text-align:right;font-size:.82rem;color:#7a8fa8;">Reason</th>
                                    <th style="padding:12px 14px;text-align:right;font-size:.82rem;color:#7a8fa8;">Requested Qty</th>
                                    <th style="padding:12px 14px;text-align:right;font-size:.82rem;color:#7a8fa8;">Unit Price</th>
                                    <th style="padding:12px 14px;text-align:right;font-size:.82rem;color:#7a8fa8;">Subtotal</th>
                                    @if(!in_array($returnRequest->status, ['Pending', 'Draft']))
                                    <th style="padding:12px 14px;text-align:right;font-size:.82rem;color:#7a8fa8;">Approved Qty</th>
                                    <th style="padding:12px 14px;text-align:right;font-size:.82rem;color:#7a8fa8;">Approved Total</th>
                                    <th style="padding:12px 14px;text-align:right;font-size:.82rem;color:#7a8fa8;">Gross Loss</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($returnRequest->lines as $line)
                                    <tr style="border-bottom:1px solid #f4f6fb;">
                                        <td style="padding:12px 14px;">{{ optional($line->item)->name ?? '–' }}</td>
                                        <td style="padding:12px 14px;text-align:right;text-transform:capitalize;">{{ $line->reason }}</td>
                                        <td style="padding:12px 14px;text-align:right;">{{ number_format($line->quantity) }}</td>
                                        <td style="padding:12px 14px;text-align:right;">RM {{ number_format($line->unit_price, 2) }}</td>
                                        <td style="padding:12px 14px;text-align:right;">RM {{ number_format($line->subtotal, 2) }}</td>
                                        @if(!in_array($returnRequest->status, ['Pending', 'Draft']))
                                        <td style="padding:12px 14px;text-align:right;font-weight:600;color:#27ae60;">{{ number_format($line->approved_qty) }}</td>
                                        <td style="padding:12px 14px;text-align:right;font-weight:600;color:#27ae60;">RM {{ number_format($line->approved_subtotal, 2) }}</td>
                                        <td style="padding:12px 14px;text-align:right;font-weight:600;color:#c0392b;">
                                            @if($line->getGrossLoss() > 0)
                                                RM {{ number_format($line->getGrossLoss(), 2) }}
                                            @else
                                                —
                                            @endif
                                        </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        
                        @if(!in_array($returnRequest->status, ['Pending', 'Draft']))
                        <div style="margin-top:20px;padding:16px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;display:flex;justify-content:flex-end;gap:40px;">
                            <div style="text-align:right;">
                                <div style="font-size:0.8rem;color:#7a8fa8;text-transform:uppercase;font-weight:700;margin-bottom:4px;">Requested Return Value</div>
                                <div style="font-size:1.2rem;font-weight:800;color:#0f2044;">RM {{ number_format($returnRequest->getRequestedTotal(), 2) }}</div>
                            </div>
                            <div style="text-align:right;">
                                <div style="font-size:0.8rem;color:#7a8fa8;text-transform:uppercase;font-weight:700;margin-bottom:4px;">Total Gross Loss</div>
                                <div style="font-size:1.2rem;font-weight:800;color:#c0392b;">RM {{ number_format($returnRequest->getGrossLoss(), 2) }}</div>
                            </div>
                            <div style="text-align:right;">
                                <div style="font-size:0.8rem;color:#7a8fa8;text-transform:uppercase;font-weight:700;margin-bottom:4px;">Approved Credit Amount</div>
                                <div style="font-size:1.2rem;font-weight:800;color:#27ae60;">RM {{ number_format($returnRequest->getApprovedTotal(), 2) }}</div>
                            </div>
                        </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h3 class="card-title">Credit Note</h3>
                @if($returnRequest->creditNote)
                    <div class="field-row">
                        <div class="field">
                            <div class="field-label">Credit Note ID</div>
                            <div class="field-value">{{ $returnRequest->creditNote->credit_note_id }}</div>
                        </div>
                        <div class="field">
                            <div class="field-label">Amount</div>
                            <div class="field-value">RM {{ number_format($returnRequest->creditNote->amount, 2) }}</div>
                        </div>
                        <div class="field">
                            <div class="field-label">Balance</div>
                            <div class="field-value">RM {{ number_format($returnRequest->creditNote->remaining_balance, 2) }}</div>
                        </div>
                        <div class="field">
                            <div class="field-label">Status</div>
                            <div class="field-value">{{ $returnRequest->creditNote->status }}</div>
                        </div>
                    </div>
                @else
                    <p class="field-value">No credit note has been issued yet for this return request.</p>
                @endif
            </div>
        </div>

        @if($returnRequest->status === 'Draft')
        <div style="display:flex; gap: 12px; margin-top: 24px;">
            <form action="{{ route('owner.return-requests.submit', $returnRequest) }}" method="POST">
                @csrf
                @method('PATCH')
                <button type="submit" style="background:#2980b9;color:#fff;border:none;border-radius:10px;padding:12px 24px;font-weight:700;cursor:pointer;font-size:0.9rem;">
                    Submit Return Request
                </button>
            </form>
            <form action="{{ route('owner.return-requests.destroy', $returnRequest) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this draft?');">
                @csrf
                @method('DELETE')
                <button type="submit" style="background:#fff;color:#c0392b;border:1px solid #f5b7b1;border-radius:10px;padding:12px 24px;font-weight:700;cursor:pointer;font-size:0.9rem;">
                    Delete Draft
                </button>
            </form>
        </div>
        @endif

        <div style="margin-top: 24px;">
            <a href="{{ route('owner.return-requests.index') }}" class="btn-secondary">← Back to Return Requests</a>
        </div>
    </div>
</div>
</body>
</html>
