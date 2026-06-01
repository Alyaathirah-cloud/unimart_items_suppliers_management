<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoices – Supplier Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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
        .topbar-icons{margin-left:auto;display:flex;align-items:center;gap:20px}
        .icon-btn{background:none;border:none;cursor:pointer;color:#5a6a85;font-size:1.1rem;position:relative;padding:4px}
        .topbar-profile{display:flex;align-items:center;gap:8px}
        .profile-name{font-size:.85rem;font-weight:600;color:#1a2744}
        .profile-sub{font-size:.72rem;color:#9daec5;cursor:pointer;background:none;border:none;font-family:'Inter',sans-serif;padding:0}
        .avatar{width:36px;height:36px;border-radius:50%;background:#0f2044;display:flex;align-items:center;justify-content:center;color:#fff;font-size:.85rem;font-weight:700}
        .content{padding:32px}
        .page-header{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:28px}
        .page-title{font-size:1.6rem;font-weight:800;color:#0f2044}
        .page-sub{font-size:.85rem;color:#7a8fa8;margin-top:4px}
        .section{background:#fff;border-radius:12px;box-shadow:0 1px 6px rgba(15,32,68,.07);overflow:hidden}
        .section-header{display:flex;align-items:center;justify-content:space-between;padding:20px 24px 16px;border-bottom:1px solid #f0f4f8}
        .section-title{font-size:1rem;font-weight:700;color:#0f2044}
        .filter-bar{display:flex;align-items:center;gap:12px;padding:14px 24px;border-bottom:1px solid #f0f4f8}
        .filter-select{border:1px solid #d1dce8;border-radius:8px;padding:8px 14px;font-size:.82rem;font-family:'Inter',sans-serif;color:#3a4d6a;background:#fff;outline:none;cursor:pointer}
        .table-wrap{overflow-x:auto}
        table{width:100%;border-collapse:collapse}
        thead th{padding:10px 20px;font-size:.65rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#9daec5;text-align:left;background:#f8fafc;border-bottom:1px solid #edf2f7;white-space:nowrap}
        tbody td{padding:14px 20px;font-size:.88rem;color:#1a2744;border-bottom:1px solid #f0f4f8;vertical-align:middle}
        tbody tr:last-child td{border-bottom:none}
        tbody tr:hover td{background:#fafbfd}
        .badge{display:inline-flex;align-items:center;padding:5px 14px;border-radius:20px;font-size:.72rem;font-weight:700;letter-spacing:.5px;text-transform:capitalize;white-space:nowrap}
        .badge-pending{background:#fef3e2;color:#d4870a;border:1px solid #fde0a0}
        .badge-approved{background:#e8f8f0;color:#1d8348;border:1px solid #a9dfbf}
        .badge-rejected{background:#fdedec;color:#c0392b;border:1px solid #f5b7b1}
        .btn{display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border-radius:7px;font-size:.78rem;font-weight:600;cursor:pointer;border:1px solid transparent;transition:all .15s;font-family:'Inter',sans-serif;text-decoration:none;white-space:nowrap}
        .btn-outline{background:#fff;color:#3a4d6a;border-color:#d1dce8}
        .btn-outline:hover{background:#f4f6fb}
        .pagination-bar{display:flex;align-items:center;justify-content:space-between;padding:14px 24px;border-top:1px solid #f0f4f8;font-size:.82rem;color:#7a8fa8}
        .empty-state{padding:48px;text-align:center;color:#9daec5;font-size:.9rem}
    </style>
</head>
<body>

<!-- Sidebar -->
@include('supplier.partials.sidebar', ['active' => 'invoices'])

<!-- Main -->
<div class="main">
    <div class="topbar">
        <div class="topbar-icons">
            <button class="icon-btn">🔔</button>
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
                <div class="page-title">My Invoices</div>
                <div class="page-sub">View and download your billing invoices.</div>
            </div>
        </div>

        <div class="section">
            <div class="section-header">
                <div class="section-title">Invoices</div>
            </div>
            <div class="filter-bar">
                <form action="{{ route('supplier.invoices.index') }}" method="GET" style="display:flex;gap:12px;">
                    <select name="status" class="filter-select" onchange="this.form.submit()">
                        <option value="">All Statuses</option>
                        <option value="Unpaid" {{ request('status') === 'Unpaid' ? 'selected' : '' }}>Unpaid</option>
                        <option value="Paid" {{ request('status') === 'Paid' ? 'selected' : '' }}>Paid</option>
                        <option value="Overdue" {{ request('status') === 'Overdue' ? 'selected' : '' }}>Overdue</option>
                        <option value="Partially Credited" {{ request('status') === 'Partially Credited' ? 'selected' : '' }}>Partially Credited</option>
                    </select>
                </form>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Invoice ID</th>
                            <th>Linked PO</th>
                            <th>Date Issued</th>
                            <th>Due Date</th>
                            <th>Amount (RM)</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($invoices as $invoice)
                        @php
                            $s = $invoice->status;
                            $cls = match(strtolower($s)) {
                                'unpaid' => 'badge-pending',
                                'paid' => 'badge-approved',
                                'overdue' => 'badge-rejected',
                                default => 'badge-pending',
                            };
                        @endphp
                        <tr>
                            <td><div style="font-weight:700;color:#0f2044;">{{ $invoice->invoice_number }}</div></td>
                            <td>{{ optional($invoice->purchaseOrder)->po_number ?? 'N/A' }}</td>
                            <td>{{ \Carbon\Carbon::parse($invoice->issue_date)->format('M d, Y') }}</td>
                            <td>
                                @if($invoice->due_date)
                                    <span style="{{ \Carbon\Carbon::parse($invoice->due_date)->isPast() && $s === 'Unpaid' ? 'color:#c0392b;font-weight:600' : '' }}">
                                        {{ \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') }}
                                    </span>
                                @else
                                    —
                                @endif
                            </td>
                            <td><div style="font-weight:600">{{ number_format($invoice->total_amount, 2) }}</div></td>
                            <td><span class="badge {{ $cls }}">{{ ucfirst($s) }}</span></td>
                            <td>
                                <a href="{{ route('supplier.invoices.export-pdf', $invoice) }}" class="btn btn-outline" target="_blank">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                    Download PDF
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="empty-state">No invoices found.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="pagination-bar">
                <div>Showing {{ $invoices->count() }} result{{ $invoices->count() !== 1 ? 's' : '' }}</div>
                <div>{{ $invoices->links('pagination::simple-default') }}</div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
