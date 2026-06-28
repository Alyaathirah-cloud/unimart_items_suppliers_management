@extends('layouts.staff')

@section('title', 'Manage Suppliers – 22UniMart')

@push('styles')
<style>
    .filter-bar{display:flex;align-items:center;gap:12px;margin-bottom:20px;flex-wrap:wrap;}
    .filter-search{display:flex;align-items:center;gap:8px;background:#fff;border:1px solid #e2e8f0;border-radius:8px;padding:9px 14px;flex:1;min-width:220px;}
    .filter-search input{border:none;outline:none;font-family:'Inter',sans-serif;font-size:.85rem;color:#1a2744;width:100%;}
    .filter-search input::placeholder{color:#9daec5;}
    .table-card{background:#fff;border-radius:12px;box-shadow:0 1px 6px rgba(15,32,68,.07);overflow:hidden;}
    table{width:100%;border-collapse:collapse;}
    thead th{padding:12px 16px;font-size:.67rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#9daec5;text-align:left;background:#f8fafc;border-bottom:1px solid #edf2f7;white-space:nowrap;}
    tbody td{padding:14px 16px;font-size:.85rem;color:#1a2744;border-bottom:1px solid #f0f4f8;vertical-align:middle;}
    tbody tr:last-child td{border-bottom:none;}
    tbody tr:hover td{background:#fafbfd;}
    
    .supplier-name { font-weight: 600; color: #0f2044; }
    .items-badge { display: inline-flex; align-items: center; justify-content: center; background: #e8f0fb; color: #1d4ed8; border-radius: 20px; padding: 3px 12px; font-size: 0.75rem; font-weight: 700; }
    
    .actions{display:flex;gap:6px;flex-wrap:wrap;align-items:center;}
    .btn{display:inline-flex;align-items:center;gap:4px;padding:5px 12px;border-radius:6px;font-size:.75rem;font-weight:600;cursor:pointer;border:1px solid transparent;transition:all .15s;font-family:'Inter',sans-serif;text-decoration:none;white-space:nowrap;}
    .btn-view{background:#f0f4ff;color:#2563eb;border-color:#c7d7f9;}
    .btn-view:hover{background:#dbe8ff;}
    .btn-edit{background:#f0f4ff;color:#2563eb;border-color:#c7d7f9;}
    .btn-edit:hover{background:#dbe8ff;}
    .btn-del{background:#fdedec;color:#c0392b;border-color:#f5b7b1;}
    .btn-del:hover{background:#fad7d4;}

    .page-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 28px; }
    .page-title { font-size: 1.7rem; font-weight: 800; color: #0f2044; }
    .page-sub { font-size: .83rem; color: #7a8fa8; margin-top: 6px; max-width: 520px; line-height: 1.6; }
    .btn-add { background: #0f2044; color: #fff; border: none; border-radius: 8px; padding: 12px 22px; font-size: .88rem; font-weight: 600; cursor: pointer; font-family: 'Inter', sans-serif; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; transition: background .15s; white-space: nowrap; }
    .btn-add:hover { background: #182e5e; }
    
    .stat-row { display: grid; grid-template-columns: repeat(3,1fr); gap:20px; margin-bottom:28px; }
    .stat-card { background:#fff;border-radius:12px;padding:22px 24px;box-shadow:0 1px 6px rgba(15,32,68,.07);display:flex;justify-content:space-between;align-items:flex-start; }
    .stat-label{font-size:.67rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#9daec5;}
    .stat-value{font-size:2rem;font-weight:800;color:#0f2044;margin:8px 0 4px;line-height:1;}
    .stat-hint{font-size:.75rem;color:#27ae60;font-weight:500;}
    .ic-blue{background:#e8f0fb;color:#1d4ed8;}
    .ic-orange{background:#fef3e2;color:#d4870a;}
    .ic-purple{background:#f3e8ff;color:#7c3aed;}
    .stat-icon-wrap{width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.1rem;flex-shrink:0;}
    
    .flash{padding:12px 20px;border-radius:8px;font-size:.88rem;margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;}
    .flash-success{background:#e8f8f0;color:#1d8348;border:1px solid #a9dfbf;}
    .flash-error{background:#fdedec;color:#c0392b;border:1px solid #f5b7b1;}
    .flash-close{background:none;border:none;cursor:pointer;color:inherit;font-size:1rem;}
    .empty-state{padding:48px;text-align:center;color:#9daec5;}
    .empty-state a{color:#3a7bd5;text-decoration:none;font-weight:600;}

    /* View modal overlay */
    .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(10,20,40,0.4); z-index: 100; align-items: center; justify-content: center; }
    .modal-overlay.open { display: flex; }
    .modal-box { background: #fff; border-radius: 14px; padding: 32px; width: 100%; max-width: 480px; box-shadow: 0 20px 60px rgba(10,20,40,0.25); }
    .modal-title { font-size: 1.1rem; font-weight: 700; color: #0f2044; margin-bottom: 20px; }
    .modal-row { display: flex; padding: 10px 0; border-bottom: 1px solid #f0f4f8; }
    .modal-row:last-of-type { border-bottom: none; }
    .modal-label { font-size: 0.75rem; font-weight: 700; color: #9daec5; text-transform: uppercase; letter-spacing: 0.5px; width: 140px; flex-shrink: 0; }
    .modal-value { font-size: 0.88rem; color: #1a2744; font-weight: 500; }
    .modal-close { margin-top: 24px; width: 100%; padding: 11px; background: #0f2044; color: #fff; border: none; border-radius: 8px; font-size: 0.88rem; font-weight: 600; cursor: pointer; font-family: 'Inter', sans-serif; }
    .modal-close:hover { background: #182e5e; }
</style>
@endpush

@section('topbar')
    <span style="font-size:1rem;font-weight:700;color:#0f2044;margin-right:20px;">Suppliers</span>
    <div class="filter-search" style="max-width:340px; margin: 0; padding: 8px 14px;">
        <svg width="14" height="14" fill="none" stroke="#9daec5" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
        <input type="text" placeholder="Search suppliers..." id="topSearchInput">
    </div>
    <div class="topbar-right">
        <a href="{{ route('staff.notif.index') }}" class="icon-btn" style="text-decoration:none;">🔔</a>
        @include('owner.components.topbar-profile')
    </div>
@endsection

@section('content')
    @php
        use App\Models\Supplier;
        use App\Models\Item;

        $totalSuppliers = Supplier::count();
        $suppliersWithItems = Supplier::has('items')->count();
        $totalItemsSupplied = Item::whereNotNull('supplier_id')->count();
    @endphp

    <div class="page-header">
        <div>
            <div class="page-title">Manage Suppliers</div>
            <div class="page-sub">View, edit and manage your supplier accounts.</div>
        </div>
        <a href="{{ route('staff.suppliers.create') }}" class="btn-add">+ Add Supplier</a>
    </div>

    @if(session('success'))
        <div class="flash flash-success" id="flash-msg">
            ✅ {{ session('success') }}
            <button class="flash-close" onclick="document.getElementById('flash-msg').style.display='none'">✕</button>
        </div>
    @endif
    @if(session('error'))
        <div class="flash flash-error" id="flash-error">
            ❌ {{ session('error') }}
            <button class="flash-close" onclick="document.getElementById('flash-error').style.display='none'">✕</button>
        </div>
    @endif

    <div class="stat-row">
        <div class="stat-card">
            <div>
                <div class="stat-label">Total Suppliers</div>
                <div class="stat-value">{{ number_format($totalSuppliers) }}</div>
                <div class="stat-hint">All registered suppliers</div>
            </div>
            <div class="stat-icon-wrap ic-blue">🏢</div>
        </div>
        <div class="stat-card">
            <div>
                <div class="stat-label">Active Suppliers</div>
                <div class="stat-value">{{ number_format($suppliersWithItems) }}</div>
                <div class="stat-hint">Suppliers with items</div>
            </div>
            <div class="stat-icon-wrap ic-orange">✅</div>
        </div>
        <div class="stat-card">
            <div>
                <div class="stat-label">Items Supplied</div>
                <div class="stat-value">{{ number_format($totalItemsSupplied) }}</div>
                <div class="stat-hint">Total inventory linked</div>
            </div>
            <div class="stat-icon-wrap ic-purple">📦</div>
        </div>
    </div>

    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Supplier Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Supplied Items</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($suppliers as $i => $supplier)
                    <tr>
                        <td style="color:#9daec5;font-size:0.8rem">{{ $i + 1 }}</td>
                        <td class="supplier-name">{{ $supplier->name }}</td>
                        <td style="color:#5a6a85">{{ $supplier->contact_email ?? '—' }}</td>
                        <td style="color:#5a6a85">{{ $supplier->contact_phone ?? '—' }}</td>
                        <td><span class="items-badge">{{ $supplier->items()->count() }}</span></td>
                        <td>
                            <div class="actions">
                                <button class="btn btn-view" onclick="openModal({{ $supplier->id }}, '{{ addslashes($supplier->name) }}', '{{ addslashes($supplier->contact_email ?? '—') }}', '{{ addslashes($supplier->contact_phone ?? '—') }}', {{ $supplier->items()->count() }})">
                                    👁 View
                                </button>
                                <a href="{{ route('staff.suppliers.edit', $supplier) }}" class="btn btn-edit">✏ Edit</a>
                                <form action="{{ route('staff.suppliers.destroy', $supplier) }}" method="POST" style="display:inline">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn btn-del" onclick="confirmDelete(this.closest('form'))">🗑 Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="empty-state">
                            No suppliers yet. <a href="{{ route('staff.suppliers.create') }}">Add one now</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- View Modal -->
    <div class="modal-overlay" id="viewModal" onclick="closeModal(event)">
        <div class="modal-box" onclick="event.stopPropagation()">
            <div class="modal-title">Supplier Details</div>
            <div class="modal-row"><span class="modal-label">Name</span><span class="modal-value" id="m-name"></span></div>
            <div class="modal-row"><span class="modal-label">Email</span><span class="modal-value" id="m-email"></span></div>
            <div class="modal-row"><span class="modal-label">Phone</span><span class="modal-value" id="m-phone"></span></div>
            <div class="modal-row"><span class="modal-label">Total Items</span><span class="modal-value" id="m-items"></span></div>
            <button class="modal-close" onclick="document.getElementById('viewModal').classList.remove('open')">Close</button>
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

function openModal(id, name, email, phone, items) {
    document.getElementById('m-name').textContent  = name;
    document.getElementById('m-email').textContent = email;
    document.getElementById('m-phone').textContent = phone;
    document.getElementById('m-items').textContent = items + ' item(s)';
    document.getElementById('viewModal').classList.add('open');
}

function closeModal(e) {
    if (e.target === document.getElementById('viewModal')) {
        document.getElementById('viewModal').classList.remove('open');
    }
}
</script>
@endpush
