<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     * Expect parameter like role:admin or role:owner,supplier
     */
    public function handle(Request $request, Closure $next, $roles)
    {
        $user = $request->user();
        if (!$user) {
            return redirect()->route('login');
        }

        $allowed = explode(',', $roles);
        if (!in_array($user->role, $allowed)) {
            if ($user->isOwner()) {
                return redirect()->route('owner.dashboard')->with('error', 'Unauthorized access.');
            } elseif ($user->isSupplier()) {
                return redirect()->route('supplier.dashboard')->with('error', 'Unauthorized access.');
            } elseif ($user->isAdmin()) {
                return redirect()->route('admin.users.index')->with('error', 'Unauthorized access.');
            }
            return redirect('/')->with('error', 'Unauthorized access.');
        }

        return $next($request);
    }
}
