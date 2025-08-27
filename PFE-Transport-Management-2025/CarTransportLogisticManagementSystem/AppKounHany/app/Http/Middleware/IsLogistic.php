<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class IsLogistic
{
    public function handle($request, Closure $next)
    {
        if (Auth::check() && Auth::user()->isLogistic) {
            return $next($request);
        }

        abort(403, 'Unauthorized');
    }
}
