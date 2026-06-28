<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
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
        $this->middleware('guest');
    }


    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role'     => ['required', 'string', 'in:owner,staff,supplier'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => $data['role'],
            'status'   => $data['role'] === 'staff' ? 'pending' : 'active',
        ]);
    }

    protected function registered(Request $request, $user)
    {
        if ($user->status === 'pending') {
            \Illuminate\Support\Facades\Auth::logout();
            
            $owner = User::where('role', 'owner')->first();
            if ($owner) {
                \App\Models\Notification::sendToOwners('staff_registration', "New Staff Registration Request: {$user->name}");
            }

            return redirect('/login')->with('status', 'Your registration request has been submitted and is awaiting owner approval.');
        }

        if ($user->isAdmin()) {
            return redirect('/admin/users');
        } elseif ($user->isOwner()) {
            return redirect('/owner/dashboard');
        } elseif ($user->isStaff()) {
            return redirect('/owner/items');
        } elseif ($user->isSupplier()) {
            return redirect('/supplier/dashboard');
        }
        return redirect($this->redirectTo);
    }
}
