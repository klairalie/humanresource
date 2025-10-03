<?php

namespace App\Http\Controllers;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
 public function index()
{
    $logs = ActivityLog::with('employeeprofiles')
        ->orderBy('created_at', 'desc')
        ->paginate(10); // 10 per page, adjust as needed

    return view('recent-activities.index', compact('logs'));
}


}
