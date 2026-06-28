@extends('layouts.staff')

@section('title', 'My Profile – 22UniMart')

@push('styles')
<style>
    .page-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 32px; }
    .page-title  { font-size: 1.6rem; font-weight: 800; color: #0f2044; }
    .page-sub    { font-size: 0.85rem; color: #7a8fa8; margin-top: 4px; }

    .profile-layout { display: grid; grid-template-columns: 280px 1fr; gap: 24px; align-items: start; }

    /* Avatar card */
    .avatar-card { background: linear-gradient(135deg, #0f2044 0%, #1e3a6e 100%); border-radius: 16px; padding: 36px 28px; display: flex; flex-direction: column; align-items: center; gap: 16px; box-shadow: 0 8px 32px rgba(15,32,68,0.18); color: #fff; }
    .avatar-large { width: 88px; height: 88px; border-radius: 50%; background: rgba(255,255,255,0.15); border: 3px solid rgba(255,255,255,0.3); display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: 800; color: #fff; }
    .avatar-name  { font-size: 1.1rem; font-weight: 700; text-align: center; }
    .avatar-role  { font-size: 0.78rem; background: rgba(255,255,255,0.12); border-radius: 20px; padding: 4px 14px; color: rgba(255,255,255,0.8); }
    .avatar-email { font-size: 0.78rem; color: rgba(255,255,255,0.6); text-align: center; word-break: break-all; }
    .avatar-divider { width: 100%; height: 1px; background: rgba(255,255,255,0.12); }
    .avatar-stat { display: flex; flex-direction: column; align-items: center; gap: 2px; }
    .avatar-stat-val { font-size: 1.2rem; font-weight: 800; }
    .avatar-stat-lbl { font-size: 0.7rem; color: rgba(255,255,255,0.6); text-align: center; }
    .avatar-meta { font-size: 0.72rem; color: rgba(255,255,255,0.45); text-align: center; line-height: 1.6; }

    /* Form card */
    .form-card { background: #fff; border-radius: 16px; padding: 32px; box-shadow: 0 2px 12px rgba(15,32,68,0.07); }
    .card-header { display: flex; align-items: center; gap: 12px; margin-bottom: 28px; padding-bottom: 20px; border-bottom: 1px solid #f0f4f8; }
    .card-line   { width: 4px; height: 24px; background: linear-gradient(180deg, #0f2044, #4a90d9); border-radius: 2px; }
    .card-title  { font-size: 1.05rem; font-weight: 700; color: #0f2044; }
    .card-sub    { font-size: 0.78rem; color: #9daec5; margin-top: 2px; }

    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .form-group { display: flex; flex-direction: column; gap: 6px; }
    .form-group.full { grid-column: 1 / -1; }
    .form-label { font-size: 0.72rem; font-weight: 700; color: #5a6a85; text-transform: uppercase; letter-spacing: 0.6px; }
    .form-control { background: #f4f6fb; border: 1.5px solid transparent; border-radius: 8px; padding: 12px 16px; font-size: 0.9rem; font-family: 'Inter', sans-serif; color: #1a2744; transition: all 0.2s; outline: none; }
    .form-control:focus { background: #fff; border-color: #4a90d9; box-shadow: 0 0 0 3px rgba(74,144,217,0.1); }
    .form-control:disabled { background: #f8fafc; color: #9daec5; cursor: not-allowed; }
    .form-control::placeholder { color: #b0bec8; }
    .form-hint { font-size: 0.72rem; color: #9daec5; }

    .error-msg   { font-size: 0.72rem; color: #c0392b; font-weight: 600; }
    .success-msg { background: linear-gradient(135deg, #e8f8f0, #d0f0e5); color: #1d8348; border: 1px solid #a9dfbf; border-radius: 10px; padding: 14px 20px; font-size: 0.88rem; font-weight: 600; margin-bottom: 24px; display: flex; align-items: center; gap: 10px; }

    .form-actions { display: flex; align-items: center; gap: 14px; margin-top: 28px; padding-top: 24px; border-top: 1px solid #f0f4f8; }
    .btn-submit { background: linear-gradient(135deg, #0f2044, #1e3a6e); color: #fff; border: none; border-radius: 8px; padding: 12px 28px; font-size: 0.9rem; font-weight: 700; cursor: pointer; transition: all 0.2s; font-family: 'Inter', sans-serif; display: inline-flex; align-items: center; gap: 8px; }
    .btn-submit:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(15,32,68,0.3); }
    .btn-secondary { background: #f4f6fb; color: #3a4d6a; border: 1.5px solid #e2e8f0; border-radius: 8px; padding: 12px 22px; font-size: 0.88rem; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: all 0.15s; font-family: 'Inter', sans-serif; }
    .btn-secondary:hover { background: #e8edf5; border-color: #c8d4e0; }
</style>
@endpush

@section('breadcrumbs')
    <a href="{{ route('staff.dashboard') }}">Dashboard</a> › <span style="color:#0f2044;">My Profile</span>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <div class="page-title">My Profile</div>
            <div class="page-sub">Manage your personal information and account settings.</div>
        </div>
    </div>

    @if(session('success'))
        <div class="success-msg">✅ {{ session('success') }}</div>
    @endif

    <div class="profile-layout">

        {{-- Avatar / Info Card --}}
        <div class="avatar-card">
            <div class="avatar-large">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            <div class="avatar-name">{{ auth()->user()->name }}</div>
            <div class="avatar-role">{{ ucfirst(auth()->user()->role) }}</div>
            <div class="avatar-email">{{ auth()->user()->email }}</div>
            <div class="avatar-divider"></div>
            <div class="avatar-meta">
                Member since<br>
                {{ auth()->user()->created_at ? auth()->user()->created_at->format('M Y') : 'N/A' }}
            </div>
            <div class="avatar-divider"></div>
            <a href="{{ route('staff.password.change') }}" class="btn-secondary" style="width:100%;justify-content:center;background:rgba(255,255,255,0.1);border-color:rgba(255,255,255,0.15);color:#fff;">
                🔐 Change Password
            </a>
        </div>

        {{-- Form Card --}}
        <div class="form-card">
            <div class="card-header">
                <div class="card-line"></div>
                <div>
                    <div class="card-title">Account Information</div>
                    <div class="card-sub">Update your name, email and phone number</div>
                </div>
            </div>

            <form method="POST" action="{{ route('staff.profile.update') }}">
                @csrf
                @method('PUT')

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label" for="name">Full Name</label>
                        <input type="text" id="name" name="name" class="form-control" placeholder="Your full name"
                       value="{{ old('name', $user->name) }}" required>
                        @error('name')<div class="error-msg">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="role">Role</label>
                        <input type="text" id="role" class="form-control" value="{{ ucfirst(auth()->user()->role) }}" disabled>
                        <div class="form-hint">Read-only — system assigned</div>
                    </div>

                    <div class="form-group full">
                        <label class="form-label" for="email">Email Address</label>
                        <input type="email" id="email" name="email" class="form-control" placeholder="your@email.com"
                        value="{{ old('email', $user->email) }}" required>
                        @error('email')<div class="error-msg">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group full">
                        <label class="form-label" for="phone">Phone Number</label>
                        <input type="text" id="phone" name="phone" class="form-control" placeholder="+601X-XXXXXXX"
                               value="{{ old('phone', $user->phone) }}">
                        @error('phone')<div class="error-msg">{{ $message }}</div>@enderror
                    </div>

                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-submit">💾 Save Changes</button>
                    <a href="{{ route('staff.dashboard') }}" class="btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
