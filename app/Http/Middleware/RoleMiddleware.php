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
    public function handle(Request $request, Closure $next, $role)
    {
        $user = auth()->user();

        if (!$user) {
            return redirect('/login');
        }

        $allowed = match($role) {
            'owner'    => in_array($user->role, ['owner', 'staff']),
            'admin'    => $user->role === 'admin',
            'supplier' => $user->role === 'supplier',
            default    => false,
        };

        if (!$allowed) {
            abort(403, 'Unauthorized.');
        }

        return $next($request);
    }
}
