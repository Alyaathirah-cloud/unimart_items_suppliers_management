@extends('layouts.staff')

@section('title', 'Invoices – 22UniMart')

@push('styles')
    <style>
        .page-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 28px; }
        .page-title { font-size: 1.6rem; font-weight: 800; color: #0f2044; }
        .page-sub   { font-size: 0.85rem; color: #7a8fa8; margin-top: 4px; }
        .btn-primary-action { background: #0f2044; color: #fff; border: none; border-radius: 8px; padding: 11px 20px; font-size: 0.88rem; font-weight: 600; cursor: pointer; font-family: 'Inter', sans-serif; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: background 0.15s; white-space: nowrap; }
        .btn-primary-action:hover { background: #1e3a6e; color: #fff; }

        /* Filters bar */
        .filter-bar { background: #fff; border-radius: 12px; padding: 16px 20px; box-shadow: 0 1px 6px rgba(15,32,68,0.07); margin-bottom: 20px; display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
        .filter-select { border: 1px solid #e2e8f0; border-radius: 8px; padding: 8px 14px; font-size: 0.85rem; font-family: 'Inter', sans-serif; color: #3a4d6a; background: #f8fafc; outline: none; cursor: pointer; }
        .filter-select:focus { border-color: #4a90d9; }
        .filter-btn { background: #0f2044; color: #fff; border: none; border-radius: 8px; padding: 8px 18px; font-size: 0.85rem; font-weight: 600; cursor: pointer; font-family: 'Inter', sans-serif; transition: background 0.15s; }
        .filter-btn:hover { background: #1e3a6e; }
        .filter-clear { background: #fff; color: #7a8fa8; border: 1px solid #e2e8f0; border-radius: 8px; padding: 8px 18px; font-size: 0.85rem; font-weight: 500; cursor: pointer; font-family: 'Inter', sans-serif; text-decoration: none; transition: all 0.15s; }
        .filter-clear:hover { background: #f4f6fb; color: #0f2044; }
        .filter-spacer { flex: 1; }
        .results-info { font-size: 0.8rem; color: #7a8fa8; white-space: nowrap; }

        /* Table card */
        .table-card { background: #fff; border-radius: 12px; box-shadow: 0 1px 6px rgba(15,32,68,0.07); overflow: hidden; }
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        thead tr { background: #f8fafc; border-bottom: 2px solid #eef2f7; }
        thead th { padding: 13px 16px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: #7a8fa8; text-align: left; white-space: nowrap; }
        tbody tr { border-bottom: 1px solid #f4f6fb; transition: background 0.12s; }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: #f8fafc; }
        tbody td { padding: 14px 16px; font-size: 0.86rem; color: #3a4d6a; vertical-align: middle; }

        .inv-number { font-size: 0.78rem; font-weight: 700; color: #4a90d9; font-family: monospace; letter-spacing: 0.5px; }
        .po-number { font-size: 0.72rem; font-weight: 700; color: #7a8fa8; font-family: monospace; }
        .item-name { font-weight: 600; color: #0f2044; font-size: 0.85rem; }
        .item-details  { font-size: 0.72rem; color: #9daec5; margin-top: 2px; }
        .supplier-link { color: #4a90d9; font-weight: 500; font-size: 0.85rem; text-decoration: none; }
        .supplier-link:hover { text-decoration: underline; }
        
        .inv-total { font-weight: 700; color: #0f2044; }

        /* Status badges */
        .badge-status { display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; }
        .badge-unpaid { background: #fef3e2; color: #d4870a; }
        .badge-paid { background: #e8f8f0; color: #1d8348; }
        .badge-dot { width: 7px; height: 7px; border-radius: 50%; background: currentColor; display: inline-block; }

        /* Action link */
        .action-link { font-size: 0.8rem; color: #4a90d9; text-decoration: none; font-weight: 600; white-space: nowrap; }
        .action-link:hover { text-decoration: underline; }
        .action-btn { background: #f4f6fb; border: 1px solid #d1dce8; border-radius: 6px; padding: 6px 12px; font-size: 0.78rem; font-weight: 600; color: #3a4d6a; cursor: pointer; text-decoration: none; transition: background 0.15s; }
        .action-btn:hover { background: #e2e8f0; }

        /* Pagination */
        .table-footer { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-top: 1px solid #f0f4f8; background: #fafbfd; flex-wrap: wrap; gap: 12px; }
        .footer-info { font-size: 0.8rem; color: #7a8fa8; }
        .pagination-wrap { display: flex; align-items: center; gap: 8px; }
        .page-btn { width: 32px; height: 32px; border-radius: 7px; border: 1px solid #e2e8f0; background: #fff; color: #3a4d6a; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 0.85rem; text-decoration: none; font-family: 'Inter', sans-serif; transition: all 0.12s; }
        .page-btn:hover { background: #0f2044; color: #fff; border-color: #0f2044; }
        .page-btn.disabled { opacity: 0.4; pointer-events: none; }

        /* Empty */
        .empty-state { padding: 60px 20px; text-align: center; color: #9daec5; }
        .empty-icon  { font-size: 2.5rem; margin-bottom: 12px; }
        .empty-text  { font-size: 0.95rem; font-weight: 600; color: #7a8fa8; }

        /* Generate missing invoices btn */
        .btn-generate-missing { background: #d4870a; color: #fff; border: none; border-radius: 8px; padding: 10px 18px; font-size: 0.84rem; font-weight: 700; cursor: pointer; font-family: 'Inter', sans-serif; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: background 0.15s; }
        .btn-generate-missing:hover { background: #b3720a; color: #fff; }
        
        .topbar-search { display: flex; align-items: center; gap: 8px; background: #f4f6fb; border-radius: 8px; padding: 8px 14px; flex: 1; max-width: 360px; margin-right: auto; }
        .topbar-search input { border: none; background: transparent; outline: none; font-family: 'Inter', sans-serif; font-size: 0.88rem; color: #3a4d6a; width: 100%; }
        .topbar-search input::placeholder { color: #9daec5; }
    </style>
@endpush

@section('topbar')
    <div class="topbar-search">
        <svg width="15" height="15" fill="none" stroke="#9daec5" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
        <input type="text" placeholder="Search invoices...">
    </div>
    <div class="topbar-right">
        <a href="{{ route('staff.notif.index') }}" class="icon-btn" style="text-decoration:none;">🔔</a>
        @include('owner.components.topbar-profile')
    </div>
@endsection

@section('content')

        <!-- Page header -->
        <div class="page-header">
            <div>
                <div class="page-title">Invoices</div>
                <div class="page-sub">Manage and view all invoices from suppliers.</div>
            </div>

        </div>

        @if(session('success'))
            <div class="flash flash-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="flash flash-error">{{ session('error') }}</div>
        @endif

        <!-- Filter bar -->
        <form method="GET" action="{{ route('staff.invoice.index') }}">
            <div class="filter-bar">
                @if($suppliers->count() > 0)
                <select name="supplier_id" class="filter-select">
                    <option value="">All Suppliers</option>
                    @foreach($suppliers as $sup)
                        <option value="{{ $sup->id }}" {{ request('supplier_id') == $sup->id ? 'selected' : '' }}>{{ $sup->name }}</option>
                    @endforeach
                </select>
                @endif

                <select name="status" class="filter-select">
                    <option value="">All Statuses</option>
                    <option value="Unpaid" {{ request('status') == 'Unpaid' ? 'selected' : '' }}>Unpaid</option>
                    <option value="Settled" {{ request('status') == 'Settled' ? 'selected' : '' }}>Settled</option>
                    <option value="Paid" {{ request('status') == 'Paid' ? 'selected' : '' }}>Paid</option>
                    <option value="Active" {{ request('status') == 'Active' ? 'selected' : '' }}>Active (Legacy)</option>
                </select>

                <button type="submit" class="filter-btn">Apply Filters</button>
                <a href="{{ route('staff.invoice.index') }}" class="filter-clear">Clear</a>

                <div class="filter-spacer"></div>

                @if($invoices instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <div class="results-info">
                        Displaying {{ $invoices->firstItem() ?? 0 }}–{{ $invoices->lastItem() ?? 0 }} of {{ $invoices->total() }}
                    </div>
                @endif
            </div>
        </form>

        <!-- Table -->
        <div class="table-card">
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Invoice No & PO</th>
                            <th>Status</th>
                            <th>Item Details</th>
                            <th>Supplier</th>
                            <th>Invoice Total</th>
                            <th>Credit Deduction</th>
                            <th>Net Payable</th>
                            <th>Invoice Date</th>
                            <th>Due Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($invoices as $invoice)
                        <tr>
                            <td>
                                <div class="inv-number">{{ $invoice->invoice_number }}</div>
                                @if($invoice->purchaseOrder)
                                    <div class="po-number">PO: {{ $invoice->purchaseOrder->po_number }}</div>
                                @endif
                            </td>
                            <td>
                                @php
                                    $computedStatus = $invoice->computeStatus();
                                    $statusColor = $invoice->statusColor();
                                    $bg = '#fef3e2'; $col = '#d4870a'; // Unpaid = orange
                                    if($statusColor === 'green')  { $bg = '#e8f8f0'; $col = '#1d8348'; }   // Paid
                                    elseif($statusColor === 'purple') { $bg = '#f3e8ff'; $col = '#9333ea'; } // Settled
                                @endphp
                                <span style="display:inline-block;padding:4px 10px;border-radius:12px;font-size:0.75rem;font-weight:700;background:{{ $bg }};color:{{ $col }};">{{ $computedStatus }}</span>
                            </td>
                            <td>
                                @if($invoice->lines->count() > 0)
                                    @php $firstLine = $invoice->lines->first(); @endphp
                                    <div class="item-name">{{ optional($firstLine->item)->name ?? 'Unknown Item' }}</div>
                                    <div class="item-details">
                                        Qty: {{ $firstLine->quantity }} &nbsp;|&nbsp; Unit Price: RM {{ number_format($firstLine->unit_price, 2) }}
                                        @if($invoice->lines->count() > 1)
                                            <br><em>+ {{ $invoice->lines->count() - 1 }} more items</em>
                                        @endif
                                    </div>
                                @elseif($invoice->purchaseOrder)
                                    <div class="item-name">{{ optional($invoice->purchaseOrder->item)->name ?? 'Unknown Item' }}</div>
                                    <div class="item-details">
                                        Qty: {{ $invoice->purchaseOrder->quantity }} &nbsp;|&nbsp; Unit Price: RM {{ number_format($invoice->purchaseOrder->unit_price, 2) }}
                                    </div>
                                @else
                                    <span style="color:#9daec5;">No items listed</span>
                                @endif
                            </td>
                            <td>
                                <span style="font-weight:600; color:#0f2044;">{{ $invoice->supplier->name ?? 'N/A' }}</span>
                            </td>
                            <td>
                                <div class="inv-total">RM {{ number_format($invoice->total_amount, 2) }}</div>
                            </td>
                            <td>
                                <div style="font-weight:700;color:#e74c3c;">RM {{ number_format($invoice->getTotalCreditDeduction(), 2) }}</div>
                            </td>
                            <td>
                                <div class="inv-total">RM {{ number_format($invoice->getNetPayableAmount(), 2) }}</div>
                                @if($invoice->getTotalCreditDeduction() > 0)
                                    <div style="font-size:0.7rem;color:#e74c3c;font-weight:600;margin-top:2px;">
                                        -RM {{ number_format($invoice->getTotalCreditDeduction(), 2) }} credited
                                    </div>
                                @endif
                            </td>
                            <td style="white-space:nowrap;color:#7a8fa8;">{{ $invoice->invoice_date->format('M d, Y') }}</td>
                            <td style="white-space:nowrap;color:#7a8fa8;">{{ $invoice->payment_due_date ? $invoice->payment_due_date->format('M d, Y') : 'N/A' }}</td>
                            <td>
                                <div style="display:flex;gap:8px;align-items:center;">
                                    <a href="{{ route('staff.invoice.show', $invoice) }}" class="action-link">View Details</a>
                                    <a href="{{ route('staff.invoice.export-pdf', $invoice) }}" class="action-btn">CSV</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">
                                <div class="empty-state">
                                    <div class="empty-icon">📄</div>
                                    <div class="empty-text">No invoices found</div>
                                    <div class="empty-sub">Try adjusting your filters.</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Table footer / pagination -->
            <div class="table-footer">
                <div class="footer-info">Records kept as per system policy.</div>
                <div class="pagination-wrap">
                    @if($invoices instanceof \Illuminate\Pagination\LengthAwarePaginator)
                        @if($invoices->onFirstPage())
                            <span class="page-btn disabled">‹</span>
                        @else
                            <a class="page-btn" href="{{ $invoices->previousPageUrl() }}">‹</a>
                        @endif

                        <span style="font-size:0.8rem;color:#7a8fa8;padding:0 8px;">Page {{ $invoices->currentPage() }} / {{ $invoices->lastPage() }}</span>

                        @if($invoices->hasMorePages())
                            <a class="page-btn" href="{{ $invoices->nextPageUrl() }}">›</a>
                        @else
                            <span class="page-btn disabled">›</span>
                        @endif
                    @endif
                </div>
            </div>
        </div>

@endsection
