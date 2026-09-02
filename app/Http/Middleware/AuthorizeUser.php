<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AuthorizeUser
{
    public function handle($request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect('auth/login');
        }

        return $next($request);
    }
}
