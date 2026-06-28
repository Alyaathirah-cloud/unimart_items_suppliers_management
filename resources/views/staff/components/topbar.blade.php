<!-- Reusable Topbar Component for Owner Pages -->
<style>
.topbar { background: #fff; padding: 0 32px; height: 56px; display: flex; align-items: center; gap: 16px; position: sticky; top: 0; z-index: 10; border-bottom: 1px solid #e2e8f0; }
.breadcrumbs { font-size: 0.75rem; font-weight: 700; color: #7a8fa8; letter-spacing: 1px; text-transform: uppercase; }
.breadcrumbs a { color: inherit; text-decoration: none; }
.breadcrumbs a:hover { color: #0f2044; }
.topbar-right { margin-left: auto; display: flex; align-items: center; gap: 20px; }
.icon-btn { width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; cursor: pointer; transition: all 0.15s; }
.icon-btn:hover { background: #f0f3f9; border-radius: 6px; }
.avatar { width: 32px; height: 32px; border-radius: 50%; background: #0f2044; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 0.75rem; font-weight: 700; }
.topbar-profile { position: relative; cursor: pointer; display: flex; align-items: center; gap: 8px; }
.topbar-profile > div:nth-child(2) { display: flex; flex-direction: column; }
.topbar-profile-name { font-size: 0.85rem; font-weight: 600; color: #1a2744; }
.topbar-profile-role { font-size: 0.72rem; color: #9daec5; }
.profile-dropdown { position: absolute; top: 100%; right: 0; background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; box-shadow: 0 4px 16px rgba(15,32,68,0.12); min-width: 200px; margin-top: 8px; display: none; z-index: 1000; }
.profile-dropdown.show { display: block; }
.dropdown-item { display: flex; align-items: center; gap: 10px; padding: 12px 16px; font-size: 0.85rem; color: #334155; text-decoration: none; border-bottom: 1px solid #f1f5f9; transition: background 0.15s; }
.dropdown-item:last-child { border-bottom: none; }
.dropdown-item:hover { background: #f8fafc; color: #0f2044; }
.dropdown-item.logout { color: #c0392b; }
.dropdown-item.logout:hover { background: #fee2e2; }
</style>

<div class="topbar">
    <div class="breadcrumbs">{{ $breadcrumb ?? 'Dashboard' }}</div>
    <div class="topbar-right">
        <a href="{{ route('staff.notif.index') }}" class="icon-btn" style="text-decoration:none;">🔔</a>
        @include('staff.components.topbar-profile')
    </div>
</div>

<script>
function toggleProfileDropdown() {
    const dropdown = document.getElementById('profileDropdown');
    dropdown.classList.toggle('show');
}

document.addEventListener('click', function(event) {
    const profile = event.target.closest('.topbar-profile');
    const dropdown = document.getElementById('profileDropdown');
    if (!profile && dropdown && !dropdown.contains(event.target)) {
        dropdown.classList.remove('show');
    }
});
</script>
