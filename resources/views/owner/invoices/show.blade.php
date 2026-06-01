<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice {{ $invoice->invoice_number }} – 22UniMart</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: #eef2f7; display: flex; min-height: 100vh; }

        /* Sidebar */
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
        .btn-report { background: #1e3a6e; color: #fff; border: none; border-radius: 8px; padding: 12px 16px; font-size: 0.85rem; font-weight: 600; cursor: pointer; width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px; transition: background 0.15s; text-decoration: none; }
        .btn-report:hover { background: #2a4f8f; }
        .sidebar-link { display: flex; align-items: center; gap: 10px; color: #8ca0c0; font-size: 0.82rem; text-decoration: none; padding: 6px 0; }

        /* Main */
        .main { margin-left: 210px; flex: 1; display: flex; flex-direction: column; }

        /* Topbar */
        .topbar { background: #fff; border-bottom: 1px solid #e2e8f0; padding: 0 32px; height: 56px; display: flex; align-items: center; gap: 16px; position: sticky; top: 0; z-index: 10; }
        .search-box { display: flex; align-items: center; gap: 8px; background: #f4f6fb; border-radius: 8px; padding: 8px 14px; flex: 1; max-width: 360px; }
        .search-box input { border: none; background: transparent; outline: none; font-family: 'Inter', sans-serif; font-size: 0.88rem; color: #3a4d6a; width: 100%; }
        .search-box input::placeholder { color: #9daec5; }
        .topbar-right { margin-left: auto; display: flex; align-items: center; gap: 20px; }
        .avatar { width: 36px; height: 36px; border-radius: 50%; background: #0f2044; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 0.85rem; font-weight: 700; }
        .breadcrumb { font-size: 0.75rem; font-weight: 700; color: #9daec5; letter-spacing: 0.8px; text-transform: uppercase; display: flex; align-items: center; gap: 6px; }
        .breadcrumb a { color: #9daec5; text-decoration: none; transition: color 0.15s; }
        .breadcrumb a:hover { color: #0f2044; }

        /* Content */
        .content { padding: 32px; max-width: 1200px; margin: 0 auto; width: 100%; }
        
        .grid-layout { display: grid; grid-template-columns: 1fr 340px; gap: 24px; align-items: start; }

        /* Cards */
        .card { background: #fff; border-radius: 12px; box-shadow: 0 1px 6px rgba(15,32,68,0.07); overflow: hidden; margin-bottom: 24px; }
        .card-header { padding: 20px 24px 16px; border-bottom: 1px solid #f0f4f8; display: flex; align-items: center; justify-content: space-between; }
        .card-title { font-size: 0.95rem; font-weight: 700; color: #0f2044; display: flex; align-items: center; gap: 8px; }
        .card-body { padding: 24px; }

        /* Invoice Document Header */
        .invoice-doc-header { text-align: center; margin-bottom: 32px; padding-bottom: 24px; border-bottom: 2px dashed #e2e8f0; }
        .invoice-doc-title { font-size: 1.8rem; font-weight: 800; color: #0f2044; margin-bottom: 4px; }
        .invoice-doc-sub { font-size: 0.9rem; color: #7a8fa8; }

        /* Two columns Info */
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 32px; }
        .info-block h5 { font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: #9daec5; margin-bottom: 8px; }
        .info-block p { font-size: 0.88rem; color: #3a4d6a; line-height: 1.6; margin-bottom: 2px; }
        .info-block strong { color: #0f2044; font-weight: 700; font-size: 0.95rem; }

        .invoice-meta { background: #f8fafc; border: 1px solid #eef2f7; border-radius: 8px; padding: 16px; }
        .meta-row { display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 0.88rem; }
        .meta-row:last-child { margin-bottom: 0; }
        .meta-label { color: #7a8fa8; }
        .meta-val { color: #0f2044; font-weight: 600; text-align: right; }

        /* Table */
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        .items-table th { padding: 12px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: #7a8fa8; border-bottom: 2px solid #eef2f7; text-align: left; }
        .items-table td { padding: 16px 12px; font-size: 0.88rem; color: #3a4d6a; border-bottom: 1px solid #f4f6fb; }
        .items-table tr:last-child td { border-bottom: none; }
        
        .totals-block { width: 300px; margin-left: auto; }
        .total-row { display: flex; justify-content: space-between; padding: 8px 0; font-size: 0.88rem; color: #3a4d6a; }
        .total-row.grand-total { border-top: 2px solid #eef2f7; margin-top: 8px; padding-top: 12px; font-size: 1.1rem; font-weight: 800; color: #0f2044; }
        .credit-row { color: #e74c3c; font-weight: 600; }

        /* Sidebar Action Buttons */
        .btn-primary-action { background: #0f2044; color: #fff; border: none; border-radius: 8px; padding: 12px 16px; font-size: 0.88rem; font-weight: 600; cursor: pointer; text-align: center; text-decoration: none; display: block; transition: background 0.15s; width: 100%; margin-bottom: 12px; }
        .btn-primary-action:hover { background: #1e3a6e; }
        .btn-secondary-action { background: #fff; color: #3a4d6a; border: 1px solid #d1dce8; border-radius: 8px; padding: 12px 16px; font-size: 0.88rem; font-weight: 600; cursor: pointer; text-align: center; text-decoration: none; display: block; transition: all 0.15s; width: 100%; margin-bottom: 12px; }
        .btn-secondary-action:hover { background: #f4f6fb; color: #0f2044; border-color: #9daec5; }

        /* Badges */
        .badge-status { display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; }
        .badge-unpaid { background: #fef3e2; color: #d4870a; }
        .badge-paid { background: #e8f8f0; color: #1d8348; }
        .badge-dot { width: 7px; height: 7px; border-radius: 50%; background: currentColor; display: inline-block; }

        /* Related Tables */
        .related-table { width: 100%; border-collapse: collapse; }
        .related-table th { padding: 10px 16px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: #7a8fa8; background: #f8fafc; text-align: left; }
        .related-table td { padding: 12px 16px; font-size: 0.82rem; color: #3a4d6a; border-bottom: 1px solid #f4f6fb; }
        .related-table tr:last-child td { border-bottom: none; }
        .empty-rel { padding: 24px; text-align: center; color: #9daec5; font-size: 0.85rem; font-style: italic; }
        .related-link { color: #4a90d9; font-weight: 600; text-decoration: none; font-family: monospace; }
        .related-link:hover { text-decoration: underline; }

        @media print {
            .sidebar, .topbar, .col-actions, .card-header { display: none !important; }
            .main { margin-left: 0 !important; }
            .content { padding: 0 !important; max-width: 100% !important; }
            .grid-layout { display: block !important; }
            .card { box-shadow: none !important; margin-bottom: 0 !important; border-radius: 0 !important; }
            body { background: #fff !important; }
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
        <a href="{{ route('owner.dashboard') }}" class="nav-item"><span class="nav-icon">⊞</span> Dashboard</a>
        <a href="{{ route('owner.items.index') }}" class="nav-item"><span class="nav-icon">📦</span> Inventory</a>
        <a href="{{ route('owner.suppliers.index') }}" class="nav-item"><span class="nav-icon">🏢</span> Suppliers</a>
        <a href="{{ route('owner.purchase-orders.index') }}" class="nav-item"><span class="nav-icon">🛒</span> Purchase Orders</a>
        <a href="{{ route('owner.return-requests.index') }}" class="nav-item"><span class="nav-icon">↩</span> Return Requests</a>
        <a href="{{ route('owner.invoices.index') }}" class="nav-item active"><span class="nav-icon">📄</span> Invoices</a>
        <a href="{{ route('owner.credit-notes.index') }}" class="nav-item"><span class="nav-icon">📋</span> Credit Notes</a>
        <a href="{{ route('owner.notifications.index') }}" class="nav-item"><span class="nav-icon">🔔</span> Notifications</a>
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
        <div class="breadcrumb">
            <a href="{{ route('owner.invoices.index') }}">Invoices</a>
            <span style="color:#e2e8f0">/</span>
            <span style="color:#0f2044">{{ $invoice->invoice_number }}</span>
        </div>
        <div class="topbar-right">
            <a href="{{ route('owner.notifications.index') }}" class="icon-btn" style="text-decoration:none;">🔔</a>
            <div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
        </div>
    </div>

    <div class="content">
        @if(session('success'))
            <div style="background:#e8f8f0;color:#1d8348;border:1px solid #a9dfbf;padding:12px 20px;border-radius:8px;margin-bottom:24px;font-size:0.88rem;font-weight:500;">
                ✓ {{ session('success') }}
            </div>
        @endif

        <div class="grid-layout">
            <!-- Main Content Area -->
            <div class="col-main">
                
                <!-- Invoice Document Card -->
                <div class="card p-0">
                    <div class="card-body">
                        <div class="invoice-doc-header">
                            <div class="invoice-doc-title">22UniMart</div>
                            <div class="invoice-doc-sub">Inventory Management System</div>
                        </div>

                        <div class="info-grid">
                            <div class="info-block">
                                <h5>Supplier Information</h5>
                                <p><strong>{{ $invoice->supplier->name }}</strong></p>
                                <p>{{ $invoice->supplier->contact_email }}</p>
                                <p>{{ $invoice->supplier->contact_phone }}</p>
                            </div>
                            
                            <div class="info-block invoice-meta">
                                <div class="meta-row">
                                    <span class="meta-label">Invoice No:</span>
                                    <span class="meta-val">{{ $invoice->invoice_number }}</span>
                                </div>
                                <div class="meta-row">
                                    <span class="meta-label">Date Issued:</span>
                                    <span class="meta-val">{{ $invoice->invoice_date->format('M d, Y') }}</span>
                                </div>
                                <div class="meta-row">
                                    <span class="meta-label">Payment Due:</span>
                                    <span class="meta-val">{{ $invoice->payment_due_date ? $invoice->payment_due_date->format('M d, Y') : 'N/A' }}</span>
                                </div>
                                <div class="meta-row">
                                    <span class="meta-label">Status:</span>
                                    <span class="meta-val">{{ $invoice->computeStatus() }}</span>
                                </div>

                            </div>
                        </div>
                        
                        <div class="info-grid" style="margin-top:-10px;">
                            <div class="info-block">
                                <h5>Buyer / Owner Information</h5>
                                <p><strong>{{ auth()->user()->name }}</strong></p>
                                <p>{{ auth()->user()->email }}</p>
                            </div>
                            
                            @if($invoice->purchaseOrder)
                            <div class="info-block">
                                <h5>Reference</h5>
                                <p><strong>PO Number:</strong> {{ $invoice->purchaseOrder->po_number }}</p>
                                <p><strong>Ordered:</strong> {{ $invoice->purchaseOrder->order_date->format('M d, Y') }}</p>
                            </div>
                            @endif
                        </div>

                        <h5 style="font-size: 0.72rem;font-weight: 700;text-transform: uppercase;letter-spacing: 0.8px;color: #9daec5;margin-bottom: 12px;">Invoice Items</h5>
                        <table class="items-table">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th style="text-align:right;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($invoice->lines as $line)
                                    <tr>
                                        <td>
                                            <div style="font-weight:600;color:#0f2044;">{{ optional($line->item)->name ?? 'Unknown Item' }}</div>
                                            @if($line->uom)
                                                <div style="font-size:0.75rem;color:#7a8fa8;">Unit: {{ $line->uom }}</div>
                                            @endif
                                        </td>
                                        <td>{{ $line->quantity }}</td>
                                        <td>RM {{ number_format($line->unit_price, 2) }}</td>
                                        <td style="text-align:right;font-weight:600;">RM {{ number_format($line->invoice_line_total, 2) }}</td>
                                    </tr>
                                @empty
                                    @if($invoice->purchaseOrder)
                                        <tr>
                                            <td>
                                                <div style="font-weight:600;color:#0f2044;">{{ optional($invoice->purchaseOrder->item)->name ?? 'Unknown Item' }}</div>
                                            </td>
                                            <td>{{ $invoice->purchaseOrder->quantity }}</td>
                                            <td>RM {{ number_format($invoice->purchaseOrder->unit_price, 2) }}</td>
                                            <td style="text-align:right;font-weight:600;">RM {{ number_format($invoice->total_amount, 2) }}</td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td colspan="4" style="text-align:center;color:#9daec5;font-style:italic;padding:24px;">No items found for this invoice.</td>
                                        </tr>
                                    @endif
                                @endforelse
                            </tbody>
                        </table>

                        <div class="totals-block">
                            <div class="total-row">
                                <span>Invoice Total:</span>
                                <span>RM {{ number_format($invoice->total_amount, 2) }}</span>
                            </div>
                            <div class="total-row credit-row">
                                <span>Credit Deduction:</span>
                                <span>- RM {{ number_format($invoice->getTotalCreditDeduction(), 2) }}</span>
                            </div>
                            <div class="total-row grand-total">
                                <span>Net Payable:</span>
                                <span>RM {{ number_format($invoice->getNetPayableAmount(), 2) }}</span>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Related Return Requests -->
                <div class="card p-0">
                    <div class="card-header">
                        <div class="card-title">↩ Related Return Requests</div>
                    </div>
                    @if($invoice->returnRequests->count())
                        <table class="related-table">
                            <thead>
                                <tr>
                                    <th>Return Number</th>
                                    <th>Status</th>
                                    <th>Returned Qty</th>
                                    <th>Credit Amount</th>
                                    <th>Gross Loss</th>
                                    <th>Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoice->returnRequests as $rr)
                                    <tr>
                                        <td><a href="{{ route('owner.return-requests.show', $rr) }}" class="related-link">{{ $rr->return_number }}</a></td>
                                        <td>
                                            <span style="font-size:0.75rem;font-weight:600;padding:2px 8px;border-radius:12px;background:{{ $rr->status==='Approved'?'#e8f8f0':($rr->status==='Pending'?'#fef3e2':'#fdedec') }};color:{{ $rr->status==='Approved'?'#1d8348':($rr->status==='Pending'?'#d4870a':'#c0392b') }};">{{ $rr->status }}</span>
                                        </td>
                                        <td>{{ $rr->lines->sum('quantity') }}</td>
                                        <td style="font-weight:600;color:#27ae60;">RM {{ number_format($rr->getApprovedTotal(), 2) }}</td>
                                        <td>
                                            @if($rr->status === 'Approved' && $rr->getGrossLoss() > 0)
                                                <span style="color:#c0392b;font-weight:600;">RM {{ number_format($rr->getGrossLoss(), 2) }}</span>
                                            @else
                                                <span style="color:#9daec5;">—</span>
                                            @endif
                                        </td>
                                        <td>{{ $rr->request_date ? $rr->request_date->format('d M Y') : 'N/A' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="empty-rel">No return requests have been created for this invoice yet.</div>
                    @endif
                </div>

                <!-- Related Credit Notes -->
                <div class="card p-0">
                    <div class="card-header">
                        <div class="card-title">📋 Related Credit Notes</div>
                    </div>
                    @if($invoice->creditNotes->count())
                        <table class="related-table">
                            <thead>
                                <tr>
                                    <th>Credit Note</th>
                                    <th>Status</th>
                                    <th>Amount</th>
                                    <th>Issued</th>
                                    <th>Linked Return</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoice->creditNotes as $cn)
                                    <tr>
                                        <td><span class="related-link">{{ $cn->credit_note_id }}</span></td>
                                        <td>
                                            <span style="font-size:0.75rem;font-weight:600;padding:2px 8px;border-radius:12px;background:#f8fafc;color:#3a4d6a;border:1px solid #e2e8f0;">{{ $cn->status }}</span>
                                        </td>
                                        <td style="font-weight:600;color:#27ae60;">RM {{ number_format($cn->amount, 2) }}</td>
                                        <td>{{ $cn->issue_date ? \Illuminate\Support\Carbon::parse($cn->issue_date)->format('d M Y') : 'N/A' }}</td>
                                        <td>{{ optional($cn->returnRequest)->return_number ?? 'N/A' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="empty-rel">No credit notes have been generated for this invoice yet.</div>
                    @endif
                </div>

            </div>

            <!-- Actions Sidebar -->
            <div class="col-actions">
                <div class="card">
                    <div class="card-body" style="padding: 20px;">
                        <h5 style="font-size: 0.8rem;font-weight: 700;color: #0f2044;margin-bottom: 16px;">Actions</h5>
                        
                        <a href="{{ route('owner.invoices.export-pdf', $invoice) }}" class="btn-primary-action">
                            ⬇ Download PDF
                        </a>

                        <a href="{{ route('owner.return-requests.create', ['invoice_id' => $invoice->id]) }}" class="btn-primary-action" style="margin-top: 12px;">
                            ↩ Create Return Request
                        </a>
                        
                        <button onclick="window.print()" class="btn-secondary-action">
                            🖨 Print Invoice
                        </button>
                        
                        @if($invoice->purchaseOrder)
                        <a href="{{ route('owner.purchase-orders.show', $invoice->purchaseOrder) }}" class="btn-secondary-action">
                            🛒 View Purchase Order
                        </a>
                        @endif
                    </div>
                </div>
                
                <div class="card" style="background:#f8fafc; border:1px solid #eef2f7; box-shadow:none;">
                    <div class="card-body" style="padding: 16px;">
                        <div style="display:flex;align-items:flex-start;gap:12px;">
                            <div style="font-size:1.5rem;opacity:0.5;">ℹ️</div>
                            <div style="font-size:0.8rem;color:#7a8fa8;line-height:1.5;">
                                This invoice serves as a permanent record. Any discrepancies should be handled via the <strong>Return Request</strong> process.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>

</body>
</html>
