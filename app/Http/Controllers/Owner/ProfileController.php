<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:owner']);
    }

    public function edit()
    {
        $owner = auth()->user();
        return view('owner.profile.edit', compact('owner'));
    }

    public function update(Request $request)
    {
        $owner = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($owner->id)],
            'phone' => 'nullable|string|max:30',
        ]);

        $owner->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function changePasswordShow()
    {
        return view('owner.profile.change-password');
    }

    public function changePasswordUpdate(Request $request)
    {
        $owner = auth()->user();

        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, $owner->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $owner->update(['password' => Hash::make($request->new_password)]);

        return back()->with('success', 'Password changed successfully.');
    }
}
