@extends('layouts.owner')

@section('title', 'Return Request ' . $returnRequest->return_number . ' – 22UniMart')

@push('styles')
<style>
    .content-center { max-width: 960px; width: 100%; margin: 0 auto; }
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
@endpush

@section('breadcrumbs')
    <a href="{{ route('owner.return-requests.index') }}">Return Requests</a> › <span style="color:#0f2044;">{{ $returnRequest->return_number }}</span>
@endsection

@section('content')
    <div class="content-center">
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
                        <div class="field-label">Total Approved Qty</div>
                        <div class="field-value">{{ number_format($returnRequest->lines->sum('approved_qty')) }}</div>
                    </div>
                    <div class="field">
                        <div class="field-label">Credit Note Amount</div>
                        <div class="field-value">{{ $returnRequest->creditNote ? 'RM ' . number_format($returnRequest->creditNote->amount, 2) : '—' }}</div>
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
                                    <th style="padding:12px 14px;text-align:left;font-size:.82rem;color:#7a8fa8;">Item Name</th>
                                    <th style="padding:12px 14px;text-align:left;font-size:.82rem;color:#7a8fa8;">Category</th>
                                    <th style="padding:12px 14px;text-align:left;font-size:.82rem;color:#7a8fa8;">Invoice No</th>
                                    <th style="padding:12px 14px;text-align:right;font-size:.82rem;color:#7a8fa8;">Return Qty</th>
                                    <th style="padding:12px 14px;text-align:left;font-size:.82rem;color:#7a8fa8;">Reason</th>
                                    <th style="padding:12px 14px;text-align:left;font-size:.82rem;color:#7a8fa8;">Damage Remark</th>
                                    <th style="padding:12px 14px;text-align:center;font-size:.82rem;color:#7a8fa8;">Status</th>
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
                                        <td style="padding:12px 14px;text-transform:capitalize;">{{ optional($line->item)->category ?? '–' }}</td>
                                        <td style="padding:12px 14px;">
                                            @if($returnRequest->invoice)
                                                <a href="{{ route('owner.invoices.show', $returnRequest->invoice) }}" style="color:#4a90d9;text-decoration:none;">{{ $returnRequest->invoice->invoice_number }}</a>
                                            @else
                                                {{ $returnRequest->invoice_number ?? '—' }}
                                            @endif
                                        </td>
                                        <td style="padding:12px 14px;text-align:right;">{{ number_format($line->quantity) }}</td>
                                        <td style="padding:12px 14px;text-transform:capitalize;">
                                            @if(strtolower($line->reason) === 'expired')
                                                <span style="background:#fef3e2;color:#d4870a;border-radius:4px;padding:2px 7px;font-size:0.72rem;font-weight:700;">Expired</span>
                                            @else
                                                <span style="background:#fdedec;color:#c0392b;border-radius:4px;padding:2px 7px;font-size:0.72rem;font-weight:700;">Damaged</span>
                                            @endif
                                        </td>
                                        <td style="padding:12px 14px;font-size:0.82rem;color:#5a6a85;max-width:200px;">
                                            {{ $line->damage_remark ?? '—' }}
                                        </td>
                                        <td style="padding:12px 14px;text-align:center;">
                                            <span class="badge badge-{{ strtolower($returnRequest->status) === 'draft' ? 'draft' : (strtolower($returnRequest->status) === 'pending' ? 'pending' : (strtolower($returnRequest->status) === 'approved' ? 'approved' : 'rejected')) }}" style="font-size:0.7rem;padding:4px 8px;">{{ $returnRequest->status }}</span>
                                        </td>
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
                            <div class="field-label">Created Date</div>
                            <div class="field-value">{{ $returnRequest->creditNote->created_at->format('M d, Y') }}</div>
                        </div>
                    </div>
                @else
                    <p class="field-value">No credit note has been issued yet for this return request.</p>
                @endif
            </div>
        </div>

        {{-- ── Gross Loss Financial Summary ── --}}
        @if($returnRequest->invoice)
        @if($returnRequest->status === 'Pending')
        <div class="card" style="border: 2px solid #e2e8f0; background: #f8fafc;">
            <div class="card-body">
                <h3 class="card-title" style="color:#5a6a85;">💰 Gross Loss Summary</h3>
                <p class="field-value">Gross loss will be calculated once the supplier approves this return request.</p>
            </div>
        </div>
        @elseif($returnRequest->status === 'Rejected')
        <div class="card" style="border: 2px solid #e2e8f0; background: #f8fafc;">
            <div class="card-body">
                <h3 class="card-title" style="color:#5a6a85;">💰 Gross Loss Summary</h3>
                <p class="field-value">No gross loss to display — this return request was rejected.</p>
            </div>
        </div>
        @elseif($returnRequest->status === 'Approved' && $returnRequest->creditNote)
        @php
            $invoiceTotal   = (float) $returnRequest->invoice->total_amount;
            $creditAmount   = (float) $returnRequest->creditNote->amount;
            $grossLoss      = max(0, $invoiceTotal - $creditAmount);
        @endphp
        <div class="card" style="border: 2px solid #f0d08a; background: #fffdf5;">
            <div class="card-body">
                <h3 class="card-title" style="color:#7a4d06;">💰 Gross Loss Summary</h3>
                <table style="width:100%;border-collapse:collapse;margin-bottom:16px;">
                    <tbody>
                        <tr style="border-bottom:1px solid #f0d08a;">
                            <td style="padding:12px 0;font-size:.9rem;color:#374151;font-weight:600;">Invoice Total</td>
                            <td style="padding:12px 0;text-align:right;font-size:.95rem;font-weight:700;color:#0f2044;">RM {{ number_format($invoiceTotal, 2) }}</td>
                        </tr>
                        <tr style="border-bottom:1px solid #f0d08a;">
                            <td style="padding:12px 0;font-size:.9rem;color:#374151;font-weight:600;">Credit Note Amount</td>
                            <td style="padding:12px 0;text-align:right;font-size:.95rem;font-weight:700;color:#27ae60;">- RM {{ number_format($creditAmount, 2) }}</td>
                        </tr>
                        <tr>
                            <td style="padding:14px 0;font-size:1rem;font-weight:800;color:#7a4d06;">Gross Loss</td>
                            <td style="padding:14px 0;text-align:right;font-size:1.1rem;font-weight:800;color:#c0392b;">RM {{ number_format($grossLoss, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
                <p style="font-size:0.78rem;color:#7a8fa8;line-height:1.6;border-top:1px solid #f0d08a;padding-top:12px;">
                    Gross loss represents the remaining payable amount after returns. This may include both sold and unsold stock as sales tracking is outside system scope.
                </p>
            </div>
        </div>
        @endif
        @endif

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
@endsection
