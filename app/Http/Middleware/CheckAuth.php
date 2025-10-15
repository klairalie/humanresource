<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAuth
{
    public function handle(Request $request, Closure $next)
    {
        // Use the request session instance, not the facade (more reliable across subdomains)
        if (!$request->session()->has('user_email')) {
            return redirect()->away('http://login.test');
        }

        return $next($request);
    }
}
