<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Session;
use Closure;
use Illuminate\Http\Request;

class CheckPosition
{
    public function handle(Request $request, Closure $next, ...$positions)
    {
        $userPosition = $request->session()->get('user_position');

        if (!$userPosition || !in_array($userPosition, $positions)) {
            return redirect()->away('http://login.test')->withErrors([
                'msg' => 'Unauthorized access.'
            ]);
        }

        return $next($request);
    }
}
