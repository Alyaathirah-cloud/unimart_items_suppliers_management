<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Suppliers – 22UniMart</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: #eef2f7; display: flex; min-height: 100vh; }

        /* Sidebar (same as owner dashboard) */
        .sidebar { width: 210px; flex-shrink: 0; background: #0f2044; color: #fff; display: flex; flex-direction: column; padding: 0 0 24px 0; position: fixed; top: 0; left: 0; height: 100vh; }
        .sidebar-brand { padding: 20px 20px 4px; display: flex; align-items: center; gap: 12px; }
        .brand-square { width: 32px; height: 32px; background: #fff; color: #0f2044; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 0.9rem; font-weight: 800; flex-shrink: 0; }
        .brand-text .brand-name { font-size: 0.9rem; font-weight: 800; }
        .brand-text .brand-sub  { font-size: 0.68rem; color: #8ca0c0; }
        .sidebar-nav { flex: 1; margin-top: 20px; }
        .nav-item { display: flex; align-items: center; gap: 12px; padding: 11px 20px; font-size: 0.88rem; font-weight: 500; color: #8ca0c0; cursor: pointer; text-decoration: none; transition: all 0.15s; border-left: 3px solid transparent; }
        .nav-item:hover { color: #fff; background: rgba(255,255,255,0.06); }
        .nav-item.active { color: #fff; background: rgba(255,255,255,0.1); border-left-color: #4a90d9; }
        .sidebar-bottom { padding: 0 20px; display: flex; flex-direction: column; gap: 8px; }
        .btn-report { background: #1e3a6e; color: #fff; border: none; border-radius: 8px; padding: 12px 16px; font-size: 0.85rem; font-weight: 600; cursor: pointer; width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px; transition: background 0.15s; text-decoration: none; }
        .btn-report:hover { background: #2a4f8f; }
        .sidebar-link { display: flex; align-items: center; gap: 10px; color: #8ca0c0; font-size: 0.82rem; text-decoration: none; padding: 6px 0; }
        .sidebar-link:hover { color: #fff; }

        /* Main */
        .main { margin-left: 210px; flex: 1; display: flex; flex-direction: column; }
        .topbar { background: #fff; border-bottom: 1px solid #e2e8f0; padding: 0 32px; height: 56px; display: flex; align-items: center; gap: 16px; position: sticky; top: 0; z-index: 10; }
        .topbar-right { margin-left: auto; display: flex; align-items: center; gap: 16px; }
        .avatar { width: 36px; height: 36px; border-radius: 50%; background: #0f2044; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 0.85rem; font-weight: 700; }
        .topbar-profile { position: relative; cursor: pointer; display: flex; align-items: center; gap: 8px; }
        .profile-dropdown { position: absolute; top: 100%; right: 0; background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; box-shadow: 0 4px 16px rgba(15,32,68,0.12); min-width: 200px; margin-top: 8px; display: none; z-index: 1000; }
        .profile-dropdown.show { display: block; }
        .dropdown-item { display: flex; align-items: center; gap: 10px; padding: 12px 16px; font-size: 0.85rem; color: #334155; text-decoration: none; border-bottom: 1px solid #f1f5f9; transition: background 0.15s; }
        .dropdown-item:last-child { border-bottom: none; }
        .dropdown-item:hover { background: #f8fafc; color: #0f2044; }
        .dropdown-item.logout { color: #c0392b; }
        .dropdown-item.logout:hover { background: #fee2e2; }
        .icon-btn { background: none; border: none; cursor: pointer; color: #5a6a85; font-size: 1.1rem; }

        /* Content */
        .content { padding: 32px; }
        .page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; }
        .page-title { font-size: 1.5rem; font-weight: 800; color: #0f2044; }
        .btn-add { background: #0f2044; color: #fff; border: none; border-radius: 8px; padding: 10px 20px; font-size: 0.88rem; font-weight: 600; cursor: pointer; font-family: 'Inter', sans-serif; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; transition: background 0.15s; }
        .btn-add:hover { background: #182e5e; }

        /* Flash */
        .flash { padding: 12px 20px; border-radius: 8px; font-size: 0.88rem; margin-bottom: 20px; }
        .flash-success { background: #e8f8f0; color: #1d8348; border: 1px solid #a9dfbf; }
        .flash-error   { background: #fdedec; color: #c0392b; border: 1px solid #f5b7b1; }

        /* Card */
        .card { background: #fff; border-radius: 12px; box-shadow: 0 1px 6px rgba(15,32,68,0.07); overflow: hidden; }

        /* Table */
        table { width: 100%; border-collapse: collapse; }
        thead th { padding: 12px 20px; font-size: 0.7rem; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; color: #9daec5; text-align: left; background: #f8fafc; border-bottom: 1px solid #edf2f7; }
        tbody td { padding: 14px 20px; font-size: 0.88rem; color: #1a2744; border-bottom: 1px solid #f0f4f8; vertical-align: middle; }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr:hover td { background: #fafbfd; }
        .supplier-name { font-weight: 600; }
        .items-badge { display: inline-flex; align-items: center; justify-content: center; background: #e8f0fb; color: #1d4ed8; border-radius: 20px; padding: 3px 12px; font-size: 0.75rem; font-weight: 700; }

        /* Action buttons */
        .actions { display: flex; gap: 8px; flex-wrap: nowrap; }
        .btn { display: inline-flex; align-items: center; gap: 5px; padding: 6px 14px; border-radius: 7px; font-size: 0.78rem; font-weight: 600; cursor: pointer; border: 1px solid transparent; transition: all 0.15s; font-family: 'Inter', sans-serif; text-decoration: none; white-space: nowrap; }
        .btn-view   { background: #f0f4ff; color: #2563eb; border-color: #c7d7f9; }
        .btn-view:hover { background: #dbe8ff; }
        .btn-edit   { background: #e8f8f0; color: #1d8348; border-color: #a9dfbf; }
        .btn-edit:hover { background: #d0f0e0; }
        .btn-delete { background: #fdedec; color: #c0392b; border-color: #f5b7b1; }
        .btn-delete:hover { background: #fad7d4; }
        .empty-state { padding: 40px; text-align: center; color: #9daec5; font-size: 0.9rem; }

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

<!-- Main -->
<div class="main">
    <div class="topbar">
        <span style="font-size:1rem;font-weight:700;color:#0f2044">Suppliers</span>
        <div class="topbar-right">
            <a href="{{ route('owner.notifications.index') }}" class="icon-btn" style="text-decoration:none;">🔔</a>
            <div class="topbar-profile" onclick="toggleProfileDropdown()">
                <div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <div>
                    <div style="font-size:0.85rem;font-weight:600;color:#1a2744">{{ auth()->user()->name }}</div>
                    <div style="font-size:0.72rem;color:#9daec5;">Owner</div>
                </div>
                <div class="profile-dropdown" id="profileDropdown">
                    <a href="{{ route('owner.profile.edit') }}" class="dropdown-item">👤 My Profile</a>
                    <a href="{{ route('owner.password.change') }}" class="dropdown-item">🔐 Change Password</a>
                    <form action="{{ route('logout') }}" method="POST" style="display:contents;">
                        @csrf
                        <button type="submit" class="dropdown-item logout" style="border:none;background:none;width:100%;text-align:left;cursor:pointer;">🚪 Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="page-header">
            <div>
                <div class="page-title">Manage Suppliers</div>
                <div style="font-size:0.85rem;color:#7a8fa8;margin-top:4px">View, edit and manage your supplier accounts.</div>
            </div>
            <a href="{{ route('owner.suppliers.create') }}" class="btn-add">+ Add Supplier</a>
        </div>

        @if(session('success'))
            <div class="flash flash-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="flash flash-error">{{ session('error') }}</div>
        @endif

        <div class="card">
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
                                    <a href="{{ route('owner.suppliers.edit', $supplier) }}" class="btn btn-edit">✏ Edit</a>
                                    <form action="{{ route('owner.suppliers.destroy', $supplier) }}" method="POST" style="display:inline">
                                        @csrf @method('DELETE')
                                        <button type="button" class="btn btn-delete" onclick="confirmDelete(this.closest('form'))">🗑 Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty-state">
                                No suppliers yet. <a href="{{ route('owner.suppliers.create') }}" style="color:#3a7bd5">Add one now</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
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

function toggleProfileDropdown() {
    const d = document.getElementById('profileDropdown');
    if (d) d.classList.toggle('show');
}
document.addEventListener('click', function(e) {
    const p = e.target.closest('.topbar-profile');
    const d = document.getElementById('profileDropdown');
    if (!p && d) d.classList.remove('show');
});
</script>

</body>
</html>
