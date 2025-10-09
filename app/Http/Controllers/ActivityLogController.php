<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index()
    {
        // Get all activity logs — includes both Employees and Applicants
        $logs = ActivityLog::with(['employeeprofiles', 'applicant'])
            ->orderBy('created_at', 'desc')
            ->paginate(20); // use pagination for table display

        return view('recent-activities.index', compact('logs'));
    }
}
