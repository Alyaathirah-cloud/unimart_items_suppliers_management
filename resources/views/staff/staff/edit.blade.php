@extends('layouts.staff')

@section('title', 'Edit Staff – 22UniMart')

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
    <a href="{{ route('staff.dashboard') }}">Dashboard</a> › 
    <a href="{{ route('staff.staff.index') }}">Staff</a> › 
    <span style="color:#0f2044;">Edit</span>
@endsection

@section('content')
<div class="content-center">
    <div class="page-title">Edit Staff Account: {{ $user->name }}</div>

    <div class="form-card">
        <form action="{{ route('staff.staff.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label" for="name">Full Name</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                @error('name')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                @error('email')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="phone">Phone</label>
                <input type="text" id="phone" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}" required>
                <div style="font-size: 0.8rem; color: #9daec5; margin-top: 4px;">Format: 01X-XXXXXXXX</div>
                @error('phone')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div style="display: flex; gap: 16px; align-items: center; justify-content: center; margin-top: 24px;">
                <button type="submit" class="btn-primary">Update Staff Account</button>
                <a href="{{ route('staff.staff.index') }}" class="btn-discard">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
