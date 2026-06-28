@extends('layouts.owner')

@section('title', 'Invoice ' . $invoice->invoice_number . ' – 22UniMart')

@push('styles')
    <style>
        /* Breadcrumb */
        .breadcrumb { font-size: 0.75rem; font-weight: 700; color: #9daec5; letter-spacing: 0.8px; text-transform: uppercase; display: flex; align-items: center; gap: 6px; }
        .breadcrumb a { color: #9daec5; text-decoration: none; transition: color 0.15s; }
        .breadcrumb a:hover { color: #0f2044; }

        /* Grid */
        .grid-layout { display: grid; grid-template-columns: 1fr 340px; gap: 24px; align-items: start; }
        
        /* Typography */
        .page-title { font-size: 1.6rem; font-weight: 800; color: #0f2044; margin: 0; }
        .page-sub { font-size: 0.85rem; color: #7a8fa8; margin-top: 4px; }
        
        /* Cards */
        .card { background: #fff; border-radius: 12px; box-shadow: 0 4px 12px rgba(15,32,68,0.04); overflow: hidden; border: 1px solid #eef2f7; }
        
        /* Buttons */
        .btn-action-outline { display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; background: #fff; color: #0f2044; border: 1px solid #d1dce8; border-radius: 8px; padding: 12px 16px; font-size: 0.9rem; font-weight: 600; cursor: pointer; text-decoration: none; transition: all 0.15s; }
        .btn-action-outline:hover { background: #f8fafc; border-color: #9daec5; }
        .btn-action-primary { display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; background: #0f2044; color: #fff; border: none; border-radius: 8px; padding: 12px 16px; font-size: 0.9rem; font-weight: 600; cursor: pointer; text-decoration: none; transition: background 0.15s; }
        .btn-action-primary:hover { background: #1e3a6e; }

        @media print {
            .sidebar, .topbar, .col-actions, .page-header, .summary-cards-grid, .breadcrumb { display: none !important; }
            .main { margin-left: 0 !important; }
            .content { padding: 0 !important; max-width: 100% !important; }
            .grid-layout { display: block !important; }
            .card { box-shadow: none !important; border: none !important; margin: 0 !important; border-radius: 0 !important; padding: 0 !important; }
            body { background: #fff !important; }
        }
    </style>
@endpush

@section('breadcrumbs')
    <div class="breadcrumb" style="display: inline-flex;">
        <a href="{{ route('owner.invoices.index') }}">Invoices</a>
        <span style="color:#e2e8f0">/</span>
        <span style="color:#0f2044">{{ $invoice->invoice_number }}</span>
    </div>
@endsection

@section('content')
<div class="page-header" style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom: 24px;">
    <div>
        <h1 class="page-title">Invoice Details</h1>
        <div class="page-sub">View invoice information and stock performance.</div>
    </div>
    <a href="{{ route('owner.invoices.index') }}" class="btn-action-outline" style="width:auto; padding: 8px 16px;">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/></svg>
        Back to Invoices
    </a>
</div>

<div class="grid-layout">
    <div class="col-main">
        <!-- 4 Summary Cards -->
        <div class="summary-cards-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px;">
            <div class="card" style="padding: 20px;">
                <div style="font-size: 0.75rem; color: #7a8fa8; font-weight: 700; text-transform: uppercase; margin-bottom: 8px;">Invoice Total</div>
                <div style="font-size: 1.4rem; font-weight: 800; color: #0f2044;">RM {{ number_format($invoice->getNetPayableAmount() + $invoice->getTotalCreditDeduction(), 2) }}</div>
            </div>
            <div class="card" style="padding: 20px;">
                <div style="font-size: 0.75rem; color: #7a8fa8; font-weight: 700; text-transform: uppercase; margin-bottom: 8px;">Net Payable</div>
                <div style="font-size: 1.4rem; font-weight: 800; color: #0f2044;">RM {{ number_format($invoice->getNetPayableAmount(), 2) }}</div>
            </div>
            <div class="card" style="padding: 20px;">
                <div style="font-size: 0.75rem; color: #7a8fa8; font-weight: 700; text-transform: uppercase; margin-bottom: 8px;">Payment Status</div>
                @php $status = $invoice->getDisplayStatus(); @endphp
                <div style="display: inline-block; padding: 4px 10px; border-radius: 6px; font-weight: 700; font-size: 0.8rem; 
                    background: {{ $status === 'Paid' ? '#e8f8f0' : '#fef3e2' }}; 
                    color: {{ $status === 'Paid' ? '#1d8348' : '#d4870a' }};">
                    {{ $status }}
                </div>
            </div>
            <div class="card" style="padding: 20px;">
                <div style="font-size: 0.75rem; color: #7a8fa8; font-weight: 700; text-transform: uppercase; margin-bottom: 8px;">Return Status</div>
                @php 
                    $rrCount = $invoice->returnRequests->count();
                    $cnCount = $invoice->creditNotes->count();
                    $pendingCount = $invoice->returnRequests->where('status', 'Pending')->count();
                    if($cnCount > 0) {
                        $retStatus = 'Credit Note Issued';
                        $retBg = '#e8f0fc'; $retColor = '#2a5fd4';
                    } elseif($pendingCount > 0) {
                        $retStatus = 'Return Pending';
                        $retBg = '#fef3e2'; $retColor = '#d4870a';
                    } elseif($rrCount > 0) {
                        $retStatus = 'Return Approved';
                        $retBg = '#e8f8f0'; $retColor = '#1d8348';
                    } else {
                        $retStatus = 'No Return Request';
                        $retBg = '#f8fafc'; $retColor = '#7a8fa8';
                    }
                @endphp
                <div style="display: inline-block; padding: 4px 10px; border-radius: 6px; font-weight: 700; font-size: 0.8rem; background: {{ $retBg }}; color: {{ $retColor }};">
                    {{ $retStatus }}
                </div>
            </div>
        </div>

        <!-- Invoice Document Card -->
        <div class="card" style="padding: 40px; margin-bottom: 24px;">
            <!-- Header -->
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 40px; padding-bottom: 24px; border-bottom: 1px solid #eef2f7;">
                <div>
                    <div style="font-size: 1.5rem; font-weight: 800; color: #0f2044; margin-bottom: 12px;">{{ $invoice->supplier->name }}</div>
                    <div style="font-size: 0.95rem; color: #5a6a85; line-height: 1.6;">
                        @if($invoice->supplier->address_line_1 || $invoice->supplier->city || $invoice->supplier->country)
                            {{ $invoice->supplier->address_line_1 }}<br>
                            @if($invoice->supplier->address_line_2){{ $invoice->supplier->address_line_2 }}<br>@endif
                            @if($invoice->supplier->city || $invoice->supplier->postal_code)
                                {{ $invoice->supplier->city }}, @if($invoice->supplier->state){{ $invoice->supplier->state }}, @endif {{ $invoice->supplier->postal_code }}<br>
                            @endif
                            {{ $invoice->supplier->country }}<br>
                        @endif
                        @if($invoice->supplier->contact_phone) Phone: {{ $invoice->supplier->contact_phone }}<br> @endif
                        @if($invoice->supplier->contact_email) Email: {{ $invoice->supplier->contact_email }} @endif
                    </div>
                </div>
                <div style="text-align: right;">
                    <div style="font-size: 2rem; font-weight: 800; color: #0f2044; letter-spacing: 1px; margin-bottom: 16px;">INVOICE</div>
                    <div style="display: grid; grid-template-columns: auto auto; gap: 8px 16px; font-size: 0.95rem;">
                        <div style="color: #7a8fa8; font-weight: 600; text-align: right;">Invoice Number</div>
                        <div style="font-weight: 700; color: #0f2044; text-align: right;">{{ $invoice->invoice_number }}</div>
                        <div style="color: #7a8fa8; font-weight: 600; text-align: right;">Invoice Date</div>
                        <div style="font-weight: 700; color: #0f2044; text-align: right;">{{ $invoice->invoice_date->format('M d, Y') }}</div>
                        <div style="color: #7a8fa8; font-weight: 600; text-align: right;">PO Reference</div>
                        <div style="font-weight: 700; color: #0f2044; text-align: right;">{{ optional($invoice->purchaseOrder)->po_number ?? 'N/A' }}</div>
                        <div style="color: #7a8fa8; font-weight: 600; text-align: right;">Payment Terms</div>
                        <div style="font-weight: 700; color: #0f2044; text-align: right;">Net 30 Days</div>
                    </div>
                </div>
            </div>

            <!-- Bill To & Ship To -->
            <div style="display: flex; gap: 40px; margin-bottom: 40px;">
                <div style="flex: 1;">
                    <div style="font-size: 0.8rem; font-weight: 700; color: #9daec5; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px;">Bill To</div>
                    <div style="font-size: 1rem; font-weight: 700; color: #0f2044; margin-bottom: 4px;">{{ auth()->user()->name }}</div>
                    <div style="font-size: 0.95rem; color: #5a6a85; line-height: 1.6;">
                        22UniMart<br>
                        @if(auth()->user()->phone) Phone: {{ auth()->user()->phone }} @endif
                    </div>
                </div>
                <div style="flex: 1;">
                    <div style="font-size: 0.8rem; font-weight: 700; color: #9daec5; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px;">Ship To</div>
                    <div style="font-size: 1rem; font-weight: 700; color: #0f2044; margin-bottom: 4px;">{{ auth()->user()->name }}</div>
                    <div style="font-size: 0.95rem; color: #5a6a85; line-height: 1.6;">
                        22UniMart<br>
                        @if(auth()->user()->phone) Phone: {{ auth()->user()->phone }} @endif
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 32px;">
                <thead>
                    <tr>
                        <th style="padding: 12px 0; font-size: 0.8rem; font-weight: 700; color: #9daec5; text-transform: uppercase; letter-spacing: 0.5px; text-align: left; border-bottom: 2px solid #eef2f7;">Item</th>
                        <th style="padding: 12px 0; font-size: 0.8rem; font-weight: 700; color: #9daec5; text-transform: uppercase; letter-spacing: 0.5px; text-align: center; border-bottom: 2px solid #eef2f7;">Qty</th>
                        <th style="padding: 12px 0; font-size: 0.8rem; font-weight: 700; color: #9daec5; text-transform: uppercase; letter-spacing: 0.5px; text-align: right; border-bottom: 2px solid #eef2f7;">Unit Price</th>
                        <th style="padding: 12px 0; font-size: 0.8rem; font-weight: 700; color: #9daec5; text-transform: uppercase; letter-spacing: 0.5px; text-align: right; border-bottom: 2px solid #eef2f7;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php $subtotal = 0; @endphp
                    @forelse($invoice->lines as $line)
                        @php $subtotal += $line->invoice_line_total; @endphp
                        <tr>
                            <td style="padding: 16px 0; border-bottom: 1px solid #f0f4f8; font-size: 0.95rem; color: #0f2044; font-weight: 600;">{{ optional($line->item)->name ?? 'Unknown Item' }}</td>
                            <td style="padding: 16px 0; border-bottom: 1px solid #f0f4f8; font-size: 0.95rem; color: #5a6a85; text-align: center;">{{ $line->quantity }}</td>
                            <td style="padding: 16px 0; border-bottom: 1px solid #f0f4f8; font-size: 0.95rem; color: #5a6a85; text-align: right;">RM {{ number_format($line->unit_price, 2) }}</td>
                            <td style="padding: 16px 0; border-bottom: 1px solid #f0f4f8; font-size: 0.95rem; font-weight: 700; color: #0f2044; text-align: right;">RM {{ number_format($line->invoice_line_total, 2) }}</td>
                        </tr>
                    @empty
                        @if($invoice->purchaseOrder && $invoice->purchaseOrder->quantity)
                            @php $subtotal = $invoice->total_amount; @endphp
                            <tr>
                                <td style="padding: 16px 0; border-bottom: 1px solid #f0f4f8; font-size: 0.95rem; color: #0f2044; font-weight: 600;">{{ optional($invoice->purchaseOrder->item)->name ?? 'Unknown Item' }}</td>
                                <td style="padding: 16px 0; border-bottom: 1px solid #f0f4f8; font-size: 0.95rem; color: #5a6a85; text-align: center;">{{ $invoice->purchaseOrder->quantity }}</td>
                                <td style="padding: 16px 0; border-bottom: 1px solid #f0f4f8; font-size: 0.95rem; color: #5a6a85; text-align: right;">RM {{ number_format($invoice->purchaseOrder->unit_price, 2) }}</td>
                                <td style="padding: 16px 0; border-bottom: 1px solid #f0f4f8; font-size: 0.95rem; font-weight: 700; color: #0f2044; text-align: right;">RM {{ number_format($invoice->total_amount, 2) }}</td>
                            </tr>
                        @endif
                    @endforelse
                </tbody>
            </table>

            <!-- Totals -->
            <div style="display: flex; justify-content: flex-end;">
                <div style="width: 320px;">
                    <div style="display: flex; justify-content: space-between; padding: 16px 0; border-top: 2px solid #0f2044; font-size: 1.2rem; font-weight: 800; color: #0f2044;">
                        <span>Total</span>
                        <span>RM {{ number_format($subtotal, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Footer Notes -->
            <div style="margin-top: 40px; padding-top: 24px; font-size: 0.9rem; color: #7a8fa8; text-align: center; line-height: 1.6;">
                Make all payments payable to <strong style="color: #0f2044;">{{ $invoice->supplier->name }}</strong><br>
                If you have any questions, please contact {{ $invoice->supplier->contact_email ?? 'us' }}<br>
                <div style="margin-top: 12px; font-weight: 700; color: #0f2044; font-size: 1rem;">Thank You For Your Business!</div>
            </div>
        </div>

        <!-- Related Return Requests -->
        <div class="card" style="margin-bottom: 24px;">
            <div style="padding: 20px 24px; border-bottom: 1px solid #eef2f7; font-size: 1.1rem; font-weight: 700; color: #0f2044;">Related Return Requests</div>
            @if($invoice->returnRequests->count())
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; min-width: 600px;">
                        <thead>
                            <tr>
                                <th style="padding: 16px 24px; text-align: left; font-size: 0.75rem; font-weight: 700; color: #9daec5; text-transform: uppercase; background: #f8fafc; border-bottom: 1px solid #eef2f7;">Return Number</th>
                                <th style="padding: 16px 24px; text-align: left; font-size: 0.75rem; font-weight: 700; color: #9daec5; text-transform: uppercase; background: #f8fafc; border-bottom: 1px solid #eef2f7;">Status</th>
                                <th style="padding: 16px 24px; text-align: right; font-size: 0.75rem; font-weight: 700; color: #9daec5; text-transform: uppercase; background: #f8fafc; border-bottom: 1px solid #eef2f7;">Returned Qty</th>
                                <th style="padding: 16px 24px; text-align: right; font-size: 0.75rem; font-weight: 700; color: #9daec5; text-transform: uppercase; background: #f8fafc; border-bottom: 1px solid #eef2f7;">Credit Amount</th>
                                <th style="padding: 16px 24px; text-align: right; font-size: 0.75rem; font-weight: 700; color: #9daec5; text-transform: uppercase; background: #f8fafc; border-bottom: 1px solid #eef2f7;">Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoice->returnRequests as $rr)
                                <tr>
                                    <td style="padding: 16px 24px; border-bottom: 1px solid #f0f4f8;"><a href="{{ route('owner.return-requests.show', $rr) }}" style="color: #2a5fd4; text-decoration: none; font-weight: 600;">{{ $rr->return_number }}</a></td>
                                    <td style="padding: 16px 24px; border-bottom: 1px solid #f0f4f8;">
                                        <span style="font-size:0.75rem;font-weight:700;padding:4px 10px;border-radius:6px;background:{{ $rr->status==='Approved'?'#e8f8f0':($rr->status==='Pending'?'#fef3e2':'#fdedec') }};color:{{ $rr->status==='Approved'?'#1d8348':($rr->status==='Pending'?'#d4870a':'#c0392b') }};">{{ $rr->status }}</span>
                                    </td>
                                    <td style="padding: 16px 24px; border-bottom: 1px solid #f0f4f8; text-align: right; color: #3a4d6a;">{{ $rr->lines->sum('quantity') }}</td>
                                    <td style="padding: 16px 24px; border-bottom: 1px solid #f0f4f8; text-align: right; font-weight: 700; color: #27ae60;">RM {{ number_format($rr->getApprovedTotal(), 2) }}</td>
                                    <td style="padding: 16px 24px; border-bottom: 1px solid #f0f4f8; text-align: right; color: #7a8fa8;">{{ $rr->request_date ? $rr->request_date->format('d M Y') : 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div style="padding: 40px 24px; text-align: center;">
                    <div style="color: #0f2044; font-weight: 600; margin-bottom: 8px; font-size: 1rem;">No return requests have been created for this invoice.</div>
                    <div style="color: #7a8fa8; font-size: 0.9rem;">Create a Return Request if damaged or expired goods are found after receiving.</div>
                </div>
            @endif
        </div>

        <!-- Related Credit Notes -->
        <div class="card" style="margin-bottom: 24px;">
            <div style="padding: 20px 24px; border-bottom: 1px solid #eef2f7; font-size: 1.1rem; font-weight: 700; color: #0f2044;">Related Credit Notes</div>
            @if($invoice->creditNotes->count())
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; min-width: 600px;">
                        <thead>
                            <tr>
                                <th style="padding: 16px 24px; text-align: left; font-size: 0.75rem; font-weight: 700; color: #9daec5; text-transform: uppercase; background: #f8fafc; border-bottom: 1px solid #eef2f7;">Credit Note</th>
                                <th style="padding: 16px 24px; text-align: left; font-size: 0.75rem; font-weight: 700; color: #9daec5; text-transform: uppercase; background: #f8fafc; border-bottom: 1px solid #eef2f7;">Status</th>
                                <th style="padding: 16px 24px; text-align: right; font-size: 0.75rem; font-weight: 700; color: #9daec5; text-transform: uppercase; background: #f8fafc; border-bottom: 1px solid #eef2f7;">Amount</th>
                                <th style="padding: 16px 24px; text-align: right; font-size: 0.75rem; font-weight: 700; color: #9daec5; text-transform: uppercase; background: #f8fafc; border-bottom: 1px solid #eef2f7;">Issued</th>
                                <th style="padding: 16px 24px; text-align: right; font-size: 0.75rem; font-weight: 700; color: #9daec5; text-transform: uppercase; background: #f8fafc; border-bottom: 1px solid #eef2f7;">Linked Return</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoice->creditNotes as $cn)
                                <tr>
                                    <td style="padding: 16px 24px; border-bottom: 1px solid #f0f4f8; font-weight: 600; color: #0f2044;">{{ $cn->credit_note_id }}</td>
                                    <td style="padding: 16px 24px; border-bottom: 1px solid #f0f4f8;">
                                        <span style="font-size:0.75rem;font-weight:700;padding:4px 10px;border-radius:6px;background:#f8fafc;color:#3a4d6a;border:1px solid #eef2f7;">{{ $cn->status }}</span>
                                    </td>
                                    <td style="padding: 16px 24px; border-bottom: 1px solid #f0f4f8; text-align: right; font-weight: 700; color: #27ae60;">RM {{ number_format($cn->amount, 2) }}</td>
                                    <td style="padding: 16px 24px; border-bottom: 1px solid #f0f4f8; text-align: right; color: #7a8fa8;">{{ $cn->issue_date ? \Illuminate\Support\Carbon::parse($cn->issue_date)->format('d M Y') : 'N/A' }}</td>
                                    <td style="padding: 16px 24px; border-bottom: 1px solid #f0f4f8; text-align: right; color: #7a8fa8;">{{ optional($cn->returnRequest)->return_number ?? 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div style="padding: 40px 24px; text-align: center;">
                    <div style="color: #0f2044; font-weight: 600; font-size: 1rem;">No credit notes have been generated for this invoice.</div>
                </div>
            @endif
        </div>
    </div> <!-- end col-main -->

    <!-- Sidebar Layout -->
    <div class="col-actions">
        <!-- Inventory Summary -->
        <div class="card" style="margin-bottom: 24px;">
            <div style="padding: 16px 24px; border-bottom: 1px solid #eef2f7; font-weight: 800; color: #0f2044; font-size: 1rem;">
                Inventory Summary
            </div>
            <div style="padding: 0;">
                @forelse($invoice->lines as $line)
                    @if(!$line->item) @continue @endif
                    @php
                        $item = $line->item;
                        $purchased = (int) $line->quantity;
                        $unitPrice = (float) $line->unit_price;
                        $currentStock = (int) $item->quantity;
                        $po = $invoice->purchaseOrder;
                        $poItem = ($po && $po->orderItems) ? $po->orderItems->where('item_id', $item->id)->first() : null;
                        $goodReceived = ($poItem && !is_null($poItem->good_quantity)) ? (int) $poItem->good_quantity : 0;
                        $inventoryValue = $currentStock * $unitPrice;
                        $potentialGrossProfit = ($currentStock * (float) $item->selling_price) - $inventoryValue;
                    @endphp
                    <div style="padding: 20px 24px; border-bottom: 1px solid #f0f4f8;">
                        <div style="font-weight: 800; color: #0f2044; margin-bottom: 16px; font-size: 0.95rem;">{{ $item->name }}</div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 0.9rem; color: #7a8fa8;">
                            <span>Purchased (Invoice)</span>
                            <span style="color: #0f2044; font-weight: 700;">{{ $purchased }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 0.9rem; color: #7a8fa8;">
                            <span>Good Received</span>
                            <span style="color: #0f2044; font-weight: 700;">{{ $goodReceived }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 16px; font-size: 0.9rem; color: #7a8fa8;">
                            <span>Current Stock</span>
                            <span style="color: #0f2044; font-weight: 700;">{{ $currentStock }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 0.9rem; color: #7a8fa8;">
                            <span>Inventory Value</span>
                            <span style="color: #0f2044; font-weight: 700;">RM {{ number_format($inventoryValue, 2) }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-size: 0.9rem; color: #7a8fa8;">
                            <span>Remaining Potential Profit</span>
                            <span style="color: #27ae60; font-weight: 800;">RM {{ number_format($potentialGrossProfit, 2) }}</span>
                        </div>
                    </div>
                @empty
                    <div style="padding: 24px; text-align: center; color: #9daec5; font-size: 0.9rem; font-style: italic;">
                        No item data available.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Status Card -->
        <div class="card" style="margin-bottom: 24px; padding: 24px;">
            <div style="font-size: 1rem; font-weight: 800; color: #0f2044; margin-bottom: 16px;">Invoice Status</div>
            <div style="margin-bottom: 24px;">
                @if($status === 'Paid')
                    <div style="display: inline-block; background:#e8f8f0; color:#1d8348; padding:6px 12px; border-radius:6px; font-weight:700; font-size:0.85rem;">Paid</div>
                @else
                    <div style="display: inline-block; background:#fef3e2; color:#d4870a; padding:6px 12px; border-radius:6px; font-weight:700; font-size:0.85rem;">Pending Payment</div>
                @endif
            </div>
            
            <div style="display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 0.9rem;">
                <span style="color: #7a8fa8;">Invoice Date</span>
                <span style="color: #0f2044; font-weight: 700;">{{ $invoice->invoice_date->format('M d, Y') }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 0.9rem;">
                <span style="color: #7a8fa8;">Due Date</span>
                <span style="color: #0f2044; font-weight: 700;">{{ $invoice->invoice_date->addDays(30)->format('M d, Y') }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 0.9rem;">
                <span style="color: #7a8fa8;">Payment Terms</span>
                <span style="color: #0f2044; font-weight: 700;">Net 30 Days</span>
            </div>
        </div>

        <!-- Actions Card -->
        <div class="card" style="padding: 24px;">
            <div style="font-size: 1rem; font-weight: 800; color: #0f2044; margin-bottom: 16px;">Actions</div>
            
            <a href="{{ route('owner.invoices.export-pdf', $invoice) }}" class="btn-action-primary">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                Download PDF
            </a>

            <button onclick="window.print()" class="btn-action-outline">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                Print Invoice
            </button>

            @if($invoice->purchaseOrder)
                <a href="{{ route('owner.purchase-orders.show', $invoice->purchaseOrder) }}" class="btn-action-outline">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                    View Purchase Order
                </a>
            @endif

            <a href="{{ route('owner.return-requests.create', ['invoice_id' => $invoice->id]) }}" class="btn-action-outline" style="margin-bottom: 0;">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 14 4 9 9 4"></polyline><path d="M20 20v-7a4 4 0 0 0-4-4H4"></path></svg>
                Create Return Request
            </a>
        </div>
    </div> <!-- end col-actions -->
</div> <!-- end grid-layout -->
@endsection
