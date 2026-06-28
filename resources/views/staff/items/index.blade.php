@extends('layouts.staff')

@section('title', 'Inventory – 22UniMart')

@push('styles')
<style>
    .sidebar { width: 230px; }
    .topbar { padding: 0 32px; }
    .topbar-search { display: flex; align-items: center; gap: 8px; background: #f4f6fb; border-radius: 8px; padding: 8px 14px; flex: 1; max-width: 340px; }
    .topbar-search input { border: none; outline: none; font-family: 'Inter', sans-serif; font-size: .85rem; color: #3a4d6a; width: 100%; }
    .topbar-search input::placeholder { color: #9daec5; }
    .topbar-right { margin-left: auto; display: flex; align-items: center; gap: 16px; }
    .avatar { width: 34px; height: 34px; border-radius: 50%; background: #0f2044; display: flex; align-items: center; justify-content: center; color: #fff; font-size: .82rem; font-weight: 700; }
    .content { padding: 32px; }
    .page-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 28px; }
    .page-title { font-size: 1.7rem; font-weight: 800; color: #0f2044; }
    .page-sub { font-size: .83rem; color: #7a8fa8; margin-top: 6px; max-width: 520px; line-height: 1.6; }
    .btn-add { background: #0f2044; color: #fff; border: none; border-radius: 8px; padding: 12px 22px; font-size: .88rem; font-weight: 600; cursor: pointer; font-family: 'Inter', sans-serif; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; transition: background .15s; white-space: nowrap; }
    .btn-add:hover { background: #182e5e; }
    .stat-row { display: grid; grid-template-columns: repeat(4,1fr); gap:20px; margin-bottom:28px; }
    .stat-card { background:#fff;border-radius:12px;padding:22px 24px;box-shadow:0 1px 6px rgba(15,32,68,.07);display:flex;justify-content:space-between;align-items:flex-start; }
    .stat-label{font-size:.67rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#9daec5;}
    .stat-value{font-size:2rem;font-weight:800;color:#0f2044;margin:8px 0 4px;line-height:1;}
    .stat-hint{font-size:.75rem;color:#27ae60;font-weight:500;}
    .stat-hint.warn{color:#d4870a;}
    .stat-hint.danger{color:#c0392b;}
    .ic-purple{background:#f3e8ff;color:#7c3aed;}
    .stat-icon-wrap{width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.1rem;flex-shrink:0;}
    .ic-blue{background:#e8f0fb;color:#1d4ed8;}
    .ic-orange{background:#fef3e2;color:#d4870a;}
    .ic-red{background:#fdedec;color:#c0392b;}
    .filter-bar{display:flex;align-items:center;gap:12px;margin-bottom:20px;flex-wrap:wrap;}
    .filter-search{display:flex;align-items:center;gap:8px;background:#fff;border:1px solid #e2e8f0;border-radius:8px;padding:9px 14px;flex:1;min-width:220px;}
    .filter-search input{border:none;outline:none;font-family:'Inter',sans-serif;font-size:.85rem;color:#1a2744;width:100%;}
    .filter-search input::placeholder{color:#9daec5;}
    .filter-select{background:#fff;border:1px solid #e2e8f0;border-radius:8px;padding:9px 14px;font-family:'Inter',sans-serif;font-size:.85rem;color:#1a2744;outline:none;cursor:pointer;min-width:140px;}
    .filter-btn{background:#fff;border:1px solid #e2e8f0;border-radius:8px;padding:9px 12px;cursor:pointer;color:#5a6a85;display:flex;align-items:center;gap:6px;font-family:'Inter',sans-serif;font-size:.82rem;font-weight:500;transition:all .15s;}
    .filter-btn:hover{background:#f4f6fb;}
    .filter-btn-primary{background:#0f2044;color:#fff;border-color:#0f2044;}
    .filter-btn-primary:hover{background:#182e5e;}
    .table-card{background:#fff;border-radius:12px;box-shadow:0 1px 6px rgba(15,32,68,.07);overflow:hidden;}
    table{width:100%;border-collapse:collapse;}
    thead th{padding:12px 16px;font-size:.67rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#9daec5;text-align:left;background:#f8fafc;border-bottom:1px solid #edf2f7;white-space:nowrap;}
    tbody td{padding:14px 16px;font-size:.85rem;color:#1a2744;border-bottom:1px solid #f0f4f8;vertical-align:middle;}
    tbody tr:last-child td{border-bottom:none;}
    tbody tr:hover td{background:#fafbfd;}
    tbody tr.expired-row{background:#fff8f8;}
    input[type=checkbox]{width:15px;height:15px;cursor:pointer;accent-color:#0f2044;}
    .item-name-cell{display:flex;align-items:center;gap:12px;}
    .item-icon{width:34px;height:34px;border-radius:8px;background:#f0f4fb;display:flex;align-items:center;justify-content:center;font-size:.95rem;flex-shrink:0;}
    .item-name{font-weight:600;color:#0f2044;}
    .badge-inline{display:inline-flex;align-items:center;padding:2px 8px;border-radius:4px;font-size:.65rem;font-weight:700;letter-spacing:.5px;text-transform:uppercase;margin-left:8px;}
    .badge-low{background:#fef3e2;color:#d4870a;border:1px solid #fde0a0;}
    .badge-low-stock{background:#fdedec;color:#c0392b;border:1px solid #f5b7b1;}
    .badge-expired{background:#fdedec;color:#c0392b;border:1px solid #f5b7b1;}
    .badge-near{background:#e8f4ff;color:#2563eb;border:1px solid #bfdbfe;}
    .badge-normal{background:#e8f8f0;color:#1d8348;border:1px solid #a9dfbf;}
    .qty-low{color:#d4870a;font-weight:700;}
    .expiry-red{color:#c0392b;font-weight:600;}
    .actions{display:flex;gap:6px;flex-wrap:wrap;align-items:center;}
    .btn{display:inline-flex;align-items:center;gap:4px;padding:5px 12px;border-radius:6px;font-size:.75rem;font-weight:600;cursor:pointer;border:1px solid transparent;transition:all .15s;font-family:'Inter',sans-serif;text-decoration:none;white-space:nowrap;}
    .btn-edit{background:#f0f4ff;color:#2563eb;border-color:#c7d7f9;}
    .btn-edit:hover{background:#dbe8ff;}
    .btn-del{background:#fdedec;color:#c0392b;border-color:#f5b7b1;}
    .btn-del:hover{background:#fad7d4;}
    .btn-po{background:#fef3e2;color:#d4870a;border-color:#fde0a0;}
    .btn-po:hover{background:#fde4a0;}
    .btn-rr{background:#e8f8f0;color:#1d8348;border-color:#a9dfbf;}
    .btn-rr:hover{background:#d0f0e0;}
    .pagination-bar{display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-top:1px solid #f0f4f8;font-size:.82rem;color:#7a8fa8;flex-wrap:wrap;gap:12px;}
    .pagination-links{display:flex;align-items:center;gap:4px;}
    .page-btn{width:32px;height:32px;border-radius:7px;border:1px solid #e2e8f0;background:#fff;display:flex;align-items:center;justify-content:center;font-size:.82rem;font-weight:600;color:#3a4d6a;cursor:pointer;text-decoration:none;transition:all .15s;}
    .page-btn:hover{background:#f4f6fb;}
    .page-btn.active{background:#0f2044;color:#fff;border-color:#0f2044;}
    .page-btn.disabled{color:#c0cfe0;cursor:not-allowed;pointer-events:none;}
    .flash{padding:12px 20px;border-radius:8px;font-size:.88rem;margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;}
    .flash-success{background:#e8f8f0;color:#1d8348;border:1px solid #a9dfbf;}
    .flash-close{background:none;border:none;cursor:pointer;color:inherit;font-size:1rem;}
    .empty-state{padding:48px;text-align:center;color:#9daec5;}
    .empty-state a{color:#3a7bd5;text-decoration:none;font-weight:600;}
</style>
@endpush

@section('breadcrumbs')
    <a href="{{ route('staff.dashboard') }}">Dashboard</a> › <span style="color:#0f2044;">Inventory</span>
@endsection

@section('topbar')
    <span class="topbar-brand">22UniMart</span>
    <div class="topbar-search">
        <svg width="14" height="14" fill="none" stroke="#9daec5" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
        <input type="text" placeholder="Search inventory..." id="topSearchInput" value="{{ $keyword ?? '' }}" onkeydown="if(event.key==='Enter')applySearch()">
    </div>
    <div class="topbar-right">
        <a href="{{ route('staff.notif.index') }}" class="icon-btn" style="text-decoration:none;">🔔</a>
        @include('staff.components.topbar-profile')
    </div>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <div class="page-title">Inventory Items</div>
            <div class="page-sub">Manage your current stock levels, monitor reorder points, and track expiring goods to ensure operational efficiency.</div>
        </div>
        <a href="{{ route('staff.inventory.create') }}" class="btn-add">+ Add New Item</a>
    </div>

    @if(session('success'))
        <div class="flash flash-success" id="flash-msg">
            ✅ {{ session('success') }}
            <button class="flash-close" onclick="document.getElementById('flash-msg').style.display='none'">✕</button>
        </div>
    @endif

    <div class="stat-row">
        <div class="stat-card">
            <div>
                <div class="stat-label">Total Items</div>
                <div class="stat-value">{{ number_format($items->total()) }}</div>
                <div class="stat-hint">↑ All inventory items</div>
            </div>
            <div class="stat-icon-wrap ic-blue">📦</div>
        </div>
        <div class="stat-card">
            <div>
                <div class="stat-label">Out of Stock</div>
                <div class="stat-value" style="color:{{ $outOfStock > 0 ? '#c0392b' : '#0f2044' }}">{{ $outOfStock }}</div>
                <div class="stat-hint danger">Zero quantity</div>
            </div>
            <div class="stat-icon-wrap ic-red">🚫</div>
        </div>
        <div class="stat-card">
            <div>
                <div class="stat-label">Low Stock Alerts</div>
                <div class="stat-value" style="color:{{ $lowStock > 0 ? '#d4870a' : '#0f2044' }}">{{ $lowStock }}</div>
                <div class="stat-hint warn">Require immediate reorder</div>
            </div>
            <div class="stat-icon-wrap ic-orange">⚠️</div>
        </div>
        <div class="stat-card">
            <div>
                <div class="stat-label" style="color:#c0392b">Expired / Expiring</div>
                <div class="stat-value" style="color:{{ ($expired + $nearExpiry) > 0 ? '#c0392b' : '#0f2044' }}">{{ $expired + $nearExpiry }}</div>
                <div class="stat-hint danger">Action required</div>
            </div>
            <div class="stat-icon-wrap ic-red">📅</div>
        </div>
    </div>

    <form method="GET" action="{{ route('staff.inventory.index') }}" id="filterForm">
        <div class="filter-bar">
            <div class="filter-search">
                <svg width="14" height="14" fill="none" stroke="#9daec5" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                <input type="text" name="keyword" placeholder="Search by item name, ID, or supplier..." value="{{ $keyword ?? '' }}">
            </div>
            <select name="status" class="filter-select" onchange="this.form.submit()">
                <option value="">Status: All</option>
                <option value="out_of_stock" @selected(($status??'') === 'out_of_stock')>Out of Stock</option>
                <option value="low_stock"   @selected(($status??'') === 'low_stock')>Low Stock</option>
                <option value="near_expiry" @selected(($status??'') === 'near_expiry')>Expiring Soon</option>
                <option value="expired"     @selected(($status??'') === 'expired')>Expired</option>
                <option value="damaged"     @selected(($status??'') === 'damaged')>Damaged</option>
                <option value="ok"          @selected(($status??'') === 'ok')>Normal</option>
            </select>
            <select name="category" class="filter-select" onchange="this.form.submit()">
                <option value="">Category: All</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat }}" @selected(($category??'') === $cat)>{{ $cat }}</option>
                @endforeach
            </select>
            <button type="submit" class="filter-btn filter-btn-primary">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                Filter
            </button>
            <a href="{{ route('staff.inventory.export', request()->query()) }}" class="filter-btn">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2 2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Export
            </a>
        </div>
    </form>

    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Quantity</th>
                    <th>Selling Price</th>
                    <th>ROP Threshold</th>
                    <th>Expiry Date</th>
                    <th>Days Until Expiry</th>
                    <th>Supplier</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    @php
                        $isLow     = $item->isLowStock();
                        $isExpired = $item->isExpired();
                        $isNear    = $item->isNearExpiry();
                        $isDamaged = $item->isDamaged();
                        $hasPendingReturn = $pendingReturnItemIds->contains($item->id);

                        $daysUntil = $item->expiry_date ? now()->startOfDay()->diffInDays($item->expiry_date->startOfDay(), false) : null;
                    @endphp
                    <tr class="{{ $isExpired || $isDamaged ? 'expired-row' : '' }}">
                        <td>
                            <div class="item-name-cell">
                                <div class="item-icon">
                                    @if(str_contains(strtolower($item->category ?? ''), 'electronic'))💻
                                    @elseif(str_contains(strtolower($item->category ?? ''), 'health'))🩺
                                    @elseif(str_contains(strtolower($item->category ?? ''), 'food') || str_contains(strtolower($item->category ?? ''), 'beverage'))🥛
                                    @elseif(str_contains(strtolower($item->category ?? ''), 'furni'))🪑
                                    @elseif(str_contains(strtolower($item->category ?? ''), 'office'))🖨️
                                    @else📦
                                    @endif
                                </div>
                                <div>
                                    <span class="item-name">{{ $item->name }}</span>
                                    @if($item->isOutOfStock())
                                        <span class="badge-inline badge-expired">OUT OF STOCK</span>
                                    @elseif($isLow)
                                        <span class="badge-inline badge-low-stock">LOW STOCK</span>
                                    @endif
                                    @if($isDamaged)
                                        <span class="badge-inline badge-expired">Damaged ({{ max(0, (int) $item->damaged_quantity) }} units)</span>
                                    @endif
                                    @if($isExpired && $item->quantity > 0)
                                        <span class="badge-inline badge-expired">Expired</span>
                                    @endif
                                    @if($hasPendingReturn)
                                        <span class="badge-inline badge-low">Return Pending</span>
                                    @endif
                                    @if($isNear && !$isExpired)
                                        <span class="badge-inline badge-near">Expiring Soon</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>{{ $item->category ?? '—' }}</td>
                        <td class="{{ $isLow ? 'qty-low' : '' }}">{{ $item->quantity }}</td>
                        <td style="font-weight:600;color:#27ae60">RM {{ number_format($item->selling_price, 2) }}</td>
                        <td>{{ $item->reorder_point }}</td>
                        <td class="{{ $isExpired ? 'expiry-red' : '' }}">
                            {{ $item->expiry_date ? $item->expiry_date->format('Y-m-d') : '—' }}
                        </td>
                        <td>
                            @if($daysUntil === null)
                                —
                            @elseif($daysUntil < 0)
                                <span style="color:#c0392b;font-weight:600;">Expired {{ abs((int)$daysUntil) }} days ago</span>
                            @elseif($daysUntil <= 7)
                                <span style="color:#e67e22;font-weight:600;">{{ (int)$daysUntil }} days left</span>
                            @else
                                <span style="color:#27ae60;font-weight:600;">{{ (int)$daysUntil }} days left</span>
                            @endif
                        </td>
                        <td>
                            @if($item->supplier_id)
                                {{ optional($item->supplier)->name }}
                            @else
                                <span class="badge-inline badge-normal" style="margin-left:0;">Direct Purchase</span>
                            @endif
                        </td>
                        <td>
                            <div class="actions">
                                <a href="{{ route('staff.inventory.edit', $item) }}" class="btn btn-edit">✏ Edit</a>
                                <form action="{{ route('staff.inventory.destroy', $item) }}" method="POST" style="display:inline">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn btn-del" onclick="confirmDelete(this.closest('form'))">🗑 Delete</button>
                                </form>
                                @if(($item->isOutOfStock() || $isLow) && $item->supplier_id)
                                    <a href="{{ route('staff.po.create', ['item_id' => $item->id]) }}" class="btn btn-po">🛒 Create PO</a>
                                @endif
                                @if(!$isDamaged && !$isExpired)
                                    <button type="button" class="btn btn-edit" style="color:#d4870a;border-color:#fde0a0" onclick="markDamaged({{ $item->id }}, '{{ addslashes($item->name) }}', {{ max(1, (int) $item->quantity) }})">⚠ Flag Damaged</button>
                                @endif
                                @if($item->quantity > 0 && ($isExpired || $isDamaged) && !$hasPendingReturn && $item->supplier_id)
                                    <a href="{{ route('staff.rr.create', array_filter(['item_id' => $item->id, 'quantity' => $isDamaged ? max(0, (int) $item->damaged_quantity) : null])) }}" class="btn btn-rr">↩ Return</a>
                                @endif
                                @if($isDamaged && !$hasPendingReturn)
                                    <form action="{{ route('staff.inventory.unmarkDamaged', $item) }}" method="POST" style="display:inline">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn btn-secondary">Unflag</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="empty-state">
                            @if($keyword || $status || $category)
                                <div style="font-size:2rem;margin-bottom:12px">🔍</div>
                                No items match your search. <a href="{{ route('staff.inventory.index') }}">Clear filters</a>
                            @else
                                <div style="font-size:2rem;margin-bottom:12px">📦</div>
                                No items in inventory yet. <a href="{{ route('staff.inventory.create') }}">Add your first item</a>
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination-bar">
            <span>Showing {{ $items->firstItem() ?? 0 }} to {{ $items->lastItem() ?? 0 }} of {{ number_format($items->total()) }} entries</span>
            <div class="pagination-links">
                <a href="{{ $items->previousPageUrl() ?? '#' }}" class="page-btn {{ $items->onFirstPage() ? 'disabled' : '' }}">‹</a>
                @foreach($items->getUrlRange(1, $items->lastPage()) as $page => $url)
                    @if($page <= 3 || $page > $items->lastPage() - 1 || abs($page - $items->currentPage()) <= 1)
                        <a href="{{ $url }}" class="page-btn {{ $page == $items->currentPage() ? 'active' : '' }}">{{ $page }}</a>
                    @elseif($page == 4 && $items->currentPage() > 5)
                        <span class="page-btn disabled">…</span>
                    @endif
                @endforeach
                <a href="{{ $items->nextPageUrl() ?? '#' }}" class="page-btn {{ !$items->hasMorePages() ? 'disabled' : '' }}">›</a>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmDelete(form) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'This item will be permanently deleted.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#c0392b',
        cancelButtonColor: '#7a8fa8',
        confirmButtonText: 'Confirm Delete'
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
}

function applySearch() {
    const v = document.getElementById('topSearchInput').value;
    const url = new URL(window.location.href);
    url.searchParams.set('keyword', v);
    window.location.href = url.toString();
}

function markDamaged(itemId, itemName, availableQuantity) {
    Swal.fire({
        title: 'Report Damaged Item',
        html: `Flagging <b>${itemName}</b> as damaged.<br><br>Please enter the damaged quantity and reason.`,
        input: 'number',
        inputLabel: 'Damaged quantity',
        inputPlaceholder: 'Enter quantity',
        inputValue: 1,
        inputAttributes: {
            min: 1,
            max: availableQuantity,
            step: 1,
        },
        showCancelButton: true,
        confirmButtonText: 'Submit Flag',
        confirmButtonColor: '#d4870a',
        preConfirm: (value) => {
            const damagedQuantity = Number(value);
            if (!Number.isInteger(damagedQuantity) || damagedQuantity < 1) {
                Swal.showValidationMessage('Enter a whole number greater than zero.');
                return;
            }
            if (damagedQuantity > availableQuantity) {
                Swal.showValidationMessage(`Damaged quantity cannot exceed available stock (${availableQuantity}).`);
                return;
            }
            return damagedQuantity;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const damagedQuantity = Number(result.value);
            Swal.fire({
                title: 'Provide Damage Reason',
                input: 'text',
                inputPlaceholder: 'E.g., Crushed packaging, water damage...',
                showCancelButton: true,
                confirmButtonText: 'Confirm Flag',
                confirmButtonColor: '#d4870a',
                inputValidator: (value) => {
                    if (!value) {
                        return 'You need to write a reason!'
                    }
                }
            }).then((reasonResult) => {
                if (!reasonResult.isConfirmed || !reasonResult.value) {
                    return;
                }

                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/owner/items/${itemId}/mark-damaged`;

                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                const tokenInput = document.createElement('input');
                tokenInput.type = 'hidden';
                tokenInput.name = '_token';
                tokenInput.value = csrfToken;

                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'PATCH';

                const quantityInput = document.createElement('input');
                quantityInput.type = 'hidden';
                quantityInput.name = 'damaged_quantity';
                quantityInput.value = damagedQuantity;

                const reasonInput = document.createElement('input');
                reasonInput.type = 'hidden';
                reasonInput.name = 'damage_reason';
                reasonInput.value = reasonResult.value;

                form.appendChild(tokenInput);
                form.appendChild(methodInput);
                form.appendChild(quantityInput);
                form.appendChild(reasonInput);
                document.body.appendChild(form);
                form.submit();
            });
        }
    });
}
</script>
@endpush
