<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create Return Request – 22UniMart</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'Inter',sans-serif;background:#eef2f7;display:flex;min-height:100vh;color:#1a2744}
        .sidebar{width:210px;flex-shrink:0;background:#0f2044;color:#fff;display:flex;flex-direction:column;padding:0 0 24px;position:fixed;top:0;left:0;height:100vh}
        .sidebar-brand{padding:20px 20px 4px;display:flex;align-items:center;gap:12px}
        .brand-sq{width:32px;height:32px;background:#fff;color:#0f2044;border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:.9rem;font-weight:800;flex-shrink:0}
        .brand-name{font-size:.9rem;font-weight:800}
        .brand-sub{font-size:.68rem;color:#8ca0c0}
        .sidebar-nav{flex:1;margin-top:20px;overflow-y:auto}
        .nav-item{display:flex;align-items:center;gap:12px;padding:11px 20px;font-size:.88rem;font-weight:500;color:#8ca0c0;text-decoration:none;transition:all .15s;border-left:3px solid transparent}
        .nav-item:hover{color:#fff;background:rgba(255,255,255,.06)}
        .nav-item.active{color:#fff;background:rgba(255,255,255,.1);border-left-color:#4a90d9}
        .nav-icon{width:18px;text-align:center;flex-shrink:0}
        .sidebar-bottom{padding:0 20px;display:flex;flex-direction:column;gap:8px}
        .btn-logout{background:#c0392b;color:#fff;border:none;border-radius:8px;padding:12px 16px;font-size:.85rem;font-weight:600;cursor:pointer;width:100%;transition:background .15s}
        .btn-logout:hover{background:#a93226}
        .main{margin-left:210px;flex:1;display:flex;flex-direction:column}
        .topbar{background:#fff;padding:0 32px;height:56px;display:flex;align-items:center;gap:16px;position:sticky;top:0;z-index:10;border-bottom:1px solid #e2e8f0}
        .breadcrumbs{font-size:.75rem;font-weight:700;color:#7a8fa8;letter-spacing:1px;text-transform:uppercase}
        .breadcrumbs a{color:inherit;text-decoration:none}
        .breadcrumbs a:hover{color:#0f2044}
        .topbar-right{margin-left:auto;display:flex;align-items:center;gap:20px}
        .avatar{width:32px;height:32px;border-radius:50%;background:#0f2044;display:flex;align-items:center;justify-content:center;color:#fff;font-size:.75rem;font-weight:700}
        .content{padding:40px;max-width:960px}
        .page-title{font-size:2rem;font-weight:800;color:#0f2044;margin-bottom:6px}
        .page-sub{font-size:.93rem;color:#5a6a85;line-height:1.6;margin-bottom:28px;max-width:760px}
        /* Context Banner */
        .context-banner{display:flex;align-items:center;gap:16px;background:#fff3cd;border:1px solid #ffc107;border-left:4px solid #d4870a;border-radius:10px;padding:14px 20px;margin-bottom:24px}
        .context-banner .cb-icon{font-size:1.4rem}
        .context-banner .cb-text{flex:1;font-size:.88rem;color:#7a4d06}
        .context-banner .cb-text strong{color:#5a3500;display:block;margin-bottom:2px}
        /* Empty banner */
        .empty-banner{background:#f8f9fc;border:1.5px dashed #cbd5e1;border-radius:10px;padding:28px;text-align:center;color:#9daec5;font-size:.9rem;margin-top:8px}
        .empty-banner .eb-icon{font-size:2rem;margin-bottom:8px}
        /* Cards */
        .form-card{background:#fff;border-radius:14px;padding:28px 32px;box-shadow:0 1px 20px rgba(15,32,68,.07);margin-bottom:20px}
        .card-title{font-size:1rem;font-weight:700;color:#0f2044;margin-bottom:18px;display:flex;align-items:center;gap:8px}
        .form-row{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px;margin-bottom:16px}
        .form-group{display:flex;flex-direction:column;gap:6px}
        .form-group.span-2{grid-column:span 2}
        .form-label{font-size:.72rem;font-weight:700;color:#5a6a85;text-transform:uppercase;letter-spacing:.5px}
        .form-label .req{color:#e74c3c}
        .form-control,.form-select{background:#f4f6fb;border:1.5px solid transparent;border-radius:9px;padding:12px 14px;font-size:.93rem;color:#1a2744;transition:all .2s;outline:none;font-family:'Inter',sans-serif;width:100%}
        .form-control:focus,.form-select:focus{background:#fff;border-color:#4a90d9;box-shadow:0 0 0 3px rgba(74,144,217,.12)}
        .form-control::placeholder{color:#9daec5}
        .form-control:disabled,.form-select:disabled{opacity:.6;cursor:not-allowed}
        /* Invoice preview strip */
        .inv-preview{display:flex;gap:20px;flex-wrap:wrap;background:#f0f7ff;border:1px solid #bfdbfe;border-radius:9px;padding:12px 16px;margin-top:12px;font-size:.84rem;color:#3a4d6a}
        .inv-preview span{font-weight:600;color:#0f2044}
        /* Table */
        .table-wrap{overflow-x:auto;margin-top:6px}
        table.items-table{width:100%;border-collapse:collapse}
        table.items-table thead th{padding:10px 14px;background:#f8fafc;font-size:.68rem;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:#7a8fa8;border-bottom:2px solid #e8edf4;white-space:nowrap}
        table.items-table thead th:last-child{text-align:right}
        table.items-table tbody td{padding:12px 14px;border-bottom:1px solid #f0f4f8;font-size:.9rem;color:#1a2744;vertical-align:middle}
        table.items-table tbody tr:last-child td{border-bottom:none}
        table.items-table tfoot td{padding:12px 14px;background:#f8fafc;font-weight:700;font-size:.93rem}
        .total-credit-row{display:flex;justify-content:flex-end;align-items:center;gap:16px;padding:14px 0 2px;border-top:2px solid #e8edf4;margin-top:8px}
        .total-credit-label{font-size:.8rem;font-weight:700;color:#5a6a85;text-transform:uppercase;letter-spacing:.5px}
        .total-credit-value{font-size:1.35rem;font-weight:800;color:#0f2044}
        /* Qty input */
        .qty-input{width:90px;border:1.5px solid #d1dce8;border-radius:7px;padding:7px 10px;font-family:'Inter',sans-serif;font-size:.9rem;color:#1a2744;outline:none;text-align:center;transition:border-color .15s}
        .qty-input:focus{border-color:#4a90d9;box-shadow:0 0 0 2px rgba(74,144,217,.12)}
        .qty-input.error{border-color:#e74c3c}
        /* Actions */
        .form-actions{display:flex;flex-wrap:wrap;gap:14px;align-items:center;margin-top:8px}
        .btn-submit{background:#0f2044;color:#fff;border:none;border-radius:9px;padding:13px 26px;font-size:.93rem;font-weight:700;cursor:pointer;transition:background .15s;font-family:'Inter',sans-serif}
        .btn-submit:hover:not(:disabled){background:#122a50}
        .btn-submit:disabled{opacity:.45;cursor:not-allowed}
        .btn-discard{color:#5a6a85;text-decoration:none;font-weight:600;font-size:.9rem}
        /* Alerts */
        .alert{padding:12px 18px;border-radius:9px;font-size:.88rem;margin-bottom:18px}
        .alert-error{background:#fdedec;color:#c0392b;border:1px solid #f5b7b1}
        .alert-error li{margin-left:16px;margin-top:4px}
        /* Badge */
        .badge-expired{display:inline-flex;align-items:center;gap:5px;background:#fdedec;color:#c0392b;border:1px solid #f5b7b1;border-radius:20px;padding:3px 10px;font-size:.72rem;font-weight:700}
    </style>
</head>
<body>

<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="brand-sq">22</div>
        <div>
            <div class="brand-name">22UNIMART</div>
            <div class="brand-sub">Inventory Control</div>
        </div>
    </div>
    <nav class="sidebar-nav">
        <a href="{{ route('owner.dashboard') }}" class="nav-item {{ request()->routeIs('owner.dashboard') ? 'active' : '' }}"><span class="nav-icon">⊞</span> Dashboard</a>
        <a href="{{ route('owner.items.index') }}" class="nav-item {{ request()->routeIs('owner.items.*') ? 'active' : '' }}"><span class="nav-icon">📦</span> Inventory</a>
        <a href="{{ route('owner.suppliers.index') }}" class="nav-item {{ request()->routeIs('owner.suppliers.*') ? 'active' : '' }}"><span class="nav-icon">🏢</span> Suppliers</a>
        <a href="{{ route('owner.purchase-orders.index') }}" class="nav-item {{ request()->routeIs('owner.purchase-orders.*') ? 'active' : '' }}"><span class="nav-icon">🛒</span> Purchase Orders</a>
        <a href="{{ route('owner.return-requests.index') }}" class="nav-item active"><span class="nav-icon">↩</span> Return Requests</a>
        <a href="{{ route('owner.credit-notes.index') }}" class="nav-item {{ request()->routeIs('owner.credit-notes.*') ? 'active' : '' }}"><span class="nav-icon">📋</span> Credit Notes</a>
        <a href="{{ route('owner.invoices.index') }}" class="nav-item {{ request()->routeIs('owner.invoices.*') ? 'active' : '' }}"><span class="nav-icon">📄</span> Invoices</a>
        <a href="{{ route('owner.notifications.index') }}" class="nav-item {{ request()->routeIs('owner.notifications.*') ? 'active' : '' }}"><span class="nav-icon">🔔</span> Notifications</a>
    </nav>
    <div class="sidebar-bottom">
        <div style="color:#fff;font-weight:bold;font-size:.82rem;padding:6px 0">Role: Owner</div>
        <form action="{{ route('logout') }}" method="POST" style="margin:0;width:100%">
            @csrf
            <button type="submit" class="btn-logout">Logout</button>
        </form>
    </div>
</aside>

<div class="main">
    <div class="topbar">
        <div class="breadcrumbs"><a href="{{ route('owner.return-requests.index') }}">Return Requests</a> › <span style="color:#0f2044">Create</span></div>
        <div class="topbar-right">
            <a href="{{ route('owner.notifications.index') }}" style="text-decoration:none;font-size:1.1rem;color:#5a6a85">🔔</a>
            <div style="display:flex;align-items:center;gap:8px">
                <div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <div>
                    <div style="font-size:.85rem;font-weight:600;color:#1a2744">{{ auth()->user()->name }}</div>
                    <form action="{{ route('logout') }}" method="POST" style="display:inline">@csrf
                        <button type="submit" style="background:none;border:none;font-size:.72rem;color:#9daec5;cursor:pointer;font-family:inherit;padding:0">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="page-title">Create Return Request</div>
        <div class="page-sub">Submit a return request for expired or damaged inventory. An invoice is required to calculate the credit amount based on the original purchase price.</div>

        {{-- Context banner when launched from expired inventory --}}
        @if($preselectedItem)
        <div class="context-banner">
            <div class="cb-icon">⚠️</div>
            <div class="cb-text">
                <strong>Auto-filled from return-eligible inventory</strong>
                {{ $preselectedItem->name }} ({{ optional($preselectedItem->supplier)->name ?? 'Unknown Supplier' }}) — Remaining Qty: {{ $preselectedItem->quantity }}
                @if($preselectedItem->isExpired())
                    &nbsp;<span class="badge-expired">Expired {{ $preselectedItem->expiry_date?->format('d M Y') }}</span>
                @elseif($preselectedItem->isDamaged())
                    &nbsp;<span class="badge-expired">Damaged ({{ max(0, (int) $preselectedItem->damaged_quantity) }} units) {{ $preselectedItem->damage_reason ? '(' . $preselectedItem->damage_reason . ')' : '' }}</span>
                @endif
            </div>
            <a href="{{ route('owner.return-requests.create') }}" style="font-size:.8rem;color:#5a6a85;text-decoration:none;white-space:nowrap">✕ Clear</a>
        </div>
        @endif

        @if($errors->any())
        <div class="alert alert-error">
            <strong>Please fix the following errors:</strong>
            <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif

        {{-- No invoices available --}}
        @if($invoices->isEmpty())
        <div class="form-card">
            <div class="empty-banner">
                <div class="eb-icon">📄</div>
                <div>
                    @if($preselectedItem)
                        <strong>No related invoice found for this item.</strong><br>
                        A received purchase order invoice is required before a return request can be created for <em>{{ $preselectedItem->name }}</em>.
                    @else
                        <strong>No invoices available.</strong><br>
                        Invoices are created automatically when a purchase order is marked as received.
                    @endif
                </div>
            </div>
        </div>
        @else

        @php
            $mappedInvoices = $invoices->map(function ($invoice) {
                return [
                    'id'              => $invoice->id,
                    'invoice_number'  => $invoice->invoice_number,
                    'supplier_name'   => optional($invoice->supplier)->name,
                    'invoice_date'    => optional($invoice->invoice_date)?->format('d M Y'),
                    'total_amount'    => number_format($invoice->total_amount, 2, '.', ''),
                    'lines'           => $invoice->lines->map(function ($line) {
                        $item = is_object($line) ? $line->item : (object)($line['item'] ?? []);
                        $quantity = is_object($line) ? (int) $line->quantity : (int) ($line['quantity'] ?? 0);
                        $currentQuantity = (int) ($item->quantity ?? 0);
                        $damagedQuantity = (int) ($item->damaged_quantity ?? 0);
                        $isExpired = is_object($item) && method_exists($item, 'isExpired') ? $item->isExpired() : false;
                        $isDamaged = is_object($item) && method_exists($item, 'isDamaged') ? $item->isDamaged() : false;
                        $returnableQuantity = min($quantity, $currentQuantity);
                        if ($isDamaged) {
                            $returnableQuantity = min($returnableQuantity, $damagedQuantity);
                        }
                        return [
                            'invoice_line_id' => is_object($line) ? $line->id : ($line['id'] ?? ''),
                            'item_id'         => is_object($line) ? $line->item_id : ($line['item_id'] ?? ''),
                            'item_name'       => $item->name ?? 'Unknown',
                            'quantity'        => $quantity,
                            'current_quantity'=> $currentQuantity,
                            'damaged_quantity'=> $damagedQuantity,
                            'returnable_qty'  => $returnableQuantity,
                            'is_expired'      => $isExpired,
                            'is_damaged'      => $isDamaged,
                            'eligible'        => ($isExpired || $isDamaged) && $returnableQuantity > 0,
                            'damage_reason'   => $item->damage_reason ?? '',
                            'unit_price'      => is_object($line) ? number_format($line->unit_price, 2, '.', '') : number_format($line['unit_price'] ?? 0, 2, '.', ''),
                            'uom'             => is_object($line) ? ($line->uom ?? 'unit') : ($line['uom'] ?? 'unit'),
                        ];
                    })->values()->toArray(),
                ];
            });

            $preselectedInvoiceId = $preselectedInvoice?->id ?? old('invoice_id', '');
            $preselectedItemId    = $preselectedItem?->id ?? '';
            $preselectedQty       = $preselectedQuantity ?? ($preselectedItem?->damaged_quantity ?? $preselectedItem?->quantity ?? 0);
        @endphp

        <form id="returnForm" method="POST" action="{{ route('owner.return-requests.store') }}">
            @csrf

            {{-- ── Section 1: Invoice & Reason ── --}}
            <div class="form-card">
                <div class="card-title">📄 Invoice & Return Details</div>
                <div class="form-row">
                    <div class="form-group span-2">
                        <label class="form-label">Search Invoice <span class="req">*</span></label>
                        <input type="text" id="invoice_search" class="form-control" placeholder="Search invoice number, supplier, or date…" oninput="filterInvoices()" {{ $preselectedItem ? 'value='.$invoices->where('id', $preselectedInvoiceId)->first()?->invoice_number ?? '' : '' }}>
                    </div>

                    <div class="form-group span-2">
                        <label class="form-label">Invoice Reference <span class="req">*</span></label>
                        <select name="invoice_id" id="invoice_id" class="form-select" required onchange="onInvoiceChange()">
                            <option value="">— Select an invoice —</option>
                            @foreach($invoices as $invoice)
                            <option value="{{ $invoice->id }}"
                                data-supplier="{{ optional($invoice->supplier)->name }}"
                                data-date="{{ optional($invoice->invoice_date)?->format('d M Y') }}"
                                data-amount="{{ number_format($invoice->total_amount, 2) }}"
                                @selected(old('invoice_id', $preselectedInvoiceId) == $invoice->id)>
                                {{ $invoice->invoice_number }} | {{ optional($invoice->supplier)->name ?? 'Unknown' }} | {{ optional($invoice->invoice_date)?->format('d M Y') ?? '—' }} | RM{{ number_format($invoice->total_amount, 2) }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Invoice preview strip --}}
                <div id="invPreview" class="inv-preview" style="display:none">
                    <div>Invoice: <span id="prev_inv_num">—</span></div>
                    <div>Supplier: <span id="prev_supplier">—</span></div>
                    <div>Date: <span id="prev_date">—</span></div>
                    <div>Total: RM <span id="prev_amount">—</span></div>
                </div>

                <div class="form-row" style="margin-top:16px">
                    <div class="form-group span-2">
                        <label class="form-label">Additional Notes <span style="color:#9daec5;font-weight:400">(optional)</span></label>
                        <input type="text" name="notes" class="form-control" placeholder="e.g. product label shows expiry passed" value="{{ old('notes') }}">
                    </div>
                </div>
                <div style="margin-top:8px;font-size:.82rem;color:#5a6a85;line-height:1.5;">
                    Return reason is selected per line item below, so you can mix expired and damaged items within one request.
                </div>
            </div>

            {{-- ── Section 2: Return Items Table ── --}}
            <div class="form-card">
                <div class="card-title">📦 Return Items</div>

                <div id="tableContainer">
                    <div class="empty-banner" id="emptyState">
                        <div class="eb-icon">🧾</div>
                        <div>Select an invoice above to populate the return items table.</div>
                    </div>
                </div>

                <div class="total-credit-row" id="totalCreditRow" style="display:none">
                    <div class="total-credit-label">Total Credit Amount</div>
                    <div class="total-credit-value">RM <span id="totalCredit">0.00</span></div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" id="submitBtn" class="btn-submit" disabled>Create Return Request</button>
                <a href="{{ route('owner.return-requests.index') }}" class="btn-discard">Discard Draft</a>
            </div>
        </form>
        @endif
    </div>
</div>

<script>
const ALL_INVOICES = @json($mappedInvoices ?? collect([]));
const PRESELECTED_ITEM_ID = {{ $preselectedItemId ?? 'null' }};
const PRESELECTED_QTY     = {{ $preselectedQty ?? 0 }};

function fmt(v) { return parseFloat(v||0).toFixed(2); }

function filterInvoices() {
    const q   = document.getElementById('invoice_search').value.toLowerCase();
    const sel = document.getElementById('invoice_id');
    const all = Array.from(sel.options);
    sel.innerHTML = '';
    all.forEach(opt => {
        if (!opt.value || opt.textContent.toLowerCase().includes(q)) {
            sel.appendChild(opt);
        }
    });
}

function onInvoiceChange() {
    const id  = document.getElementById('invoice_id').value;
    const inv = ALL_INVOICES.find(i => String(i.id) === String(id));

    const preview = document.getElementById('invPreview');
    if (inv) {
        document.getElementById('prev_inv_num').textContent  = inv.invoice_number;
        document.getElementById('prev_supplier').textContent = inv.supplier_name || '—';
        document.getElementById('prev_date').textContent     = inv.invoice_date   || '—';
        document.getElementById('prev_amount').textContent   = inv.total_amount   || '—';
        preview.style.display = 'flex';
    } else {
        preview.style.display = 'none';
    }

    renderTable(inv);
    checkSubmitReady();
}

function renderTable(inv) {
    const container = document.getElementById('tableContainer');
    const totalRow  = document.getElementById('totalCreditRow');

    if (!inv || !inv.lines || inv.lines.length === 0) {
        container.innerHTML = `<div class="empty-banner" id="emptyState">
            <div class="eb-icon">📄</div>
            <div>${!inv ? 'Select an invoice above to populate the return items table.' : '<strong>No line items found for this invoice.</strong>'}</div>
        </div>`;
        totalRow.style.display = 'none';
        return;
    }

    let html = `<div class="table-wrap"><table class="items-table">
        <thead><tr>
            <th style="text-align:left">Item</th>
            <th style="text-align:right">Remaining Qty</th>
            <th style="text-align:center">Return Qty</th>
            <th style="text-align:center">Reason</th>
            <th style="text-align:right">Unit Price (RM)</th>
            <th style="text-align:right">Subtotal (RM)</th>
        </tr></thead>
        <tbody id="linesBody">`;

    inv.lines.forEach((line, idx) => {
        const returnableQty = parseInt(line.returnable_qty || 0);
        const eligible = !!line.eligible;
        const defaultQty = (PRESELECTED_ITEM_ID && String(line.item_id) === String(PRESELECTED_ITEM_ID))
            ? Math.min(parseInt(PRESELECTED_QTY || 0), returnableQty)
            : 0;
        const defaultReason = line.is_damaged ? 'damaged' : (line.is_expired ? 'expired' : '');
        const statusText = eligible
            ? [line.is_expired ? 'Expired' : null, line.is_damaged ? `Damaged (${line.damaged_quantity || 0} units)` : null].filter(Boolean).join(' / ')
            : 'Not expired or damaged';
        const disabled = eligible ? '' : 'disabled';

        html += `<tr style="${eligible ? '' : 'opacity:.55'}">
            <td>
                <input type="hidden" name="items[${idx}][invoice_line_id]" value="${line.invoice_line_id}">
                <strong>${line.item_name || 'Unknown Item'}</strong>
                <div style="font-size:.75rem;color:#9daec5">${line.uom} · ${statusText}</div>
            </td>
            <td style="text-align:right;font-weight:600">${returnableQty}</td>
            <td style="text-align:center">
                <input type="number" name="items[${idx}][quantity]"
                    class="qty-input" id="qty_${idx}"
                    value="${eligible ? defaultQty : 0}" min="0" max="${returnableQty}"
                    data-unit-price="${line.unit_price}"
                    data-idx="${idx}"
                    ${disabled}
                    oninput="updateRow(${idx}); checkSubmitReady()">
                <div style="font-size:.68rem;color:#9daec5;margin-top:3px">${eligible ? `max ${returnableQty}` : 'not eligible'}</div>
            </td>
            <td style="text-align:center">
                <select name="items[${idx}][reason]" id="reason_${idx}" class="form-select" onchange="checkSubmitReady()" ${disabled} style="min-width:140px;padding:8px 10px;border-radius:7px;border:1px solid #d1dce8;">
                    <option value="">Select reason</option>
                    ${line.is_expired ? `<option value="expired" ${defaultReason === 'expired' ? 'selected' : ''}>Expired</option>` : ''}
                    ${line.is_damaged ? `<option value="damaged" ${defaultReason === 'damaged' ? 'selected' : ''}>Damaged</option>` : ''}
                </select>
            </td>
            <td style="text-align:right">RM ${fmt(line.unit_price)}</td>
            <td style="text-align:right;font-weight:700" id="sub_${idx}">RM ${fmt((eligible ? defaultQty : 0) * parseFloat(line.unit_price))}</td>
        </tr>`;
    });

    html += `</tbody></table></div>`;
    container.innerHTML = html;
    totalRow.style.display = 'flex';
    updateTotal();
}

function updateRow(idx) {
    const input     = document.getElementById('qty_' + idx);
    const max       = parseInt(input.getAttribute('max'));
    const qty       = parseInt(input.value) || 0;
    const unitPrice = parseFloat(input.dataset.unitPrice) || 0;

    if (qty < 0) input.value = 0;
    if (qty > max) { input.value = max; input.classList.add('error'); }
    else           { input.classList.remove('error'); }

    const subtotal = Math.max(0, parseInt(input.value)||0) * unitPrice;
    const subEl    = document.getElementById('sub_' + idx);
    if (subEl) subEl.textContent = 'RM ' + fmt(subtotal);

    updateTotal();
}

function updateTotal() {
    let total = 0;
    document.querySelectorAll('[id^="sub_"]').forEach(el => {
        total += parseFloat(el.textContent.replace('RM','').trim()) || 0;
    });
    const el = document.getElementById('totalCredit');
    if (el) el.textContent = fmt(total);
}

function checkSubmitReady() {
    const invoiceSelected = !!document.getElementById('invoice_id').value;
    const qtyInputs = Array.from(document.querySelectorAll('.qty-input'));
    const anyQty = qtyInputs.some(i => parseInt(i.value) > 0);
    const validReasons = qtyInputs.every(i => {
        const qty = parseInt(i.value) || 0;
        const reasonEl = document.getElementById('reason_' + i.dataset.idx);
        if (qty <= 0) {
            return true;
        }
        return reasonEl && !!reasonEl.value;
    });
    document.getElementById('submitBtn').disabled = !(invoiceSelected && anyQty && validReasons);
}

// Init on load
document.addEventListener('DOMContentLoaded', function() {
    const sel = document.getElementById('invoice_id');
    if (sel && sel.value) {
        onInvoiceChange();
    }
    document.getElementById('reason')?.addEventListener('change', checkSubmitReady);
});
</script>
</body>
</html>
