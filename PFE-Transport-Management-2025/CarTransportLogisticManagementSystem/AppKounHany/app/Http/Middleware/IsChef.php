<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class IsChef
{
    public function handle($request, Closure $next)
    {
        if (Auth::check() && Auth::user()->isChef) {
            return $next($request);
        }

        abort(403, 'Unauthorized');
    }
}
