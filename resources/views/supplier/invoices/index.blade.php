<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoices – Supplier Portal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: #eef2f7; display: flex; min-height: 100vh; color: #1a2744; }

        /* Sidebar styles are inherited from partial but we need basic reset */
        .sidebar { width: 210px; flex-shrink: 0; background: #0f2044; color: #fff; display: flex; flex-direction: column; padding: 0 0 24px 0; position: fixed; top: 0; left: 0; height: 100vh; }
        .sidebar-brand { padding: 20px 20px 4px; }
        .brand-name { font-size: 0.9rem; font-weight: 800; }
        .brand-sub { font-size: 0.68rem; color: #8ca0c0; }
        .sidebar-nav { flex: 1; margin-top: 20px; }
        .nav-item { display: flex; align-items: center; gap: 12px; padding: 11px 20px; font-size: 0.88rem; font-weight: 500; color: #8ca0c0; text-decoration: none; border-left: 3px solid transparent; }
        .nav-item:hover { color: #fff; background: rgba(255,255,255,0.06); }
        .nav-item.active { color: #fff; background: rgba(255,255,255,0.1); border-left-color: #4a90d9; }
        .nav-icon { width: 18px; text-align: center; }
        .sidebar-bottom { padding: 0 20px; display: flex; flex-direction: column; gap: 8px; }
        .btn-report { background: #c0392b; color: #fff; border: none; border-radius: 8px; padding: 12px 16px; font-size: 0.85rem; font-weight: 600; cursor: pointer; width: 100%; }
        
        .main { margin-left: 210px; flex: 1; display: flex; flex-direction: column; }
        .topbar { background: #fff; border-bottom: 1px solid #e2e8f0; padding: 0 32px; height: 56px; display: flex; align-items: center; justify-content: flex-end; position: sticky; top: 0; z-index: 10; }
        .avatar { width: 36px; height: 36px; border-radius: 50%; background: #0f2044; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 0.85rem; font-weight: 700; }
        .topbar-profile { display: flex; align-items: center; gap: 12px; }

        .content { padding: 32px; }
        .page-header { margin-bottom: 28px; }
        .page-title { font-size: 1.6rem; font-weight: 800; color: #0f2044; }
        .page-sub { font-size: 0.85rem; color: #7a8fa8; margin-top: 4px; }

        .stats-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 28px; }
        .stat-card { background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 1px 6px rgba(15,32,68,0.07); }
        .stat-label { font-size: 0.65rem; font-weight: 700; text-transform: uppercase; color: #9daec5; margin-bottom: 6px; }
        .stat-value { font-size: 2rem; font-weight: 800; color: #0f2044; line-height: 1; }

        .card { background: #fff; border-radius: 12px; box-shadow: 0 1px 6px rgba(15,32,68,0.07); overflow: hidden; }
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        thead tr { background: #f8fafc; border-bottom: 2px solid #eef2f7; }
        thead th { padding: 13px 16px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: #7a8fa8; text-align: left; }
        tbody tr { border-bottom: 1px solid #f4f6fb; }
        tbody tr:hover { background: #f8fafc; }
        tbody td { padding: 14px 16px; font-size: 0.86rem; color: #3a4d6a; vertical-align: middle; }

        .badge { display: inline-flex; align-items: center; padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; }
        .badge-unpaid { background: #fef3e2; color: #d4870a; }
        .badge-settled { background: #e8f4fd; color: #2980b9; }
        .badge-paid { background: #e8f8f0; color: #1d8348; }
        
        .action-link { font-size: 0.8rem; color: #4a90d9; text-decoration: none; font-weight: 600; margin-right: 12px; }
        .action-link:hover { text-decoration: underline; }
        
        .flash { padding: 13px 20px; border-radius: 8px; font-size: 0.88rem; margin-bottom: 20px; font-weight: 500; }
        .flash-success { background: #e8f8f0; color: #1d8348; border: 1px solid #a9dfbf; }
        
        .btn-mark-paid { background: #27ae60; color: #fff; border: none; border-radius: 6px; padding: 6px 12px; font-size: 0.75rem; font-weight: 700; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn-mark-paid:hover { background: #219653; }
        
        .empty-state { padding: 40px 20px; text-align: center; color: #9daec5; }
    </style>
</head>
<body>

@include('supplier.partials.sidebar', ['active' => 'invoices'])

<div class="main">
    <div class="topbar">
        <div class="topbar-right" style="margin-left: auto; display: flex; align-items: center; gap: 20px;">
            @include('supplier.components.topbar-profile')
        </div>
    </div>

    <div class="content">
        <div class="page-header">
            <div class="page-title">Invoices</div>
            <div class="page-sub">View your invoices and mark them as paid once payment is received.</div>
        </div>

        @if(session('success'))
            <div class="flash flash-success">{{ session('success') }}</div>
        @endif

        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-label">Total Invoices</div>
                <div class="stat-value">{{ $invoices->count() }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Unpaid</div>
                <div class="stat-value" style="color: #d4870a;">{{ $invoices->filter(fn($i) => $i->computeStatus() === 'Unpaid')->count() }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Settled (Awaiting Pmt)</div>
                <div class="stat-value" style="color: #2980b9;">{{ $invoices->filter(fn($i) => $i->computeStatus() === 'Settled')->count() }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Paid</div>
                <div class="stat-value" style="color: #1d8348;">{{ $invoices->filter(fn($i) => $i->computeStatus() === 'Paid')->count() }}</div>
            </div>
        </div>

        <div class="card">
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Invoice Number</th>
                            <th>PO Number</th>
                            <th>Invoice Date</th>
                            <th>Due Date</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $invoice)
                            <tr>
                                <td style="font-weight:600;">{{ $invoice->invoice_number }}</td>
                                <td>{{ optional($invoice->purchaseOrder)->po_number ?? 'N/A' }}</td>
                                <td>{{ $invoice->invoice_date->format('M d, Y') }}</td>
                                <td>{{ $invoice->payment_due_date ? $invoice->payment_due_date->format('M d, Y') : 'N/A' }}</td>
                                <td style="font-weight:600;">RM {{ number_format($invoice->total_amount, 2) }}</td>
                                <td>
                                    @php $status = $invoice->computeStatus(); @endphp
                                    <span class="badge badge-{{ strtolower($status) }}">{{ $status }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('supplier.invoices.show', $invoice) }}" class="action-link">View</a>
                                    @if($status === 'Settled')
                                        <form action="{{ route('supplier.invoices.markPaid', $invoice) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to mark this invoice as Paid? This action cannot be undone.');">
                                            @csrf
                                            <button type="submit" class="btn-mark-paid">Mark as Paid</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">No invoices found.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</body>
</html>
