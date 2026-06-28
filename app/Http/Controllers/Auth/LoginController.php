<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers {
        login as traitLogin;
    }

    /**
     * Where to redirect users after login (fallback).
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * The user has been authenticated. Redirect based on role.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return \Illuminate\Http\Response
     */
    public function username()
    {
        return 'login';
    }

    protected function credentials(Request $request)
    {
        $login = trim($request->input($this->username()));
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';

        if ($field === 'email') {
            $login = strtolower($login);
        }

        return [
            $field => $login,
            'password' => trim((string) $request->input('password')),
        ];
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        $login = trim($request->input($this->username()));
        $user = User::whereRaw('LOWER(email) = ?', [strtolower($login)])
            ->orWhere('name', $login)
            ->first();

        if (!$user) {
            throw ValidationException::withMessages([
                $this->username() => ['Email not found. Please check your email address.'],
            ]);
        }

        throw ValidationException::withMessages([
            'password' => ['Incorrect password. Please try again or contact your supplier manager.'],
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
            'role' => 'required|string|in:owner,staff,supplier'
        ]);

        $loginValue = $request->input($this->username());
        $user = User::where('email', $loginValue)->orWhere('name', $loginValue)->first();

        if ($user && $user->role !== $request->role) {
            return back()->withErrors([$this->username() => 'The selected role does not match your account.']);
        }

        if ($user && !in_array($user->role, ['admin', 'owner', 'staff', 'supplier'])) {
            return back()->withErrors([$this->username() => 'Your account role is not recognised.']);
        }

        if ($user && $user->status === 'inactive') {
            return back()->withErrors([$this->username() => 'Your account has been deactivated.']);
        }

        if ($user && $user->status === 'pending') {
            return back()->withErrors([$this->username() => 'Your account is pending approval from the owner.']);
        }

        if ($user && $user->status === 'rejected') {
            return back()->withErrors([$this->username() => 'Your registration request has been rejected. Please contact the owner.']);
        }

        return $this->traitLogin($request);
    }

    protected function authenticated(Request $request, $user)
    {
        if ($user->isAdmin()) {
            return redirect('/admin/users');
        }
        if ($user->isOwner()) {
            return redirect('/owner/dashboard');
        }
        if ($user->isStaff()) {
            return redirect('/staff/dashboard');
        }
        if ($user->isSupplier()) {
            // Preserve existing supplier guard login logic
            Auth::guard('web')->logout();
            Auth::guard('supplier')->login($user);
            $request->session()->regenerate();
            return redirect('/supplier/dashboard');
        }
        return redirect('/');
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        Auth::guard('supplier')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
