@extends('layouts.owner')

@section('title', 'Reset Password – 22UniMart')

@push('styles')
<style>
    .content-center { display: flex; flex-direction: column; align-items: center; max-width: 600px; margin: 0 auto; width: 100%; }
    .page-title { font-size: 1.6rem; font-weight: 800; color: #0f2044; margin-bottom: 24px; text-align: center; }
    .form-card { background: #fff; border-radius: 12px; box-shadow: 0 1px 15px rgba(15,32,68,0.03); padding: 32px; width: 100%; }
    .form-group { margin-bottom: 20px; }
    .form-label { display: block; font-size: 0.85rem; font-weight: 700; color: #5a6a85; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px; }
    .form-control { width: 100%; background: #f4f6fb; padding: 12px 16px; border: 1px solid transparent; border-radius: 6px; font-size: 0.95rem; font-family: 'Inter', sans-serif; box-sizing: border-box; color: #1a2744; transition: all 0.2s; outline: none; }
    .form-control:focus { background: #fff; border-color: #4a90d9; box-shadow: 0 0 0 3px rgba(74, 144, 217, 0.1); }
    .text-danger { color: #c0392b; font-size: 0.8rem; margin-top: 4px; display: block; font-weight: 500; }
    .btn-primary { background: #0f2044; color: #fff; border: none; padding: 12px 24px; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 0.9rem; transition: background 0.15s; font-family: 'Inter', sans-serif; }
    .btn-primary:hover { background: #182e5e; }
    .btn-discard { background: #e2e8f0; color: #3a4d6a; border: none; border-radius: 6px; padding: 12px 24px; font-size: 0.9rem; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-flex; transition: background 0.15s; }
    .btn-discard:hover { background: #cbd5e1; }
</style>
@endpush

@section('breadcrumbs')
    <a href="{{ route('owner.dashboard') }}">Dashboard</a> › 
    <a href="{{ route('owner.staff.index') }}">Staff</a> › 
    <span style="color:#0f2044;">Reset Password</span>
@endsection

@section('content')
<div class="content-center">
    <div class="page-title">Reset Password for: {{ $user->name }}</div>

    <div class="form-card">
        <form action="{{ route('owner.staff.reset-password.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Hidden username field to help password managers understand this is for a different user -->
            <input type="text" name="username" value="{{ $user->email }}" autocomplete="username" style="display: none;" aria-hidden="true">

            <div class="form-group">
                <label class="form-label" for="password">New Password</label>
                <input type="password" id="password" name="password" class="form-control" required autocomplete="new-password">
                <div style="font-size: 0.8rem; color: #9daec5; margin-top: 4px;">Min 8 characters, must include letters and numbers</div>
                @error('password')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="password_confirmation">Confirm New Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required autocomplete="new-password">
            </div>

            <div style="display: flex; gap: 16px; align-items: center; justify-content: center; margin-top: 24px;">
                <button type="submit" class="btn-primary">Reset Password</button>
                <a href="{{ route('owner.staff.index') }}" class="btn-discard">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
