<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create Purchase Order – 22UniMart</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: #eef2f7; display: flex; min-height: 100vh; }

        /* ── Sidebar ── */
        .sidebar { width: 210px; flex-shrink: 0; background: #0f2044; color: #fff; display: flex; flex-direction: column; padding: 0 0 24px 0; position: fixed; top: 0; left: 0; height: 100vh; overflow-y: auto; }
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
        .sidebar-link:hover { color: #fff; }

        /* ── Main ── */
        .main { margin-left: 210px; flex: 1; display: flex; flex-direction: column; }

        /* Topbar */
        .topbar { background: #fff; border-bottom: 1px solid #e2e8f0; padding: 0 32px; height: 56px; display: flex; align-items: center; gap: 16px; position: sticky; top: 0; z-index: 10; }
        .topbar-right { margin-left: auto; display: flex; align-items: center; gap: 20px; }
        .icon-btn { background: none; border: none; cursor: pointer; color: #5a6a85; font-size: 1.1rem; }
        .btn-switch { background: #fff; border: 1px solid #d1dce8; border-radius: 7px; padding: 7px 14px; font-size: 0.82rem; font-weight: 600; color: #3a4d6a; cursor: pointer; font-family: 'Inter', sans-serif; }
        .topbar-profile { display: flex; align-items: center; gap: 8px; }
        .profile-name { font-size: 0.85rem; font-weight: 600; color: #1a2744; }
        .profile-sub { font-size: 0.72rem; color: #9daec5; }
        .avatar { width: 36px; height: 36px; border-radius: 50%; background: #0f2044; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 0.85rem; font-weight: 700; }

        /* Content */
        .content { padding: 32px; max-width: 900px; margin: 0 auto; width: 100%; }
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
        <a href="{{ route('owner.purchase-orders.index') }}" class="nav-item active"><span class="nav-icon">🛒</span> Purchase Orders</a>
        <a href="{{ route('owner.return-requests.index') }}" class="nav-item"><span class="nav-icon">↩</span> Return Requests</a>
        <a href="{{ route('owner.invoices.index') }}" class="nav-item"><span class="nav-icon">📄</span> Invoices</a>
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
        <div style="font-size:0.85rem;color:#7a8fa8;">
            <a href="{{ route('owner.purchase-orders.index') }}" style="color:#4a90d9;text-decoration:none;font-weight:600;">Purchase Orders</a>
            <span style="margin:0 8px;">›</span>
            <span style="color:#0f2044;font-weight:700;">Create</span>
        </div>
        <div class="topbar-right">
            <a href="{{ route('owner.notifications.index') }}" class="icon-btn" style="text-decoration:none;">🔔</a>
            <div class="topbar-profile">
                <div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <div>
                    <div style="font-size:0.85rem;font-weight:600;color:#1a2744">{{ auth()->user()->name }}</div>
                    <form action="{{ route('logout') }}" method="POST" style="display:inline">
                        @csrf
                        <button type="submit" style="background:none;border:none;font-size:0.72rem;color:#9daec5;cursor:pointer;font-family:inherit;padding:0;">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="content">
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
                    <textarea name="notes" class="form-control" rows="4" placeholder="Add any special instructions or notes for the supplier here...">{{ old('notes') }}</textarea>
                </div>

                <button type="submit" class="btn-submit">Create Purchase Order</button>
            </form>
        </div>
    </div>
</div>

<script>
    const allItems = @json($items);
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
        const qtyInput = row.querySelector('.qty-input');
        const qty = parseInt(qtyInput.value) || 0;
        const priceInput = row.querySelector('.price-input');
        const subDisplay = row.querySelector('.subtotal-display');
        
        let price = 0;
        let ropPlaceholder = 'Qty';
        if (select.selectedIndex > 0) {
            price = parseFloat(select.options[select.selectedIndex].dataset.price || 0);
            ropPlaceholder = select.options[select.selectedIndex].dataset.rop || 'Qty';
            qtyInput.placeholder = ropPlaceholder;
        } else {
            qtyInput.placeholder = 'Qty';
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
        // Clear all rows when supplier changes
        document.getElementById('itemsContainer').innerHTML = '';
        rowCount = 0;
        calculateTotal();
        
        if (this.value) {
            addItemRow();
        }
    });

    // Init
    if (document.getElementById('supplier_id').value) {
        if (preselectedItemId) {
            addItemRow(preselectedItemId, preselectedQty);
        } else {
            addItemRow();
        }
    }
</script>
</body>
</html>