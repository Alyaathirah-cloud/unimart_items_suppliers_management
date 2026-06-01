@extends('layouts.owner')

@section('title', 'Change Password – 22UniMart')

@section('breadcrumbs')
    <a href="{{ route('owner.dashboard') }}">Dashboard</a> › <span style="color:#0f2044;">Change Password</span>
@endsection

@section('content')
    <div class="page-title">Change Password</div>
    <div class="page-sub">Update your password to keep your account secure.</div>

    @if(session('success'))
        <div class="success-msg">{{ session('success') }}</div>
    @endif

    <div class="form-card">
        <div class="card-header">
            <div class="card-line"></div>
            <div class="card-title">Password Settings</div>
        </div>

        <form method="POST" action="{{ route('owner.password.update') }}">
            @csrf
            @method('PUT')

            <div class="form-row">
                <div class="form-group" style="flex:1;">
                    <label class="form-label" for="current_password">Current Password</label>
                    <input type="password" id="current_password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" required>
                    @error('current_password')<div class="error-msg">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group" style="flex:1;">
                    <label class="form-label" for="new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password" class="form-control @error('new_password') is-invalid @enderror" required>
                    @error('new_password')<div class="error-msg">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group" style="flex:1;">
                    <label class="form-label" for="new_password_confirmation">Confirm New Password</label>
                    <input type="password" id="new_password_confirmation" name="new_password_confirmation" class="form-control" required>
                </div>
            </div>

            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px; margin-bottom: 20px; font-size: 0.85rem; color: #5a6a85; line-height: 1.5;">
                <strong style="color:#0f2044;">Password Requirements:</strong><br>
                • Minimum 8 characters<br>
                • Use a mix of uppercase and lowercase letters<br>
                • Include numbers and special characters
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit">Update Password</button>
            </div>
        </form>
    </div>
@endsection
