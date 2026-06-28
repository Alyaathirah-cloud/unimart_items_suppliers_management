@extends('layouts.owner')

@section('title', 'Edit Purchase Order – 22UniMart')

@push('styles')
<style>
    .content-wrap { max-width: 900px; margin: 0 auto; width: 100%; }
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
</style>
@endpush

@section('breadcrumbs')
    <a href="{{ route('owner.purchase-orders.index') }}">Purchase Orders</a> › <span style="color:#0f2044;">Edit</span>
@endsection

@section('content')
<div class="content-wrap">
    <div class="page-header">
        <div>
            <div class="page-title">Edit Purchase Order</div>
            <div class="page-sub">Update pending purchase order details before supplier confirmation.</div>
        </div>
        <a href="{{ route('owner.purchase-orders.show', $purchaseOrder) }}" class="btn-back">
            ← Back to Order
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
        <form method="POST" action="{{ route('owner.purchase-orders.update', $purchaseOrder) }}" id="poForm">
            @csrf
            @method('PATCH')

            <div class="form-group">
                <label class="form-label">Purchase Order Number</label>
                <input type="text" class="form-control" value="{{ $purchaseOrder->po_number }}" readonly>
            </div>

            <div class="form-group">
                <label class="form-label">Supplier</label>
                <select name="supplier_id" id="supplier_id" class="form-select" required>
                    <option value="">Select supplier</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" @selected(old('supplier_id', $purchaseOrder->supplier_id) == $supplier->id)>
                            {{ $supplier->name }}
                        </option>
                    @endforeach
                </select>
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
                <div class="summary-row final">
                    <span>Final Amount to Pay:</span>
                    <span id="summary_final">RM 0.00</span>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Notes <span style="font-weight:400;color:#9daec5;">(optional)</span></label>
                <textarea name="notes" class="form-control" rows="4">{{ old('notes', $purchaseOrder->notes) }}</textarea>
            </div>

            <button type="submit" class="btn-submit">Save Changes</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const allItems = @json($items);
    let rowCount = 0;
    
    // Existing lines
    const existingLines = @json($purchaseOrder->orderItems);

    function filterItemsBySupplier(supplierId) {
        if (!supplierId) return [];
        return allItems.filter(i => i.supplier_id == supplierId);
    }

    function renderItemOptions(supplierId, selectedItemId = null) {
        let options = '<option value="" data-price="0">Select item</option>';
        const filtered = filterItemsBySupplier(supplierId);
        filtered.forEach(item => {
            const isSelected = selectedItemId == item.id ? 'selected' : '';
            options += `<option value="${item.id}" data-price="${item.unit_price || 0}" ${isSelected}>
                ${item.name} (${item.quantity} in stock)
            </option>`;
        });
        return options;
    }

    function addItemRow(itemId = null, qty = 1) {
        const supplierId = document.getElementById('supplier_id').value;
        if (!supplierId) {
            alert('Please select a supplier first.');
            return;
        }

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
            <div class="item-col-sub subtotal-display">
                RM 0.00
            </div>
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
        if(!row) return;
        
        const select = row.querySelector('.item-select');
        const qty = parseInt(row.querySelector('.qty-input').value) || 0;
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
        let totalQty = 0;
        let totalAmount = 0;
        
        document.querySelectorAll('.item-row').forEach(row => {
            totalQty += parseInt(row.querySelector('.qty-input').value) || 0;
            totalAmount += parseFloat(row.querySelector('.subtotal-display').dataset.value || 0);
        });
        
        document.getElementById('summary_quantity').textContent = totalQty;
        document.getElementById('summary_final').textContent = 'RM ' + totalAmount.toFixed(2);
    }

    document.getElementById('supplier_id').addEventListener('change', function() {
        document.getElementById('itemsContainer').innerHTML = '';
        rowCount = 0;
        calculateTotal();
        if (this.value) {
            addItemRow();
        }
    });

    // Init with existing lines
    if (document.getElementById('supplier_id').value) {
        if (existingLines && existingLines.length > 0) {
            existingLines.forEach(line => {
                addItemRow(line.item_id, line.quantity);
            });
        } else if ('{{ $purchaseOrder->item_id }}' !== '') {
            // legacy single item
            addItemRow('{{ $purchaseOrder->item_id }}', '{{ $purchaseOrder->quantity }}');
        } else {
            addItemRow();
        }
    }
</script>
@endpush
