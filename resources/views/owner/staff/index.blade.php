@extends('layouts.owner')

@section('title', 'Manage Staff – 22UniMart')

@push('styles')
<style>
    .page-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 28px; }
    .page-title { font-size: 1.6rem; font-weight: 800; color: #0f2044; }
    .btn-primary { background: #0f2044; color: #fff; border: none; padding: 10px 20px; border-radius: 6px; font-weight: 600; text-decoration: none; display: inline-block; }
    .btn-primary:hover { background: #182e5e; }
    .btn-danger { background: #fff; color: #c0392b; border: 1px solid #f5b7b1; padding: 6px 12px; border-radius: 6px; font-weight: 600; cursor: pointer; }
    .btn-danger:hover { background: #fdedec; }
    .table-container { background: #fff; border-radius: 12px; box-shadow: 0 1px 6px rgba(15,32,68,0.07); overflow: hidden; }
    table { width: 100%; border-collapse: collapse; text-align: left; }
    th, td { padding: 16px 20px; border-bottom: 1px solid #f0f4f8; }
    th { font-size: 0.85rem; font-weight: 700; color: #9daec5; text-transform: uppercase; letter-spacing: 0.5px; }
    td { font-size: 0.9rem; color: #0f2044; }
    .flash-success { padding: 12px 20px; border-radius: 8px; font-size: 0.88rem; margin-bottom: 20px; background: #e8f8f0; color: #1d8348; border: 1px solid #a9dfbf; }
    .flash-error { padding: 12px 20px; border-radius: 8px; font-size: 0.88rem; margin-bottom: 20px; background: #fdedec; color: #c0392b; border: 1px solid #f5b7b1; }
</style>
@endpush

@section('breadcrumbs')
    <a href="{{ route('owner.dashboard') }}">Dashboard</a> › <span style="color:#0f2044;">Staff</span>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <div class="page-title">Manage Staff</div>
        </div>
        <div>
            <a href="{{ route('owner.staff.create') }}" class="btn-primary">+ Add Staff</a>
        </div>
    </div>

    @if(session('success'))
        <div class="flash-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="flash-error">{{ session('error') }}</div>
    @endif

    <div class="page-title" style="margin-bottom: 16px; font-size: 1.2rem;">Pending Requests</div>
    <div class="table-container" style="margin-bottom: 40px;">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Registration Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pendingStaff as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->created_at->format('Y-m-d H:i') }}</td>
                        <td>
                            <span class="badge" style="padding: 4px 8px; border-radius: 4px; color: #fff; background-color: #f39c12; font-size: 0.75rem; font-weight: 600;">Pending</span>
                        </td>
                        <td>
                            <div style="display: flex; gap: 8px;">
                                <form action="{{ route('owner.staff.approve', $user->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn-primary" style="background: #27ae60; padding: 6px 12px; font-size: 0.85rem;">Approve</button>
                                </form>
                                <form action="{{ route('owner.staff.reject', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to reject this request?');">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn-danger" style="padding: 6px 12px; font-size: 0.85rem;">Reject</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: #9daec5; padding: 32px;">No pending registration requests.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="page-title" style="margin-bottom: 16px; font-size: 1.2rem;">Active Staff</div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($activeStaff as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone ?? '-' }}</td>
                        <td>
                            @if($user->status === 'active')
                                <span class="badge bg-success" style="padding: 4px 8px; border-radius: 4px; color: #fff; background-color: #198754; font-size: 0.75rem; font-weight: 600;">Active</span>
                            @elseif($user->status === 'inactive')
                                <span class="badge bg-danger" style="padding: 4px 8px; border-radius: 4px; color: #fff; background-color: #dc3545; font-size: 0.75rem; font-weight: 600;">Inactive</span>
                            @elseif($user->status === 'rejected')
                                <span class="badge bg-danger" style="padding: 4px 8px; border-radius: 4px; color: #fff; background-color: #c0392b; font-size: 0.75rem; font-weight: 600;">Rejected</span>
                            @endif
                        </td>
                        <td>{{ $user->created_at->format('Y-m-d H:i') }}</td>
                        <td>
                            <div style="display: flex; gap: 8px;">
                                <a href="{{ route('owner.staff.edit', $user->id) }}" class="btn-primary" style="padding: 6px 12px; font-size: 0.85rem;">Edit</a>
                                
                                @if($user->status !== 'rejected')
                                    <form action="{{ route('owner.staff.toggle-status', $user->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn-danger" style="color: #d35400; border-color: #f5cba7; min-width: 85px;">{{ $user->status === 'active' ? 'Deactivate' : 'Reactivate' }}</button>
                                    </form>
                                @endif

                                <a href="{{ route('owner.staff.reset-password', $user->id) }}" class="btn-primary" style="background: #34495e; padding: 6px 12px; font-size: 0.85rem;">Reset Password</a>

                                <form action="{{ route('owner.staff.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this staff account?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-danger">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: #9daec5; padding: 32px;">No active staff accounts found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
