@extends('layouts.owner')

@section('title', 'Create Purchase Order – 22UniMart')

@push('styles')
<style>
    /* Content */
    .content-center { max-width: 900px; margin: 0 auto; width: 100%; }
    .page-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 28px; }
    .page-title { font-size: 1.6rem; font-weight: 800; color: #0f2044; }
    .page-sub   { font-size: 0.85rem; color: #7a8fa8; margin-top: 4px; }
    .btn-back { display: inline-flex; align-items: center; gap: 6px; background: #fff; border: 1px solid #d1dce8; color: #3a4d6a; border-radius: 7px; padding: 7px 16px; font-size: 0.82rem; font-weight: 600; cursor: pointer; font-family: 'Inter', sans-serif; text-decoration: none; transition: background 0.15s; }
    .btn-back:hover { background: #f4f6fb; }

    /* Form Card */
    .form-card { background: #fff; border-radius: 12px; box-shadow: 0 1px 6px rgba(15,32,68,0.07); padding: 32px; }
    .form-group { margin-bottom: 20px; }
    .form-label { display: block; font-size: 0.85rem; font-weight: 600; color: #1a2744; margin-bottom: 8px; }
    .form-control, .form-select { width: 100%; border: 1px solid #d1dce8; border-radius: 8px; padding: 10px 14px; font-family: 'Inter', sans-serif; font-size: 0.9rem; color: #1a2744; background: #fff; outline: none; transition: border-color 0.15s; box-sizing: border-box; }
    .form-control:focus, .form-select:focus { border-color: #0f2044; }
    .form-control[readonly] { background: #f8fafc; color: #7a8fa8; cursor: not-allowed; }
    
    .items-section { border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; margin-bottom: 24px; }
    .items-header { background: #f8fafc; padding: 12px 16px; font-size: 0.85rem; font-weight: 700; color: #0f2044; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; }
    .item-row { display: flex; gap: 12px; padding: 16px; border-bottom: 1px solid #f0f4f8; align-items: center; }
    .item-row:last-child { border-bottom: none; }
    .item-col-name { flex: 2; }
    .item-col-qty { flex: 1; }
    .item-col-price { flex: 1; }
    .item-col-sub { flex: 1; text-align: right; font-weight: 600; color: #27ae60; }
    .item-col-action { width: 40px; text-align: center; }

    .btn-add-item { background: #f0f4f8; color: #0f2044; border: 1px dashed #d1dce8; border-radius: 8px; padding: 12px; width: 100%; font-size: 0.85rem; font-weight: 600; cursor: pointer; transition: all 0.15s; margin-bottom: 24px; }
    .btn-add-item:hover { background: #e2e8f0; }

    .btn-remove { background: #fdedec; color: #c0392b; border: none; border-radius: 6px; width: 30px; height: 30px; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 1rem; }
    .btn-remove:hover { background: #fad7d4; }

    .summary-box { background: #f0f4f8; border: 1px solid #d1dce8; border-radius: 8px; padding: 20px; margin-bottom: 24px; }
    .summary-title { font-size: 0.95rem; font-weight: 700; color: #0f2044; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid #d1dce8; }
    .summary-row { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 0.9rem; color: #3a4d6a; }
    .summary-row.final { margin-top: 16px; padding-top: 16px; border-top: 1px solid #d1dce8; font-size: 1.1rem; font-weight: 800; color: #0f2044; }
    
    .btn-submit { background: #0f2044; color: #fff; border: none; border-radius: 8px; padding: 12px 24px; font-size: 0.95rem; font-weight: 600; cursor: pointer; font-family: 'Inter', sans-serif; transition: background 0.15s; width: 100%; }
    .btn-submit:hover { background: #182e5e; }

    /* Flash / Errors */
    .alert { padding: 16px 20px; border-radius: 8px; font-size: 0.88rem; margin-bottom: 24px; }
    .alert-danger { background: #fdedec; color: #c0392b; border: 1px solid #f5b7b1; }
    .alert ul { margin: 0; padding-left: 20px; }

    .avatar { width: 34px; height: 34px; border-radius: 50%; background: #0f2044; display: flex; align-items: center; justify-content: center; color: #fff; font-size: .82rem; font-weight: 700; }
</style>
@endpush

@section('topbar')
    <div style="font-size:0.85rem;color:#7a8fa8;">
        <a href="{{ route('owner.purchase-orders.index') }}" style="color:#4a90d9;text-decoration:none;font-weight:600;">Purchase Orders</a>
        <span style="margin:0 8px;">›</span>
        <span style="color:#0f2044;font-weight:700;">Create</span>
    </div>
    <div class="topbar-right">
        <a href="{{ route('owner.notifications.index') }}" class="icon-btn" style="text-decoration:none;">🔔</a>
        @include('owner.components.topbar-profile')
    </div>
@endsection

@section('content')
<div class="content-center">
    <!-- Header -->
    <div class="page-header">
        <div>
            <div class="page-title">Create Purchase Order</div>
            <div class="page-sub">Request new inventory from your suppliers.</div>
        </div>
        <a href="{{ route('owner.purchase-orders.index') }}" class="btn-back">
            ← Back to Orders
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="form-card">
        <form method="POST" action="{{ route('owner.purchase-orders.store') }}" id="poForm">
            @csrf

            <div class="form-group">
                <label class="form-label">Supplier</label>
                <select name="supplier_id" id="supplier_id" class="form-select" required>
                    <option value="">Select supplier</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" @selected(old('supplier_id') == $supplier->id || ($preselectedItem && $preselectedItem->supplier_id === $supplier->id))>
                            {{ $supplier->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- ── Suggested Items Panel ── --}}
            <div id="suggestionPanel" style="display:none;border:1px solid #dbe7f5;border-radius:10px;margin-bottom:24px;overflow:hidden;">
                <div style="background:#eef6ff;padding:12px 16px;display:flex;align-items:center;justify-content:space-between;">
                    <div>
                        <span style="font-size:.88rem;font-weight:700;color:#0f2044;">⚠ Suggested Reorder Items</span>
                        <span style="font-size:.75rem;color:#5a6a85;margin-left:8px;">Items linked to this supplier that are low or out of stock</span>
                    </div>
                    <button type="button" onclick="addSuggestedItems()"
                        style="background:#0f2044;color:#fff;border:none;border-radius:7px;padding:8px 16px;font-size:.82rem;font-weight:600;cursor:pointer;font-family:'Inter',sans-serif;">
                        + Add Selected to Order
                    </button>
                </div>
                <div id="suggestionList">
                    <div style="color:#9daec5;font-size:.85rem;padding:12px">Loading…</div>
                </div>
            </div>
            
            <div class="items-section">
                <div class="items-header">
                    <span>Order Items</span>
                </div>
                <div id="itemsContainer">
                    <!-- Rows will be injected here -->
                </div>
            </div>

            <button type="button" class="btn-add-item" onclick="addItemRow()">+ Add Item</button>

            <div class="summary-box">
                <div class="summary-title">Order Summary</div>
                <div class="summary-row">
                    <span>Total Items:</span>
                    <span style="font-weight:600;" id="summary_quantity">0</span>
                </div>
                <div class="summary-row">
                    <span>PO Total:</span>
                    <span id="summary_po_total">RM 0.00</span>
                </div>
                <div class="summary-row" id="credit_row" style="display:none;color:#27ae60;">
                    <span>Credit Applied:</span>
                    <span id="summary_credit_applied" style="font-weight:700;">- RM 0.00</span>
                </div>
                <div class="summary-row final">
                    <span>Final Amount to Pay:</span>
                    <span id="summary_final">RM 0.00</span>
                </div>
            </div>

            {{-- ── Credit Note Panel ── --}}
            <div id="creditNotePanel" style="display:none;border:2px solid #27ae60;border-radius:10px;margin-bottom:24px;overflow:hidden;">
                <div style="background:#e8f8f0;padding:12px 18px;display:flex;align-items:center;gap:10px;">
                    <span style="font-size:1rem;">📋</span>
                    <span style="font-size:.9rem;font-weight:700;color:#1d8348;">Available Credit Notes</span>
                </div>
                <div id="creditNoteContent" style="padding:16px 18px;">
                    {{-- Populated by JS --}}
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Notes <span style="font-weight:400;color:#9daec5;">(optional)</span></label>
                <textarea name="notes" class="form-control" rows="4" placeholder="Add any special instructions or notes for the supplier here...">{{ old('notes') }}</textarea>
            </div>

            <button type="submit" class="btn-submit">Create Purchase Order</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const allItems = @json($items);
    const LOW_STOCK_URL_BASE = '{{ url("owner/purchase-orders/low-stock-items") }}';
    let rowCount = 0;
    const preselectedItemId = {{ $preselectedItem ? $preselectedItem->id : 'null' }};
    const preselectedQty = {{ $preselectedItem ? max(1, $preselectedItem->reorder_point * 2) : 1 }};

    function filterItemsBySupplier(supplierId) {
        if (!supplierId) return [];
        return allItems.filter(i => i.supplier_id == supplierId);
    }

    function renderItemOptions(supplierId, selectedItemId = null) {
        let options = '<option value="" data-price="0" data-rop="">Select item</option>';
        const filtered = filterItemsBySupplier(supplierId);
        filtered.forEach(item => {
            const isSelected = selectedItemId == item.id ? 'selected' : '';
            const rop = item.reorder_point ?? item.rop_threshold ?? 0;
            options += `<option value="${item.id}" data-price="${item.unit_price || 0}" data-rop="${rop}" ${isSelected}>
                ${item.name} (${item.quantity} in stock)
            </option>`;
        });
        return options;
    }

    function addItemRow(itemId = null, qty = 1) {
        const supplierId = document.getElementById('supplier_id').value;
        if (!supplierId) { alert('Please select a supplier first.'); return; }

        const container = document.getElementById('itemsContainer');
        const rowId = `row_${rowCount}`;
        const row = document.createElement('div');
        row.className = 'item-row';
        row.id = rowId;
        row.innerHTML = `
            <div class="item-col-name">
                <select name="items[${rowCount}][item_id]" class="form-select item-select" required onchange="updateRow('${rowId}')">
                    ${renderItemOptions(supplierId, itemId)}
                </select>
            </div>
            <div class="item-col-qty">
                <input type="number" name="items[${rowCount}][quantity]" class="form-control qty-input" min="1" value="${qty}" required oninput="updateRow('${rowId}')" placeholder="Qty">
            </div>
            <div class="item-col-price">
                <input type="text" class="form-control price-input" value="RM 0.00" readonly>
            </div>
            <div class="item-col-sub subtotal-display">RM 0.00</div>
            <div class="item-col-action">
                <button type="button" class="btn-remove" onclick="removeRow('${rowId}')">&times;</button>
            </div>
        `;
        container.appendChild(row);
        updateRow(rowId);
        rowCount++;
    }

    function removeRow(rowId) {
        document.getElementById(rowId).remove();
        calculateTotal();
    }

    function updateRow(rowId) {
        const row = document.getElementById(rowId);
        if (!row) return;
        const select = row.querySelector('.item-select');
        const qtyInput = row.querySelector('.qty-input');
        const qty = parseInt(qtyInput.value) || 0;
        const priceInput = row.querySelector('.price-input');
        const subDisplay = row.querySelector('.subtotal-display');
        let price = 0;
        if (select.selectedIndex > 0) {
            price = parseFloat(select.options[select.selectedIndex].dataset.price || 0);
        }
        priceInput.value = 'RM ' + price.toFixed(2);
        const subtotal = price * qty;
        subDisplay.textContent = 'RM ' + subtotal.toFixed(2);
        subDisplay.dataset.value = subtotal;
        calculateTotal();
    }

    function calculateTotal() {
        let totalQty = 0, totalAmount = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            totalQty += parseInt(row.querySelector('.qty-input').value) || 0;
            totalAmount += parseFloat(row.querySelector('.subtotal-display').dataset.value || 0);
        });
        document.getElementById('summary_quantity').textContent = totalQty;
        document.getElementById('summary_po_total').textContent = 'RM ' + totalAmount.toFixed(2);

        let creditApplied = 0;
        document.querySelectorAll('.cn-amount-input').forEach(input => {
            if (input.closest('.cn-row').querySelector('.cn-checkbox').checked) {
                let amount = parseFloat(input.value) || 0;
                let max = parseFloat(input.max) || 0;
                if (amount > max) {
                    amount = max;
                    input.value = amount;
                }
                creditApplied += amount;
            }
        });

        const finalAmount = Math.max(0, totalAmount - creditApplied);
        document.getElementById('summary_credit_applied').textContent = '- RM ' + creditApplied.toFixed(2);
        
        if (creditApplied > 0) {
            document.getElementById('credit_row').style.display = 'flex';
        } else {
            document.getElementById('credit_row').style.display = 'none';
        }

        document.getElementById('summary_final').textContent = 'RM ' + finalAmount.toFixed(2);
    }

    // ── Suggestion panel ──────────────────────────────────────────────────────

    async function loadSuggestions(supplierId) {
        const panel = document.getElementById('suggestionPanel');
        const list  = document.getElementById('suggestionList');
        if (!supplierId) { panel.style.display = 'none'; return; }
        list.innerHTML = '<div style="color:#9daec5;font-size:.85rem;padding:12px">Loading…</div>';
        panel.style.display = 'block';
        try {
            const res  = await fetch(`${LOW_STOCK_URL_BASE}/${supplierId}`);
            const data = await res.json();
            if (!data.items || data.items.length === 0) {
                list.innerHTML = '<div style="color:#27ae60;font-size:.85rem;padding:12px">✅ No low stock items for this supplier.</div>';
                return;
            }
            list.innerHTML = data.items.map(item => `
                <label class="suggestion-item" style="display:flex;align-items:center;gap:12px;padding:10px 14px;border-bottom:1px solid #f0f4f8;cursor:pointer;">
                    <input type="checkbox" class="suggest-cb" data-id="${item.id}" data-qty="${item.suggested_qty}" style="accent-color:#0f2044;width:15px;height:15px;">
                    <div style="flex:1">
                        <div style="font-weight:600;font-size:.85rem;color:#0f2044">${item.name}</div>
                        <div style="font-size:.72rem;color:#9daec5">
                            ${item.status === 'out_of_stock'
                                ? '<span style="color:#c0392b;font-weight:700">OUT OF STOCK</span>'
                                : `<span style="color:#d4870a;font-weight:600">Low Stock</span> — ${item.quantity} left (ROP: ${item.reorder_point})`}
                        </div>
                    </div>
                    <div style="font-size:.82rem;color:#0f2044">
                        Suggested qty: <strong>${item.suggested_qty}</strong>
                    </div>
                    <div style="font-size:.82rem;color:#27ae60;font-weight:600">
                        RM ${(item.unit_price * item.suggested_qty).toFixed(2)}
                    </div>
                </label>
            `).join('');
        } catch(e) {
            list.innerHTML = '<div style="color:#c0392b;font-size:.85rem;padding:12px">Failed to load suggestions.</div>';
        }
    }

    function addSuggestedItems() {
        const cbs = document.querySelectorAll('.suggest-cb:checked');
        if (cbs.length === 0) { alert('Please tick at least one suggested item.'); return; }
        cbs.forEach(cb => {
            const itemId = cb.dataset.id;
            const qty    = parseInt(cb.dataset.qty);
            addItemRow(itemId, qty);
        });
        // Uncheck all after adding
        document.querySelectorAll('.suggest-cb').forEach(cb => cb.checked = false);
    }

    // ── Credit Note Logic ────────────────────────────────────────────────────
    const CREDIT_NOTE_URL_BASE = '{{ url("owner/suppliers") }}';

    async function loadCreditNotes(supplierId) {
        const panel = document.getElementById('creditNotePanel');
        const content = document.getElementById('creditNoteContent');
        if (!supplierId) { panel.style.display = 'none'; return; }

        content.innerHTML = '<div style="color:#9daec5;font-size:.85rem;padding:12px">Loading credit notes…</div>';
        panel.style.display = 'block';

        calculateTotal();

        try {
            const res  = await fetch(`${CREDIT_NOTE_URL_BASE}/${supplierId}/credit-notes`);
            const data = await res.json();
            if (!data.creditNotes || data.creditNotes.length === 0 || data.availableCredit <= 0) {
                panel.style.display = 'none';
                return;
            }
            
            let html = '<p style="font-size:.85rem;color:#3a4d6a;margin-top:0;margin-bottom:12px;">You have <strong>RM ' + parseFloat(data.availableCredit).toFixed(2) + '</strong> credit available. Would you like to apply it to this order?</p>';
            html += '<div style="display:flex;flex-direction:column;gap:12px;">';
            
            data.creditNotes.forEach(cn => {
                const balance = parseFloat(cn.remaining_balance);
                html += `
                    <div class="cn-row" style="display:flex;align-items:center;gap:12px;background:#f4f6fb;padding:12px;border-radius:8px;">
                        <input type="checkbox" class="cn-checkbox" onchange="toggleCreditNote(this, ${cn.id})" style="accent-color:#0f2044;width:16px;height:16px;">
                        <div style="flex:1;">
                            <div style="font-weight:700;color:#0f2044;font-size:.9rem;">${cn.credit_note_id}</div>
                            <div style="font-size:.78rem;color:#5a6a85;">Balance: <strong>RM ${balance.toFixed(2)}</strong></div>
                        </div>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <span style="font-size:.8rem;color:#3a4d6a;font-weight:600;">Apply RM:</span>
                            <input type="number" step="0.01" min="0.01" max="${balance}" value="${balance}" name="applied_credit_notes[${cn.id}]" class="form-control cn-amount-input" style="width:100px;text-align:right;" disabled oninput="calculateTotal()">
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            content.innerHTML = html;
        } catch(e) {
            panel.style.display = 'none';
        }
    }

    function toggleCreditNote(checkbox, cnId) {
        const input = checkbox.closest('.cn-row').querySelector('.cn-amount-input');
        if (checkbox.checked) {
            input.disabled = false;
        } else {
            input.disabled = true;
            // reset to max balance just in case
            input.value = input.max;
        }
        calculateTotal();
    }

    document.getElementById('supplier_id').addEventListener('change', function() {
        document.getElementById('itemsContainer').innerHTML = '';
        rowCount = 0;
        calculateTotal();
        loadSuggestions(this.value);
        loadCreditNotes(this.value);
        if (this.value) addItemRow();
    });

    // Init
    if (document.getElementById('supplier_id').value) {
        loadSuggestions(document.getElementById('supplier_id').value);
        if (preselectedItemId) {
            addItemRow(preselectedItemId, preselectedQty);
        } else {
            addItemRow();
        }
    }
</script>
@endpush