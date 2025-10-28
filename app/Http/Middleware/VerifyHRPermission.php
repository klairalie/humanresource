<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\JWTHelper;

class VerifyHRPermission
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->query('token');

        if (!$token) {
            abort(403, 'Missing token');
        }

        $data = JWTHelper::decodeJWT($token);

        // Store the allowed permission in the session (for later page loads)
        session(['allowed_permission' => $data['allowed_permission'] ?? null]);

        // Check if the current route corresponds to the allowed permission
        $routePermissionMap = [
            'Employeeprofiles' => 'employeeProfile',
            'HR/view_attendance' => 'attendanceRecord',
            'recent-activities' => 'recentActivities',
            'evaluationresult' => 'evaluationResults',
            'assessmentresult' => 'applicationTestResult',
            'archivedprofiles' => 'archivedProfiles',
            'HR' => 'hrDashboard',
            'evaluateservices' => 'serviceReports',
        ];

        $path = trim($request->path(), '/');
        $allowedPermission = session('allowed_permission');

        foreach ($routePermissionMap as $route => $perm) {
            if (str_contains($path, $route) && $perm !== $allowedPermission) {
                abort(403, 'You do not have permission to access this page.');
            }
        }

        return $next($request);
    }
}
