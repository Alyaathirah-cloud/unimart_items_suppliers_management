@extends('layouts.owner')

@section('title', 'My Profile – 22UniMart')

@section('breadcrumbs')
    <a href="{{ route('owner.dashboard') }}">Dashboard</a> › <span style="color:#0f2044;">My Profile</span>
@endsection

@section('content')
    <div class="page-title">My Profile</div>
    <div class="page-sub">Update your personal information and manage your account.</div>

    @if(session('success'))
        <div class="success-msg">{{ session('success') }}</div>
    @endif

    <div class="form-card">
        <div class="card-header">
            <div class="card-line"></div>
            <div class="card-title">Account Information</div>
        </div>

        <form method="POST" action="{{ route('owner.profile.update') }}">
            @csrf
            @method('PUT')

            <div class="form-row">
                <div class="form-group" style="flex:1;">
                    <label class="form-label" for="name">Full Name</label>
                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $owner->name) }}" required>
                    @error('name')<div class="error-msg">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group" style="flex:1;">
                    <label class="form-label" for="email">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $owner->email) }}" required>
                    @error('email')<div class="error-msg">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group" style="flex:1;">
                    <label class="form-label" for="phone">Phone Number</label>
                    <input type="text" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $owner->phone) }}">
                    @error('phone')<div class="error-msg">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group" style="flex:1;">
                    <label class="form-label" for="role">Role</label>
                    <input type="text" id="role" class="form-control" value="Owner" disabled>
                    <div style="font-size:0.75rem;color:#9daec5;margin-top:4px;">Read-only field</div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit">Save Changes</button>
            </div>
        </form>
    </div>
@endsection
