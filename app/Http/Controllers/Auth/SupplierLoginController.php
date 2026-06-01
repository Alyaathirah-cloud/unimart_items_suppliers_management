<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class SupplierLoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/supplier/dashboard';

    public function __construct()
    {
        $this->middleware('guest:supplier')->except('logout');
    }

    public function showLoginForm()
    {
        return view('auth.login', ['loginAction' => route('supplier.login')]);
    }

    public function username()
    {
        return 'login';
    }

    protected function guard()
    {
        return Auth::guard('supplier');
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
        return redirect($this->redirectTo);
    }

    public function logout(Request $request)
    {
        Auth::guard('supplier')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('supplier.login');
    }
}
