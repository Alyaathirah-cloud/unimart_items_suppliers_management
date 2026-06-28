<div class="topbar-profile" onclick="toggleProfileDropdown()">
    <div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
    <div>
        <div style="font-size:0.85rem;font-weight:600;color:#1a2744">{{ auth()->user()->name }}</div>
        <div style="font-size:0.7rem;color:#9daec5;font-weight:normal;">Owner</div>
    </div>
    <div class="profile-dropdown" id="profileDropdown">
        <a href="{{ route('owner.profile.edit') ?? '#' }}" class="dropdown-item">👤 My Profile</a>
        <a href="{{ route('owner.password.change') ?? '#' }}" class="dropdown-item">🔐 Change Password</a>
        <form action="{{ route('logout') }}" method="POST" style="display:contents;">
            @csrf
            <button type="submit" class="dropdown-item logout" style="border:none;background:none;width:100%;text-align:left;cursor:pointer;">🚪 Logout</button>
        </form>
    </div>
</div>
