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

    use AuthenticatesUsers;

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

    protected function authenticated(Request $request, $user)
    {
        if ($user->isAdmin()) {
            return redirect('/admin/users');
        } elseif ($user->isOwner()) {
            return redirect('/owner/items');
        } elseif ($user->isSupplier()) {
            return redirect('/supplier/dashboard');
        }
        return redirect($this->redirectTo);
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
