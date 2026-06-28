@extends('layouts.owner')

@section('title', 'Create Staff – 22UniMart')

@push('styles')
<style>
    .page-header { margin-bottom: 28px; }
    .page-title { font-size: 1.6rem; font-weight: 800; color: #0f2044; }
    .form-card { background: #fff; border-radius: 12px; box-shadow: 0 1px 6px rgba(15,32,68,0.07); padding: 24px; max-width: 600px; }
    .form-group { margin-bottom: 20px; }
    .form-label { display: block; font-size: 0.85rem; font-weight: 700; color: #0f2044; margin-bottom: 8px; }
    .form-control { width: 100%; padding: 10px 14px; border: 1px solid #d1dce8; border-radius: 8px; font-size: 0.95rem; font-family: 'Inter', sans-serif; box-sizing: border-box; }
    .form-control:focus { border-color: #3a7bd5; outline: none; }
    .text-danger { color: #e74c3c; font-size: 0.8rem; margin-top: 4px; display: block; }
    .btn-primary { background: #0f2044; color: #fff; border: none; padding: 12px 24px; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 0.95rem; width: 100%; }
    .btn-primary:hover { background: #182e5e; }
</style>
@endpush

@section('breadcrumbs')
    <a href="{{ route('owner.dashboard') }}">Dashboard</a> › 
    <a href="{{ route('owner.staff.index') }}">Staff</a> › 
    <span style="color:#0f2044;">Create</span>
@endsection

@section('content')
    <div class="page-header">
        <div class="page-title">Create Staff Account</div>
    </div>

    <div class="form-card">
        <form action="{{ route('owner.staff.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label class="form-label" for="name">Full Name</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required>
                @error('name')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required>
                @error('email')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="phone">Phone</label>
                <input type="text" id="phone" name="phone" class="form-control" value="{{ old('phone') }}" required>
                <div style="font-size: 0.8rem; color: #9daec5; margin-top: 4px;">Format: 01X-XXXXXXXX</div>
                @error('phone')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
                <div style="font-size: 0.8rem; color: #9daec5; margin-top: 4px;">Min 8 characters, must include letters and numbers</div>
                @error('password')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="password_confirmation">Confirm Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
            </div>

            <div style="display: flex; gap: 16px; align-items: center; margin-top: 24px;">
                <button type="submit" class="btn-primary" style="width: auto;">Create Staff Account</button>
                <a href="{{ route('owner.staff.index') }}" style="color: #5a6a85; text-decoration: none; font-size: 0.95rem; font-weight: 500;">Cancel</a>
            </div>
        </form>
    </div>
@endsection
