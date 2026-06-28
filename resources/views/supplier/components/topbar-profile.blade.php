<div class="topbar-profile" onclick="toggleProfileDropdown()" style="display:flex;align-items:center;gap:8px;position:relative;cursor:pointer;">
    <div class="avatar" style="width:36px;height:36px;border-radius:50%;background:#0f2044;color:#fff;display:flex;align-items:center;justify-content:center;font-size:0.85rem;font-weight:700;">
        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
    </div>
    <div>
        <div style="font-size:0.85rem;font-weight:600;color:#1a2744;">{{ auth()->user()->name }}</div>
        <div style="font-size:0.7rem;color:#9daec5;font-weight:normal;">Supplier</div>
    </div>
    <div class="profile-dropdown" id="profileDropdown" style="display:none;position:absolute;top:100%;right:0;background:#fff;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,0.1);padding:8px 0;min-width:150px;z-index:100;">
        <form action="{{ route('logout') }}" method="POST" style="display:contents;">
            @csrf
            <button type="submit" class="dropdown-item logout" style="border:none;background:none;width:100%;text-align:left;cursor:pointer;padding:8px 16px;font-size:0.85rem;color:#3a4d6a;">🚪 Logout</button>
        </form>
    </div>
</div>

<script>
    function toggleProfileDropdown() {
        const dropdown = document.getElementById('profileDropdown');
        dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
    }
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const profile = document.querySelector('.topbar-profile');
        const dropdown = document.getElementById('profileDropdown');
        if (profile && !profile.contains(event.target) && dropdown) {
            dropdown.style.display = 'none';
        }
    });
</script>
