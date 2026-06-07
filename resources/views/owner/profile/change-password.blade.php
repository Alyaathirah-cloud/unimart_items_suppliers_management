@extends('layouts.owner')

@section('title', 'Change Password – 22UniMart')

@push('styles')
<style>
    .page-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 32px; }
    .page-title  { font-size: 1.6rem; font-weight: 800; color: #0f2044; }
    .page-sub    { font-size: 0.85rem; color: #7a8fa8; margin-top: 4px; }

    .pw-layout { display: grid; grid-template-columns: 280px 1fr; gap: 24px; align-items: start; }

    /* Side info card */
    .info-sidebar { background: linear-gradient(135deg, #0f2044 0%, #1e3a6e 100%); border-radius: 16px; padding: 32px 24px; display: flex; flex-direction: column; gap: 20px; box-shadow: 0 8px 32px rgba(15,32,68,0.18); }
    .sidebar-icon  { width: 56px; height: 56px; border-radius: 14px; background: rgba(255,255,255,0.12); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
    .sidebar-heading { font-size: 1rem; font-weight: 700; color: #fff; }
    .sidebar-text    { font-size: 0.8rem; color: rgba(255,255,255,0.6); line-height: 1.6; }
    .sidebar-divider { height: 1px; background: rgba(255,255,255,0.12); }
    .req-list { list-style: none; display: flex; flex-direction: column; gap: 10px; }
    .req-item { display: flex; align-items: center; gap: 10px; font-size: 0.8rem; color: rgba(255,255,255,0.75); }
    .req-dot  { width: 6px; height: 6px; border-radius: 50%; background: #4a90d9; flex-shrink: 0; }
    .back-link { display: flex; align-items: center; gap: 8px; font-size: 0.8rem; color: rgba(255,255,255,0.55); text-decoration: none; transition: color 0.15s; margin-top: 4px; }
    .back-link:hover { color: rgba(255,255,255,0.9); }

    /* Form card */
    .form-card    { background: #fff; border-radius: 16px; padding: 32px; box-shadow: 0 2px 12px rgba(15,32,68,0.07); }
    .card-header  { display: flex; align-items: center; gap: 12px; margin-bottom: 28px; padding-bottom: 20px; border-bottom: 1px solid #f0f4f8; }
    .card-line    { width: 4px; height: 24px; background: linear-gradient(180deg, #0f2044, #4a90d9); border-radius: 2px; }
    .card-title   { font-size: 1.05rem; font-weight: 700; color: #0f2044; }
    .card-sub     { font-size: 0.78rem; color: #9daec5; margin-top: 2px; }

    .form-group   { display: flex; flex-direction: column; gap: 6px; margin-bottom: 20px; }
    .form-label   { font-size: 0.72rem; font-weight: 700; color: #5a6a85; text-transform: uppercase; letter-spacing: 0.6px; }
    .input-wrap   { position: relative; }
    .form-control { background: #f4f6fb; border: 1.5px solid transparent; border-radius: 8px; padding: 12px 44px 12px 16px; font-size: 0.9rem; font-family: 'Inter', sans-serif; color: #1a2744; transition: all 0.2s; outline: none; width: 100%; }
    .form-control:focus { background: #fff; border-color: #4a90d9; box-shadow: 0 0 0 3px rgba(74,144,217,0.1); }
    .form-control::placeholder { color: #b0bec8; }
    .toggle-eye   { position: absolute; right: 14px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; font-size: 1rem; color: #9daec5; padding: 0; line-height: 1; }
    .toggle-eye:hover { color: #0f2044; }

    .error-msg    { font-size: 0.72rem; color: #c0392b; font-weight: 600; }
    .success-msg  { background: linear-gradient(135deg, #e8f8f0, #d0f0e5); color: #1d8348; border: 1px solid #a9dfbf; border-radius: 10px; padding: 14px 20px; font-size: 0.88rem; font-weight: 600; margin-bottom: 24px; display: flex; align-items: center; gap: 10px; }

    /* Strength meter */
    .strength-bar   { height: 4px; border-radius: 2px; background: #e2e8f0; margin-top: 8px; overflow: hidden; }
    .strength-fill  { height: 100%; border-radius: 2px; transition: width 0.3s, background 0.3s; width: 0%; }
    .strength-label { font-size: 0.7rem; color: #9daec5; margin-top: 4px; }

    .form-actions { display: flex; align-items: center; gap: 14px; margin-top: 28px; padding-top: 24px; border-top: 1px solid #f0f4f8; }
    .btn-submit   { background: linear-gradient(135deg, #0f2044, #1e3a6e); color: #fff; border: none; border-radius: 8px; padding: 12px 28px; font-size: 0.9rem; font-weight: 700; cursor: pointer; transition: all 0.2s; font-family: 'Inter', sans-serif; display: inline-flex; align-items: center; gap: 8px; }
    .btn-submit:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(15,32,68,0.3); }
    .btn-secondary { background: #f4f6fb; color: #3a4d6a; border: 1.5px solid #e2e8f0; border-radius: 8px; padding: 12px 22px; font-size: 0.88rem; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: all 0.15s; font-family: 'Inter', sans-serif; }
    .btn-secondary:hover { background: #e8edf5; }
</style>
@endpush

@section('breadcrumbs')
    <a href="{{ route('owner.dashboard') }}">Dashboard</a> › <span style="color:#0f2044;">Change Password</span>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <div class="page-title">Change Password</div>
            <div class="page-sub">Update your password to keep your account secure.</div>
        </div>
    </div>

    @if(session('success'))
        <div class="success-msg">✅ {{ session('success') }}</div>
    @endif

    <div class="pw-layout">

        {{-- Sidebar info --}}
        <div class="info-sidebar">
            <div class="sidebar-icon">🔐</div>
            <div>
                <div class="sidebar-heading">Password Security</div>
                <div class="sidebar-text">Choose a strong password to protect your account from unauthorised access.</div>
            </div>
            <div class="sidebar-divider"></div>
            <div>
                <div class="sidebar-text" style="color:rgba(255,255,255,0.5);font-size:0.7rem;text-transform:uppercase;letter-spacing:0.8px;margin-bottom:12px;">Requirements</div>
                <ul class="req-list">
                    <li class="req-item"><span class="req-dot"></span>Minimum 8 characters</li>
                    <li class="req-item"><span class="req-dot"></span>Mix of uppercase &amp; lowercase</li>
                    <li class="req-item"><span class="req-dot"></span>At least one number</li>
                    <li class="req-item"><span class="req-dot"></span>At least one special character</li>
                </ul>
            </div>
            <div class="sidebar-divider"></div>
            <a href="{{ route('owner.profile.edit') }}" class="back-link">← Back to My Profile</a>
        </div>

        {{-- Form card --}}
        <div class="form-card">
            <div class="card-header">
                <div class="card-line"></div>
                <div>
                    <div class="card-title">Password Settings</div>
                    <div class="card-sub">Enter your current password then set a new one</div>
                </div>
            </div>

            <form method="POST" action="{{ route('owner.password.update') }}">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label class="form-label" for="current_password">Current Password</label>
                    <div class="input-wrap">
                        <input type="password" id="current_password" name="current_password"
                               class="form-control" placeholder="Enter your current password" required>
                        <button type="button" class="toggle-eye" onclick="togglePw('current_password',this)">👁</button>
                    </div>
                    @error('current_password')<div class="error-msg">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="new_password">New Password</label>
                    <div class="input-wrap">
                        <input type="password" id="new_password" name="new_password"
                               class="form-control" placeholder="Minimum 8 characters" required oninput="checkStrength(this.value)">
                        <button type="button" class="toggle-eye" onclick="togglePw('new_password',this)">👁</button>
                    </div>
                    <div class="strength-bar"><div class="strength-fill" id="strength-fill"></div></div>
                    <div class="strength-label" id="strength-label">Enter a new password</div>
                    @error('new_password')<div class="error-msg">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="new_password_confirmation">Confirm New Password</label>
                    <div class="input-wrap">
                        <input type="password" id="new_password_confirmation" name="new_password_confirmation"
                               class="form-control" placeholder="Re-enter your new password" required>
                        <button type="button" class="toggle-eye" onclick="togglePw('new_password_confirmation',this)">👁</button>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-submit">🔒 Update Password</button>
                    <a href="{{ route('owner.profile.edit') }}" class="btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
function togglePw(id, btn) {
    const input = document.getElementById(id);
    const visible = input.type === 'text';
    input.type = visible ? 'password' : 'text';
    btn.textContent = visible ? '👁' : '🙈';
}

function checkStrength(val) {
    const fill = document.getElementById('strength-fill');
    const label = document.getElementById('strength-label');
    let score = 0;
    if (val.length >= 8) score++;
    if (/[A-Z]/.test(val) && /[a-z]/.test(val)) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;
    const levels = [
        { pct:'0%',   color:'#e2e8f0', text:'Enter a new password' },
        { pct:'25%',  color:'#e74c3c', text:'Weak password' },
        { pct:'50%',  color:'#f39c12', text:'Fair password' },
        { pct:'75%',  color:'#f1c40f', text:'Good password' },
        { pct:'100%', color:'#27ae60', text:'Strong password ✓' },
    ];
    fill.style.width    = levels[score].pct;
    fill.style.background = levels[score].color;
    label.textContent   = levels[score].text;
    label.style.color   = score > 0 ? levels[score].color : '#9daec5';
}
</script>
@endpush
