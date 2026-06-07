<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ForcePasswordChangeController extends Controller
{
    public function show()
    {
        $user = auth()->user() ?? auth('supplier')->user();
        if (!$user || !$user->must_change_password) {
            return redirect('/home');
        }
        return view('auth.force-password-change');
    }

    public function update(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = auth()->user() ?? auth('supplier')->user();
        
        $user->update([
            'password' => Hash::make($request->password),
            'must_change_password' => false,
        ]);

        return redirect('/home')->with('success', 'Password updated successfully.');
    }
}
