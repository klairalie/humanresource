<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\VisitToken;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
class CheckVisitToken
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->query('visit_token');

        if ($token) {
            $visit = VisitToken::where('token', $token)
                ->where('expires_at', '>', now())
                ->first();

            if ($visit) {
                // Populate session as acting user
                Session::put('user_email', $visit->admin_email);
                Session::put('user_position', $visit->acting_as);
                Session::put('acting_as', $visit->target_module);
            } else {
                return abort(403, 'Invalid or expired visit token.');
            }
        }

        return $next($request);
    }
}
