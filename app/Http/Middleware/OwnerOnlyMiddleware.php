<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class OwnerOnlyMiddleware
{
    public function handle($request, Closure $next)
    {
        if (auth()->check() && auth()->user()->role === 'owner') {
            return $next($request);
        }
        abort(403, 'This section is restricted to the owner only.');
    }
}
