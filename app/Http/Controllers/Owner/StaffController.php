<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\EmailNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    public function index()
    {
        $pendingStaff = User::where('role', 'staff')->where('status', 'pending')->orderBy('created_at', 'desc')->get();
        $activeStaff = User::where('role', 'staff')->where('status', '!=', 'pending')->orderBy('created_at', 'desc')->get();
        return view('owner.staff.index', compact('pendingStaff', 'activeStaff'));
    }

    public function create()
    {
        return view('owner.staff.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users'],
            'phone' => ['required', 'regex:/^(\+?601|01)[0-9]{8,9}$/'],
            'password' => [
                'required',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[A-Za-z])(?=.*\d).+$/'
            ]
        ], [
            'phone.regex' => 'The phone format is invalid. It must be a Malaysian number (e.g. 0123456789, 60123456789 or +60123456789).',
            'password.regex' => 'The password must contain at least one letter and one number.'
        ]);

        $plainPassword = $request->password;

        User::create([
            'name'        => $request->name,
            'email'       => $request->email,
            'phone'       => $request->phone,
            'password'    => Hash::make($plainPassword),
            'role'        => 'staff',
            'is_approved' => true,  // owner-created accounts are pre-approved
            'status'      => 'active',
        ]);

        // Send welcome email with temporary (plaintext) password — silently swallow failures
        try {
            (new EmailNotificationService)->sendNewStaffWelcome(
                $request->email,
                $request->name,
                $plainPassword
            );
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Staff welcome email failed', [
                'email' => $request->email,
                'error' => $e->getMessage(),
            ]);
        }

        return redirect()->route('owner.staff.index')->with('success', 'Staff account created successfully.');
    }

    public function destroy(User $user)
    {
        if (!auth()->user()->isOwner()) {
            return redirect()->back()->with('error', 'Only owners can delete staff.');
        }

        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot delete yourself.');
        }

        $user->delete();

        return redirect()->route('owner.staff.index')->with('success', 'Staff account deleted.');
    }

    public function edit(User $user)
    {
        return view('owner.staff.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,' . $user->id],
            'phone' => ['required', 'regex:/^(\+?601|01)[0-9]{8,9}$/'],
        ], [
            'phone.regex' => 'The phone format is invalid. It must be a Malaysian number (e.g. 0123456789, 60123456789 or +60123456789).',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        return redirect()->route('owner.staff.index')->with('success', 'Staff account updated successfully.');
    }

    public function toggleStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot deactivate yourself.');
        }

        $user->update([
            'status' => $user->status === 'active' ? 'inactive' : 'active'
        ]);

        return redirect()->back()->with('success', "Staff account status changed to {$user->status}.");
    }

    public function approve(User $user)
    {
        $user->update([
            'status'      => 'active',
            'is_approved' => true,
        ]);

        // Send approval email notification to staff
        try {
            (new \App\Services\EmailNotificationService)->sendStaffApproved(
                $user->email,
                $user->name
            );
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Staff approved email failed', [
                'email' => $user->email,
                'error' => $e->getMessage(),
            ]);
        }

        return redirect()->back()->with('success', "✅ Staff account for {$user->name} approved. A notification email has been sent.");
    }

    public function reject(User $user)
    {
        $user->update(['status' => 'rejected']);
        return redirect()->back()->with('success', 'Staff registration request rejected.');
    }

    public function resetPasswordShow(User $user)
    {
        return view('owner.staff.reset-password', compact('user'));
    }

    public function resetPasswordUpdate(Request $request, User $user)
    {
        $request->validate([
            'password' => [
                'required',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[A-Za-z])(?=.*\d).+$/'
            ]
        ], [
            'password.regex' => 'The password must contain at least one letter and one number.'
        ]);

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return redirect()->route('owner.staff.index')->with('success', "Password for {$user->name} reset successfully.");
    }
}
