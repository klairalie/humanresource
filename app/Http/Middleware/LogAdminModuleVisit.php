<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class LogAdminModuleVisit
{
    public function handle(Request $request, Closure $next)
    {
        // Example: log admin email
        \App\Helpers\AdminActivityLogger::log(
            targetEmail: $request->user()->email ?? null,
            module: 'HR Dashboard',
            action: 'Visited HR dashboard'
        );

        return $next($request);
    }
}
