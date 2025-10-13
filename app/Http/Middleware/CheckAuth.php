<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CheckAuth
{
   public function handle(Request $request, Closure $next)
{
    if (!Session::has('user_email')) {  // check the session key you already set
        return redirect()->away('http://login.test');
    }

    return $next($request);
}

}
