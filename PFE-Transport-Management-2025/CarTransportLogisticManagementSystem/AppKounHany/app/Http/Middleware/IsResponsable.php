<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class IsResponsable
{
    public function handle($request, Closure $next)
    {
        if (Auth::check() && Auth::user()->isResponsable) {
            return $next($request);
        }

        abort(403, 'Unauthorized');
    }
}
