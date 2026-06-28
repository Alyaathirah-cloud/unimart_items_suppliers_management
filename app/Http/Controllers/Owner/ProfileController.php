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
            'name'             => 'required|string|max:255',
            'email'            => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($owner->id)],
            'phone'            => ['nullable', 'string', 'regex:/^(\+?601|01)[0-9]{8,9}$/'],
            'whatsapp_number'  => ['nullable', 'string', 'regex:/^(\+?601|01)[0-9]{8,9}$/'],
        ], [
            'phone.regex' => 'The phone format is invalid. It must be a Malaysian number (e.g. 0123456789, 60123456789 or +60123456789).',
            'whatsapp_number.regex' => 'The WhatsApp number format is invalid. It must be a Malaysian number (e.g. 0123456789, 60123456789 or +60123456789).',
        ]);

        $owner->update([
            'name'            => $request->name,
            'email'           => $request->email,
            'phone'           => $request->phone,
            'whatsapp_number' => $request->whatsapp_number,
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
