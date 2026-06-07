<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ForcePasswordChange
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user() ?? auth('supplier')->user();
        if ($user && $user->must_change_password) {
            if (! $request->routeIs('password.force-change')
                && ! $request->routeIs('password.force-change.update')
                && ! $request->routeIs('password.request')
                && ! $request->routeIs('password.email')
                && ! $request->routeIs('password.reset')
                && ! $request->routeIs('password.update')
                && ! $request->routeIs('password.confirm')
                && ! $request->routeIs('logout')
                && ! $request->routeIs('login')
                && ! $request->routeIs('supplier.login')) {
                return redirect()->route('password.force-change')
                    ->with('warning', 'For your security, please set a new password before continuing.');
            }
        }

        return $next($request);
    }
}
