<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Permission;
use App\Models\PositionPermission;

class CheckPermission
{
    public function handle(Request $request, Closure $next, $permissionKey)
    {
        $userEmail = session('user_email');
        $userPosition = session('user_position');

        if (!$userEmail || !$userPosition) {
            return redirect('http://login.test')->withErrors('Please login first.');
        }

        $permission = Permission::where('permission_key', $permissionKey)->first();
        if (!$permission) abort(403, 'Permission not found.');

        $positionPermission = PositionPermission::whereRaw('LOWER(position) = ?', [strtolower($userPosition)])
            ->where('permission_id', $permission->permission_id)
            ->first();

        if (!$positionPermission || !$positionPermission->is_allowed) {
            abort(403, 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}
