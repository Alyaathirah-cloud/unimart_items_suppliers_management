<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Item – 22UniMart</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: #eef2f7; display: flex; min-height: 100vh; color: #1a2744; }

        .sidebar { width: 210px; flex-shrink: 0; background: #0f2044; color: #fff; display: flex; flex-direction: column; padding: 0 0 24px 0; position: fixed; top: 0; left: 0; height: 100vh; }
        .sidebar-brand { padding: 20px 20px 4px; display: flex; align-items: center; gap: 12px; }
        .brand-square { width: 32px; height: 32px; background: #fff; color: #0f2044; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 0.9rem; font-weight: 800; flex-shrink: 0; }
        .brand-text .brand-name { font-size: 0.9rem; font-weight: 800; }
        .brand-text .brand-sub  { font-size: 0.68rem; color: #8ca0c0; }
        .sidebar-nav { flex: 1; margin-top: 20px; overflow-y: auto; }
        .nav-item { display: flex; align-items: center; gap: 12px; padding: 11px 20px; font-size: 0.88rem; font-weight: 500; color: #8ca0c0; cursor: pointer; text-decoration: none; transition: all 0.15s; border-left: 3px solid transparent; }
        .nav-item:hover { color: #fff; background: rgba(255,255,255,0.06); }
        .nav-item.active { color: #fff; background: rgba(255,255,255,0.1); border-left-color: #4a90d9; }
        .nav-icon { width: 18px; text-align: center; flex-shrink: 0; }
        .sidebar-bottom { padding: 0 20px; display: flex; flex-direction: column; gap: 8px; }
        .sidebar-link { display: flex; align-items: center; gap: 10px; color: #8ca0c0; font-size: 0.82rem; text-decoration: none; padding: 6px 0; }
        .btn-report { background: #c0392b; color: #fff; border: none; border-radius: 8px; padding: 12px 16px; font-size: 0.85rem; font-weight: 600; cursor: pointer; width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px; transition: background 0.15s; }
        .btn-report:hover { background: #a93226; }

        .main { margin-left: 210px; flex: 1; display: flex; flex-direction: column; }
        .topbar { background: #fff; padding: 0 32px; height: 56px; display: flex; align-items: center; gap: 16px; position: sticky; top: 0; z-index: 10; border-bottom: 1px solid #e2e8f0; }
        .breadcrumbs { font-size: 0.75rem; font-weight: 700; color: #7a8fa8; letter-spacing: 1px; text-transform: uppercase; }
        .breadcrumbs a { color: inherit; text-decoration: none; }
        .breadcrumbs a:hover { color: #0f2044; }
        .topbar-right { margin-left: auto; display: flex; align-items: center; gap: 20px; }
        .avatar { width: 32px; height: 32px; border-radius: 50%; background: #0f2044; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 0.75rem; font-weight: 700; }

        .content { padding: 40px; max-width: 920px; }
        .page-title { font-size: 2rem; font-weight: 800; color: #0f2044; margin-bottom: 8px; }
        .page-sub { font-size: 0.95rem; color: #5a6a85; line-height: 1.6; margin-bottom: 32px; max-width: 760px; }

        .form-card { background: #fff; border-radius: 16px; padding: 32px; box-shadow: 0 1px 24px rgba(15,32,68,0.08); }
        .form-row { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 20px; margin-bottom: 20px; }
        .form-group { display: flex; flex-direction: column; gap: 8px; }
        .form-label { font-size: 0.75rem; font-weight: 700; color: #5a6a85; text-transform: uppercase; letter-spacing: 0.5px; }
        .form-control, .form-select { background: #f4f6fb; border: 1px solid transparent; border-radius: 10px; padding: 14px 16px; font-size: 0.95rem; color: #1a2744; transition: all 0.2s; outline: none; }
        .form-control:focus, .form-select:focus { background: #fff; border-color: #4a90d9; box-shadow: 0 0 0 3px rgba(74, 144, 217, 0.12); }
        .form-control::placeholder { color: #9daec5; }
        .form-actions { display: flex; flex-wrap: wrap; gap: 16px; align-items: center; margin-top: 24px; }
        .btn-submit { background: #0f2044; color: #fff; border: none; border-radius: 10px; padding: 14px 26px; font-size: 0.95rem; font-weight: 700; cursor: pointer; transition: background 0.15s; }
        .btn-submit:hover { background: #122a50; }
        .btn-discard { color: #5a6a85; text-decoration: none; font-weight: 600; }
        .error-box { background: #fff3f3; border: 1px solid #f5c2c7; border-radius: 12px; padding: 18px; color: #842029; margin-bottom: 24px; }
    </style>
</head>
<body>

<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="brand-square">22</div>
        <div class="brand-text">
            <div class="brand-name">22UNIMART</div>
            <div class="brand-sub">Inventory Control</div>
        </div>
    </div>
    <nav class="sidebar-nav">
        <a href="{{ route('owner.dashboard') }}" class="nav-item {{ request()->routeIs('owner.dashboard') ? 'active' : '' }}"><span class="nav-icon">⊞</span> Dashboard</a>
        <a href="{{ route('owner.items.index') }}" class="nav-item {{ request()->routeIs('owner.items.*') ? 'active' : '' }}"><span class="nav-icon">📦</span> Inventory</a>
        <a href="{{ route('owner.suppliers.index') }}" class="nav-item {{ request()->routeIs('owner.suppliers.*') ? 'active' : '' }}"><span class="nav-icon">🏢</span> Suppliers</a>
        <a href="{{ route('owner.purchase-orders.index') }}" class="nav-item {{ request()->routeIs('owner.purchase-orders.*') ? 'active' : '' }}"><span class="nav-icon">🛒</span> Purchase Orders</a>
        <a href="{{ route('owner.return-requests.index') }}" class="nav-item {{ request()->routeIs('owner.return-requests.*') ? 'active' : '' }}"><span class="nav-icon">↩</span> Return Requests</a>
        <a href="{{ route('owner.invoices.index') }}" class="nav-item {{ request()->routeIs('owner.invoices.*') ? 'active' : '' }}"><span class="nav-icon">📄</span> Invoices</a>
        <a href="{{ route('owner.credit-notes.index') }}" class="nav-item {{ request()->routeIs('owner.credit-notes.*') ? 'active' : '' }}"><span class="nav-icon">📋</span> Credit Notes</a>
        <a href="{{ route('owner.notifications.index') }}" class="nav-item {{ request()->routeIs('owner.notifications.*') ? 'active' : '' }}"><span class="nav-icon">🔔</span> Notifications</a>
    </nav>
        <div class="sidebar-bottom">
        <div class="sidebar-link" style="color: #fff; cursor: default; font-weight: bold;">Role: Owner</div>
        <form action="{{ route('logout') }}" method="POST" style="margin: 0; width: 100%;">
            @csrf
            <button type="submit" class="btn-report" style="background: #c0392b;">Logout</button>
        </form>
    </div>
</aside>

<div class="main">
    <div class="topbar">
        <div class="breadcrumbs"><a href="{{ route('owner.items.index') }}">Inventory</a> › <span style="color:#0f2044;">Edit Item</span></div>
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

    <div class="content">
        <div class="page-title">Edit Item</div>
        <div class="page-sub">Update inventory details, supplier assignment, and reorder thresholds in the owner portal.</div>

        @if(request('from') === 'delivery')
            <div style="background:#e8f0fb;border-left:4px solid #3a7bd5;
                        border-radius:8px;padding:12px 18px;margin-bottom:20px;
                        font-size:0.87rem;color:#1a2744;">
                <strong>ℹ Delivery Update</strong> —
                Please update the <strong>expiry date</strong> and
                <strong>unit price</strong> for the new batch just received.
                Selling price will be recalculated automatically on save.
            </div>
        @endif

        @if($errors->any())
            <div class="error-box">
                <strong>There are errors with your submission.</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="form-card">
            <form action="{{ route('owner.items.update', $item) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-row">
                    <div class="form-group" style="grid-column: span 2;">
                        <label class="form-label" for="name">Item Name <span class="text-danger">*</span></label>
                        <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $item->name) }}" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="category">Category</label>
                        <input type="text" id="category" name="category" class="form-control" value="{{ old('category', $item->category) }}">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="unit_price">Unit Cost Price (RM)</label>
                        <input type="number" step="0.01" id="unit_price" name="unit_price" class="form-control" value="{{ old('unit_price', $item->unit_price) }}">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="markup_percentage">Markup Percentage</label>
                        <select id="markup_percentage" name="markup_percentage" class="form-select">
                            <option value="">-- Select Markup --</option>
                            <option value="20" @selected(old('markup_percentage', (int)$item->markup_percentage) == 20)>+20% (Supplier Delivery Item)</option>
                            <option value="30" @selected(old('markup_percentage', (int)$item->markup_percentage) == 30)>+30% (Cash & Carry / Shop Purchase Item)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="selling_price">Selling Price (RM)</label>
                        <input type="text" id="selling_price" class="form-control" style="background:#eef2f7;font-weight:600;color:#1d8348" readonly placeholder="Auto-calculated">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="quantity">Quantity <span class="text-danger">*</span></label>
                        <input type="number" id="quantity" name="quantity" class="form-control" min="0" value="{{ old('quantity', $item->quantity) }}" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="reorder_point">Reorder Point <span class="text-danger">*</span></label>
                        <input type="number" id="reorder_point" name="reorder_point" class="form-control" min="0" value="{{ old('reorder_point', $item->reorder_point) }}" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="expiry_date">Expiry Date</label>
                        <input type="date" id="expiry_date" name="expiry_date" class="form-control" value="{{ old('expiry_date', $item->expiry_date?->toDateString()) }}" min="{{ date('Y-m-d') }}">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="supplier_id">Supplier</label>
                        <select id="supplier_id" name="supplier_id" class="form-select">
                            <option value="">-- Select Supplier --</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" @selected(old('supplier_id', $item->supplier_id) == $supplier->id)>{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-submit">Update Item</button>
                    <a href="{{ route('owner.items.index') }}" class="btn-discard">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const unitPriceInput = document.getElementById('unit_price');
        const markupSelect = document.getElementById('markup_percentage');
        const sellingPriceInput = document.getElementById('selling_price');

        function calculateSellingPrice() {
            const costPrice = parseFloat(unitPriceInput.value);
            const markup = parseFloat(markupSelect.value);

            if (!isNaN(costPrice) && !isNaN(markup)) {
                const sellingPrice = costPrice + (costPrice * markup / 100);
                sellingPriceInput.value = 'RM ' + sellingPrice.toFixed(2);
            } else {
                sellingPriceInput.value = '';
            }
        }

        unitPriceInput.addEventListener('input', calculateSellingPrice);
        markupSelect.addEventListener('change', calculateSellingPrice);
        
        // Initial calculation on load
        calculateSellingPrice();
    });
</script>
</body>
</html>
