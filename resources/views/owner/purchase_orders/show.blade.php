@extends('layouts.owner')

@section('title', 'PO #' . $purchaseOrder->po_number . ' – 22UniMart')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/po-detail.css') }}">
<style>
    /* Button system */
    .btn { display:inline-flex; align-items:center; justify-content:center; gap:8px; padding:10px 16px; border-radius:8px; border:0; cursor:pointer; font-weight:600; transition:all .12s ease; font-family:inherit; }
    .btn-primary { background:#0f3b73; color:#fff; }
    .btn-primary:hover { background:#0c2f59; }
    .btn-secondary { background:#fff; color:#0f3b73; border:1px solid #dbe7f5; }
    .btn-secondary:hover { background:#f4f8fc; }
    .btn-danger { background:#c0392b; color:#fff; }
    .btn-danger:hover { background:#9f2e25; }
    .btn-outline { background:transparent; color:#0f3b73; border:1px solid rgba(15,59,115,0.12); }
    .btn-export-pdf, .btn-view-invoice, .btn-modal-close, .btn-report { padding:8px 14px; border-radius:8px; }
    .btn-export-pdf { background:#eef6ff; color:#0f3b73; border:1px solid #dbe7f5; }
    .btn-view-invoice { background:#0f3b73; color:#fff; }
    .btn-modal-close { background:#fff; color:#0f3b73; border:1px solid #e6eefc; }
    .btn-report { background:#c0392b; color:#fff; }
    .btn-report:hover { background:#9f2e25; }
    /* Modal styles */
    .modal-overlay { position:fixed; inset:0; display:none; align-items:center; justify-content:center; background:rgba(6,12,28,0.6); z-index:9999; }
    .modal-overlay.open { display:flex; }
    .modal-box { background:#fff; border-radius:12px; width:480px; max-width:92%; padding:20px; box-shadow:0 8px 40px rgba(6,12,28,0.2); }
    .modal-title { font-size:1.05rem; font-weight:800; margin-bottom:8px; }
    .modal-body { color:#475569; margin-bottom:18px; }
    .modal-actions { display:flex; gap:12px; justify-content:flex-end; }
</style>
@endpush

@section('content')

        {{-- Flash messages --}}
        @if(session('success'))
            <div class="flash flash-success">✓ {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="flash flash-error">✕ {{ session('error') }}</div>
        @endif
        {{-- removed damaged warning banner per business rules — returns handled separately --}}
        @if(session('item_update_reminder'))
            <div style="
                background:#fef3e2;border:1px solid #f0d08a;
                border-left:4px solid #d4870a;border-radius:8px;
                padding:14px 20px;margin-bottom:20px;
                display:flex;align-items:center;
                justify-content:space-between;gap:16px;
                font-size:0.88rem;color:#7a4d06;
            ">
                <div>
                    <strong>⚠ Action Required</strong> —
                    Please update expiry dates and unit prices based on the invoice and product labels from this delivery.
                </div>
                <a href="{{ route('owner.items.index') }}"
                   style="background:#d4870a;color:#fff;border-radius:6px;
                          padding:8px 18px;font-size:0.82rem;font-weight:600;
                          text-decoration:none;white-space:nowrap;">
                    Update Inventory →
                </a>
            </div>
        @endif

        {{-- Page header --}}
        <div class="page-header">
            <div class="page-eyebrow">Purchase Order</div>
            <div class="page-title">{{ $purchaseOrder->po_number }}</div>
            <div class="po-meta">
                @php
                    $statusClasses = ['Approved'=>'badge-approved','Pending'=>'badge-pending','Rejected'=>'badge-rejected','Received'=>'badge-received','received'=>'badge-received'];
                    $statusClass = $statusClasses[$purchaseOrder->status] ?? 'badge-pending';
                    $statusDots = ['Approved'=>'🟢','Pending'=>'🟡','Rejected'=>'🔴','Received'=>'🟢','received'=>'🟢'];
                @endphp
                <span class="badge-status {{ $statusClass }}">{{ $statusDots[$purchaseOrder->status] ?? '⚪' }} {{ ucfirst($purchaseOrder->status) }}</span>
                <span class="page-date">Ordered {{ optional($purchaseOrder->order_date)->format('M d, Y') ?? '—' }}</span>
                @if($purchaseOrder->status === 'Pending')
                    <a href="{{ route('owner.purchase-orders.edit', $purchaseOrder) }}" style="font-size:.8rem;font-weight:600;color:#3b82f6;text-decoration:none">✏ Edit Order</a>
                @endif
            </div>
        </div>

        {{-- Two-column grid --}}
        <div class="grid-2">

            {{-- ── LEFT COLUMN ── --}}
            <div>
                {{-- Order Summary card --}}
                <div class="card" style="margin-bottom:20px">
                    <div class="card-header">
                        <span class="card-title">Order Summary</span>
                        <span class="vendor-tag">✓ Vendor Details Attached</span>
                    </div>
                    <table class="items-table">
                        <thead>
                            <tr>
                                <th>Item Description</th>
                                <th>Supplier</th>
                                <th>Qty</th>
                                <th>Unit Price</th>
                                <th style="text-align:right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($purchaseOrder->orderItems && $purchaseOrder->orderItems->count() > 0)
                                @foreach($purchaseOrder->orderItems as $poItem)
                                <tr>
                                    <td>
                                        <div style="display:flex;align-items:center;gap:12px">
                                            <div class="item-thumb">📦</div>
                                            <div>
                                                <div class="item-name">{{ optional($poItem->item)->name ?? 'N/A' }}</div>
                                                <div class="item-sku">SKU: {{ optional($poItem->item)->sku ?? optional($poItem->item)->id ?? 'N/A' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="{{ route('owner.suppliers.show', $purchaseOrder->supplier_id) }}" class="supplier-link">
                                            {{ optional($purchaseOrder->supplier)->name ?? 'N/A' }}
                                        </a>
                                    </td>
                                    <td style="font-weight:700">{{ number_format($poItem->quantity) }}</td>
                                    <td>RM {{ number_format($poItem->unit_price ?? 0, 2) }}</td>
                                    <td style="text-align:right;font-weight:700">RM {{ number_format($poItem->subtotal ?? ($poItem->quantity * $poItem->unit_price), 2) }}</td>
                                </tr>
                                @endforeach
                            @else
                            <tr>
                                <td>
                                    <div style="display:flex;align-items:center;gap:12px">
                                        <div class="item-thumb">📦</div>
                                        <div>
                                            <div class="item-name">{{ optional($purchaseOrder->item)->name ?? 'N/A' }}</div>
                                            <div class="item-sku">SKU: {{ optional($purchaseOrder->item)->sku ?? optional($purchaseOrder->item)->id ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <a href="{{ route('owner.suppliers.show', $purchaseOrder->supplier_id) }}" class="supplier-link">
                                        {{ optional($purchaseOrder->supplier)->name ?? 'N/A' }}
                                    </a>
                                </td>
                                <td style="font-weight:700">{{ number_format($purchaseOrder->quantity) }}</td>
                                <td>RM {{ number_format($purchaseOrder->unit_price ?? 0, 2) }}</td>
                                <td style="text-align:right;font-weight:700">RM {{ number_format($purchaseOrder->total_amount ?? 0, 2) }}</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                    <div class="totals-section">
                        <div class="total-row">
                            <span>Total Amount</span>
                            <span>RM {{ number_format($purchaseOrder->total_amount ?? 0, 2) }}</span>
                        </div>
                    </div>
                </div>

                {{-- Notes --}}
                @if($purchaseOrder->notes)
                <div class="card" style="margin-bottom:20px;padding:16px 20px">
                    <div class="card-title" style="margin-bottom:8px">Order Notes</div>
                    <p style="font-size:.88rem;color:#374151;line-height:1.6;margin:0">{{ $purchaseOrder->notes }}</p>
                </div>
                @endif

                {{-- ── Delivery Checklist Form ── --}}
                @if($purchaseOrder->status === 'Approved' && $purchaseOrder->delivery && $purchaseOrder->delivery->delivery_date)
                <div class="card" style="margin-bottom:20px;border:2px solid #1d8348;">
                    <div class="card-header" style="background:#e8f8f0;border-bottom:1px solid #a9dfbf;">
                        <span class="card-title" style="color:#1d8348;">📦 Delivery Checklist</span>
                        <span style="font-size:.78rem;color:#5a6a85;">Delivery scheduled: {{ $purchaseOrder->delivery->delivery_date->format('d M Y') }}</span>
                    </div>
                    <div style="padding:20px 24px;">
                        <form id="receiveForm" method="POST" action="{{ route('owner.purchase-orders.markAsReceived', $purchaseOrder) }}">
                            @csrf
                            @method('PATCH')
                            <p style="font-size:.9rem;color:#5a6a85;margin-bottom:16px;">Verify the items received from the supplier. Record any damaged goods and the expiry dates found on the packaging.</p>

                            <table style="width:100%; border-collapse:collapse; margin-bottom: 20px;">
                                <thead>
                                    <tr style="border-bottom: 1px solid #e2e8f0;">
                                        <th style="padding:10px 0; text-align:left; font-size:.8rem; color:#5a6a85;">Item</th>
                                        <th style="padding:10px; text-align:center; font-size:.8rem; color:#5a6a85;">Ordered</th>
                                        <th style="padding:10px; text-align:center; font-size:.8rem; color:#5a6a85;">Received</th>
                                        <th style="padding:10px; text-align:center; font-size:.8rem; color:#5a6a85;">Damaged</th>
                                        <th style="padding:10px; text-align:center; font-size:.8rem; color:#5a6a85;">Good</th>
                                        <th style="padding:10px; text-align:left; font-size:.8rem; color:#5a6a85;">Expiry Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($purchaseOrder->orderItems as $poItem)
                                    <tr style="border-bottom: 1px solid #f0f4f8;">
                                        <td style="padding:12px 0; font-size:.88rem; font-weight:600;">{{ optional($poItem->item)->name ?? 'N/A' }}</td>
                                        <td style="padding:12px; text-align:center; font-size:.88rem;">{{ $poItem->quantity }}</td>
                                        <td style="padding:12px; text-align:center;">
                                            <input type="number" name="items[{{ $poItem->id }}][received_quantity]" min="0" value="{{ $poItem->quantity }}" style="width:70px; padding:6px; border:1px solid #d1dce8; border-radius:6px; text-align:center;" oninput="updateGoodQty({{ $poItem->id }})">
                                        </td>
                                        <td style="padding:12px; text-align:center;">
                                            <input type="number" name="items[{{ $poItem->id }}][damaged_quantity]" min="0" value="0" style="width:70px; padding:6px; border:1px solid #d1dce8; border-radius:6px; text-align:center;" oninput="updateGoodQty({{ $poItem->id }})">
                                        </td>
                                        <td style="padding:12px; text-align:center; font-weight:700; color:#1d8348;" id="goodQty_{{ $poItem->id }}">
                                            {{ $poItem->quantity }}
                                        </td>
                                        <td style="padding:12px; text-align:left;">
                                            <input type="date" name="items[{{ $poItem->id }}][expiry_date]" style="padding:6px; border:1px solid #d1dce8; border-radius:6px;">
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <button type="submit" id="confirmReceiveBtn"
                                    style="background:#1d8348;color:#fff;border:none;border-radius:8px;padding:12px 28px;font-size:.9rem;font-weight:700;cursor:pointer;font-family:inherit;transition:background .15s; width:100%;"
                                    onmouseover="this.style.background='#155d36'" onmouseout="this.style.background='#1d8348'">
                                ✅ Confirm Receipt & Generate Invoice
                            </button>
                        </form>
                    </div>
                </div>
                @elseif(in_array(strtolower($purchaseOrder->status), ['received', 'delivered']))
                <div class="card" style="margin-bottom:20px;border:2px solid #a9dfbf;background:#e8f8f0;padding:16px 20px;">
                    <span style="font-size:.9rem;font-weight:700;color:#1d8348;">🟢 This purchase order has been marked as Received.</span>
                </div>
                @endif

                {{-- Damaged items/history removed per updated business rules --}}
            </div>

            {{-- ── RIGHT COLUMN ── --}}
            <div style="display:flex;flex-direction:column;gap:16px">

                {{-- Delivery Card --}}
                <div class="card-navy">
                    <div class="delivery-label">Scheduled Delivery</div>
                    @if($purchaseOrder->delivery && $purchaseOrder->delivery->delivery_date)
                        <div class="delivery-date">{{ $purchaseOrder->delivery->delivery_date->format('d M Y') }}</div>
                        <div class="delivery-time">🕐 {{ $purchaseOrder->delivery->delivery_time ? \Carbon\Carbon::parse($purchaseOrder->delivery->delivery_time)->format('g:i A') : 'Time TBD' }}</div>
                        <hr class="delivery-divider">
                        <div class="driver-row">
                            <div class="driver-avatar">🚚</div>
                            <div>
                                <div class="driver-name">{{ optional($purchaseOrder->supplier)->name ?? 'Supplier' }}</div>
                                <div class="driver-sub">Assigned Supplier</div>
                            </div>
                        </div>
                        @if($purchaseOrder->delivery->notes)
                            <div style="margin-top:12px;font-size:.78rem;color:#7a95b5;line-height:1.5">📝 {{ $purchaseOrder->delivery->notes }}</div>
                        @endif
                    @else
                        <div class="delivery-date" style="font-size:1rem;color:#fff;opacity:0.9">Not Scheduled Yet</div>
                        <div class="delivery-time" style="color:#7a95b5">Pending supplier confirmation</div>
                        <hr class="delivery-divider">
                        <div class="driver-row">
                            <div class="driver-avatar" style="background:rgba(255,255,255,0.1)">?</div>
                            <div>
                                <div class="driver-name" style="color:#fff;opacity:0.9">Awaiting Assignment</div>
                                <div class="driver-sub" style="color:#7a95b5">Supplier will confirm</div>
                            </div>
                        </div>
                    @endif
                </div>



                {{-- Purchase Order Information (Audit) --}}
                <div class="card">
                    <div class="card-header">
                        <span class="card-title">ℹ️ Purchase Order Information</span>
                    </div>
                    <div style="padding:16px 20px">
                        @php
                            $creator = \App\Models\User::find($purchaseOrder->created_by);
                            $updater = $purchaseOrder->updated_by ? \App\Models\User::find($purchaseOrder->updated_by) : null;
                            $creatorText = $creator ? $creator->name . ' (' . ucfirst($creator->role) . ')' : 'System';
                            $updaterText = $updater ? $updater->name . ' (' . ucfirst($updater->role) . ')' : ($creator ? $creatorText : 'System');
                        @endphp
                        <div style="display:flex; flex-direction:column; gap:12px;">
                            <div>
                                <div style="font-size:0.75rem; color:#7a8fa8; text-transform:uppercase; font-weight:700;">Created By</div>
                                <div style="font-size:0.88rem; color:#0D1B2A; font-weight:500;">{{ $creatorText }}</div>
                            </div>
                            <div>
                                <div style="font-size:0.75rem; color:#7a8fa8; text-transform:uppercase; font-weight:700;">Created At</div>
                                <div style="font-size:0.88rem; color:#0D1B2A; font-weight:500;">{{ $purchaseOrder->created_at ? $purchaseOrder->created_at->format('d M Y, g:i A') : '—' }}</div>
                            </div>
                            <div>
                                <div style="font-size:0.75rem; color:#7a8fa8; text-transform:uppercase; font-weight:700;">Last Updated By</div>
                                <div style="font-size:0.88rem; color:#0D1B2A; font-weight:500;">{{ $updaterText }}</div>
                            </div>
                            <div>
                                <div style="font-size:0.75rem; color:#7a8fa8; text-transform:uppercase; font-weight:700;">Last Updated At</div>
                                <div style="font-size:0.88rem; color:#0D1B2A; font-weight:500;">{{ $purchaseOrder->updated_at ? $purchaseOrder->updated_at->format('d M Y, g:i A') : '—' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Invoice Card: only show invoice when goods have been received and invoice exists --}}
                <div class="card">
                    <div class="card-header">
                        <span class="card-title">📄 Invoice</span>
                    </div>
                    <div class="invoice-card-inner" id="invoiceCardInner">
                                @if(in_array(strtolower($purchaseOrder->status), ['received', 'delivered']) && $purchaseOrder->invoice)
                            <div style="display:flex;flex-direction:column;gap:12px;">
                                <div class="invoice-doc-icon">🧾</div>
                                <div class="invoice-num">{{ $purchaseOrder->invoice->invoice_number }}</div>
                                <div class="invoice-sub">Invoice Date: {{ $purchaseOrder->invoice->invoice_date?->format('M d, Y') ?? 'N/A' }}</div>
                                <div class="invoice-sub">Due Date: {{ $purchaseOrder->invoice->payment_due_date?->format('M d, Y') ?? 'N/A' }}</div>
                                <div class="invoice-sub">Total: RM {{ number_format($purchaseOrder->invoice->total_amount ?? $purchaseOrder->total_amount ?? 0, 2) }}</div>
                                <div class="invoice-sub">Status: {{ ucfirst($purchaseOrder->invoice->status ?? 'unpaid') }}</div>
                                <div style="display:flex;gap:12px;flex-wrap:wrap; margin-top:8px;">
                                    <button onclick="openInvoiceModal()" class="btn btn-primary" style="padding:10px 16px;">View Invoice</button>
                                    <a href="{{ route('owner.invoices.export-pdf', $purchaseOrder->invoice) }}" class="btn btn-secondary" style="padding:10px 16px; text-decoration:none;">Export PDF</a>
                                </div>
                            </div>
                        @else
                            <div style="display:flex;gap:12px;align-items:center">
                                <div class="invoice-doc-icon" style="opacity:.6">📄</div>
                                <div>
                                    <div class="invoice-num" style="color:#6b7280">Invoice will be generated after goods are received.</div>
                                    <div class="invoice-sub">An invoice is created when the owner marks this delivery as received.</div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>


@endsection





    {{-- Inventory Update Reminder Modal (shown after receive) --}}
    @if(session('item_update_reminder'))
    <div class="modal-overlay" id="inventoryReminderModal">
        <div class="modal-box">
            <div class="modal-title">Inventory Update Reminder</div>
            <div class="modal-body">Items received successfully. Please update the item expiry dates and inventory details if necessary.</div>
            <div class="modal-actions">
                <a href="{{ route('owner.items.index') }}" class="btn btn-primary">Update Inventory</a>
                <button class="btn btn-secondary" id="reminderLaterBtn">Later</button>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('inventoryReminderModal');
            if (modal) {
                modal.classList.add('open');
                document.getElementById('reminderLaterBtn')?.addEventListener('click', function() {
                    modal.classList.remove('open');
                });
            }
        });
    </script>
    @endif

{{-- ── Invoice Modal ── --}}
@if($purchaseOrder->invoice)
<div class="modal-overlay" id="invoiceModal">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-title">Invoice — {{ $purchaseOrder->invoice->invoice_number }}</div>
            <button class="modal-close" onclick="closeInvoiceModal()">✕</button>
        </div>
        <div class="modal-body">
            <div class="inv-company">22UniMart</div>
            <div class="inv-tagline">Inventory Management System · Kuala Lumpur, Malaysia</div>

            <div class="inv-grid">
                <div>
                    <div class="inv-section-label">Supplier (From)</div>
                    <div class="inv-val"><strong>{{ optional($purchaseOrder->supplier)->name ?? 'N/A' }}</strong></div>
                    <div class="inv-val">{{ optional($purchaseOrder->supplier)->contact_email ?? '—' }}</div>
                    <div class="inv-val">{{ optional($purchaseOrder->supplier)->contact_phone ?? '—' }}</div>
                </div>
                <div>
                    <div class="inv-section-label">Buyer (Bill To)</div>
                    <div class="inv-val"><strong>{{ auth()->user()->name }}</strong></div>
                    <div class="inv-val">{{ auth()->user()->email }}</div>
                    <div class="inv-val">22UniMart, Owner</div>
                </div>
            </div>

            <div class="inv-grid" style="margin-bottom:0">
                <div>
                    <div class="inv-section-label">Invoice Number</div>
                    <div class="inv-val"><strong>{{ $purchaseOrder->invoice->invoice_number }}</strong></div>
                </div>
                <div>
                    <div class="inv-section-label">PO Reference</div>
                    <div class="inv-val"><strong>{{ $purchaseOrder->po_number }}</strong></div>
                </div>
                <div>
                    <div class="inv-section-label">Invoice Date</div>
                    <div class="inv-val">{{ $purchaseOrder->invoice->invoice_date?->format('M d, Y') ?? '—' }}</div>
                </div>
                <div>
                    <div class="inv-section-label">Status</div>
                    <span class="inv-badge inv-badge-approved">{{ $purchaseOrder->status }}</span>
                </div>
            </div>

            <table class="inv-table">
                <thead>
                    <tr>
                        <th>Item Description</th>
                        <th>Qty</th>
                        <th>Unit Price</th>
                        <th style="text-align:right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @if($purchaseOrder->invoice->lines && $purchaseOrder->invoice->lines->count() > 0)
                        @foreach($purchaseOrder->invoice->lines as $line)
                        <tr>
                            <td>{{ optional($line->item)->name ?? 'N/A' }}</td>
                            <td>{{ number_format($line->quantity) }}</td>
                            <td>RM {{ number_format($line->unit_price ?? 0, 2) }}</td>
                            <td style="text-align:right;font-weight:700">RM {{ number_format($line->invoice_line_total ?? 0, 2) }}</td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td>{{ optional($purchaseOrder->item)->name ?? 'N/A' }}</td>
                            <td>{{ number_format($purchaseOrder->quantity) }}</td>
                            <td>RM {{ number_format($purchaseOrder->unit_price ?? 0, 2) }}</td>
                            <td style="text-align:right;font-weight:700">RM {{ number_format($purchaseOrder->total_amount ?? 0, 2) }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>

            <div class="inv-totals">
                    <div class="inv-total-row">
                        <span>Total Amount</span>
                        <span>RM {{ number_format($purchaseOrder->invoice->total_amount ?? $purchaseOrder->total_amount ?? 0, 2) }}</span>
                    </div>
                </div>
            <div style="margin-top:16px;padding:12px;background:#f8fafc;border-radius:8px;font-size:.78rem;color:#6b7280">
                This is a system-generated invoice for record-keeping and sharing with the supplier. It is read-only and cannot be edited.
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-modal-close" onclick="closeInvoiceModal()">Close</button>
            <a href="{{ route('owner.invoices.export-pdf', $purchaseOrder->invoice) }}" class="btn-export-pdf">⬇ Export to PDF</a>
        </div>
    </div>
</div>
@endif

<script>
function openInvoiceModal() {
    document.getElementById('invoiceModal').classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeInvoiceModal() {
    document.getElementById('invoiceModal').classList.remove('open');
    document.body.style.overflow = '';
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeInvoiceModal();
});
document.getElementById('invoiceModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeInvoiceModal();
});
</script>

<script>
function updateGoodQty(id) {
    const receivedInput = document.querySelector(`input[name="items[${id}][received_quantity]"]`);
    const damagedInput = document.querySelector(`input[name="items[${id}][damaged_quantity]"]`);
    if (!receivedInput || !damagedInput) return;
    
    const received = parseInt(receivedInput.value) || 0;
    const damaged = parseInt(damagedInput.value) || 0;
    const goodCell = document.getElementById(`goodQty_${id}`);
    const good = Math.max(0, received - damaged);
    goodCell.textContent = good;
}
</script>

@if(($purchaseOrder->status === 'Approved' || in_array(strtolower($purchaseOrder->status), ['received', 'delivered'])) && !$purchaseOrder->invoice)
<script>
    const pollInterval = setInterval(() => {
        fetch('{{ route('owner.purchase-orders.invoice-status', $purchaseOrder) }}')
            .then(r => r.json())
            .then(data => {
                if(data.status === 'ready') {
                    clearInterval(pollInterval);
                    window.location.reload();
                }
            })
            .catch(err => console.error('Error polling invoice status:', err));
    }, 3000);
</script>
<style>
@keyframes pulse {
    0% { transform: scale(1); opacity: 0.6; }
    50% { transform: scale(1.1); opacity: 1; }
    100% { transform: scale(1); opacity: 0.6; }
}
</style>
@endif
