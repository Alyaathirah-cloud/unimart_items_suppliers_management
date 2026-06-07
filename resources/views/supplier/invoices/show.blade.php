<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice {{ $invoice->invoice_number }} – Supplier Portal</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
        
        .breadcrumb { font-size: 0.75rem; font-weight: 700; color: #9daec5; letter-spacing: 0.8px; text-transform: uppercase; display: flex; align-items: center; gap: 6px; }
        .breadcrumb a { color: #9daec5; text-decoration: none; transition: color 0.15s; }
        .breadcrumb a:hover { color: #0f2044; }

        .content { padding: 32px; max-width: 1200px; margin: 0 auto; width: 100%; }
        
        .grid-layout { display: grid; grid-template-columns: 1fr 340px; gap: 24px; align-items: start; }

        .card { background: #fff; border-radius: 12px; box-shadow: 0 1px 6px rgba(15,32,68,0.07); overflow: hidden; margin-bottom: 24px; }
        .card-header { padding: 20px 24px 16px; border-bottom: 1px solid #f0f4f8; display: flex; align-items: center; justify-content: space-between; }
        .card-title { font-size: 0.95rem; font-weight: 700; color: #0f2044; display: flex; align-items: center; gap: 8px; }
        .card-body { padding: 24px; }

        .invoice-doc-header { text-align: center; margin-bottom: 32px; padding-bottom: 24px; border-bottom: 2px dashed #e2e8f0; }
        .invoice-doc-title { font-size: 1.8rem; font-weight: 800; color: #0f2044; margin-bottom: 4px; }
        .invoice-doc-sub { font-size: 0.9rem; color: #7a8fa8; }

        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 32px; }
        .info-block h5 { font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: #9daec5; margin-bottom: 8px; }
        .info-block p { font-size: 0.88rem; color: #3a4d6a; line-height: 1.6; margin-bottom: 2px; }
        .info-block strong { color: #0f2044; font-weight: 700; font-size: 0.95rem; }

        .invoice-meta { background: #f8fafc; border: 1px solid #eef2f7; border-radius: 8px; padding: 16px; }
        .meta-row { display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 0.88rem; }
        .meta-row:last-child { margin-bottom: 0; }
        .meta-label { color: #7a8fa8; }
        .meta-val { color: #0f2044; font-weight: 600; text-align: right; }

        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        .items-table th { padding: 12px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: #7a8fa8; border-bottom: 2px solid #eef2f7; text-align: left; }
        .items-table td { padding: 16px 12px; font-size: 0.88rem; color: #3a4d6a; border-bottom: 1px solid #f4f6fb; }
        .items-table tr:last-child td { border-bottom: none; }
        
        .totals-block { width: 300px; margin-left: auto; }
        .total-row { display: flex; justify-content: space-between; padding: 8px 0; font-size: 0.88rem; color: #3a4d6a; }
        .total-row.grand-total { border-top: 2px solid #eef2f7; margin-top: 8px; padding-top: 12px; font-size: 1.1rem; font-weight: 800; color: #0f2044; }
        .credit-row { color: #e74c3c; font-weight: 600; }

        .btn-primary-action { background: #0f2044; color: #fff; border: none; border-radius: 8px; padding: 12px 16px; font-size: 0.88rem; font-weight: 600; cursor: pointer; text-align: center; text-decoration: none; display: block; transition: background 0.15s; width: 100%; margin-bottom: 12px; }
        .btn-primary-action:hover { background: #1e3a6e; }
        .btn-secondary-action { background: #fff; color: #3a4d6a; border: 1px solid #d1dce8; border-radius: 8px; padding: 12px 16px; font-size: 0.88rem; font-weight: 600; cursor: pointer; text-align: center; text-decoration: none; display: block; transition: all 0.15s; width: 100%; margin-bottom: 12px; }
        .btn-secondary-action:hover { background: #f4f6fb; color: #0f2044; border-color: #9daec5; }

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
@include('supplier.partials.sidebar', ['active' => 'invoices'])

<!-- Main -->
<div class="main">
    <!-- Topbar -->
    <div class="topbar">
        <div class="breadcrumb">
            <a href="{{ route('supplier.invoices.index') }}">Invoices</a>
            <span style="color:#e2e8f0">/</span>
            <span style="color:#0f2044">{{ $invoice->invoice_number }}</span>
        </div>
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
                                <h5>Your Information</h5>
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
                                <h5>Billed To</h5>
                                <p><strong>22UniMart Administration</strong></p>
                            </div>
                            
                            @if($invoice->purchaseOrder)
                            <div class="info-block">
                                <h5>Reference</h5>
                                <p><strong>PO Number:</strong> <a href="{{ route('supplier.purchase-orders.index') }}?search={{ $invoice->purchaseOrder->po_number }}" style="color:#4a90d9;text-decoration:none;">{{ $invoice->purchaseOrder->po_number }}</a></p>
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
                                        <td>{{ $rr->return_number }}</td>
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
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoice->creditNotes as $cn)
                                    <tr>
                                        <td><a href="{{ route('supplier.credit-notes.show', $cn) }}" class="related-link">{{ $cn->credit_note_id }}</a></td>
                                        <td>
                                            <span style="font-size:0.75rem;font-weight:600;padding:2px 8px;border-radius:12px;background:#f8fafc;color:#3a4d6a;border:1px solid #e2e8f0;">{{ $cn->status }}</span>
                                        </td>
                                        <td style="font-weight:600;color:#27ae60;">RM {{ number_format($cn->amount, 2) }}</td>
                                        <td>{{ $cn->issue_date ? \Illuminate\Support\Carbon::parse($cn->issue_date)->format('d M Y') : 'N/A' }}</td>
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
                        
                        <a href="{{ route('supplier.invoices.export-pdf', $invoice) }}" class="btn-primary-action">
                            ⬇ Download PDF
                        </a>
                        
                        <button onclick="window.print()" class="btn-secondary-action">
                            🖨 Print Invoice
                        </button>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>

</body>
</html>
