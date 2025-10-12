<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class SessionTimeOutMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
{
    // Skip session timeout logic for guests or verification routes
    if (!Auth::check() || $request->is('show.login') || $request->is('show.register') || 
        $request->is('verify.code') || $request->is('verify.submit')) {
        return $next($request);
    }

    $timeout = config('session.lifetime') * 60; // minutes to seconds
    $lastActivity = session('lastActivityTime');

    if ($lastActivity && (time() - $lastActivity > $timeout)) {
        Auth::logout();
        session()->forget('lastActivityTime');
        return redirect()->route('show.login')->with('message', 'You have been logged out due to inactivity.');
    }

    session(['lastActivityTime' => time()]);

    return $next($request);
}
}
