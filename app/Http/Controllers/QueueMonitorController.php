<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use App\Models\LeaveRequest;
use App\Models\OvertimeRequest;

class QueueMonitorController extends Controller
{
 public function index()
{
    // Main failed jobs table data
    $failedJobs = DB::table('failed_jobs')->orderByDesc('failed_at')->paginate(10);

 // If "notifications_read" exists, show none
    if (Session::get('notifications_read')) {
        $notifications = collect();
    } else {
        // Build notifications (same logic as in AppServiceProvider)
    $leaveRequests = DB::table('leave_requests')
        ->join('employeeprofiles', 'leave_requests.employeeprofiles_id', '=', 'employeeprofiles.employeeprofiles_id')
        ->where('leave_requests.status', 'Pending')
        ->select('employeeprofiles.first_name', 'employeeprofiles.last_name', 'leave_requests.created_at')
        ->get();

        $serviceRequests = DB::table('service_request_items')
        ->where('status', 'Pending')
        ->select('service_request_id', 'created_at')
        ->get();

    // Pending overtime requests
    $overtimeRequests = DB::table('overtime_requests')
        ->join('employeeprofiles', 'overtime_requests.employeeprofiles_id', '=', 'employeeprofiles.employeeprofiles_id')
        ->where('overtime_requests.status', 'Pending')
        ->select('employeeprofiles.first_name', 'employeeprofiles.last_name', 'overtime_requests.created_at')
        ->get();

    // Pending applicants
    $pendingApplicants = DB::table('applicants')
        ->where('applicant_status', 'Pending')
        ->select('name', 'created_at')
        ->get();

    $releasedPayrolls = DB::table('payrolls')
        ->where('status', 'Released')
        ->select('payroll_id', 'created_at')
        ->get();

    $failedCount = DB::table('failed_jobs')->count();

    // Build notification collection
    $notifications = collect();

    foreach ($leaveRequests as $leave) {
        $notifications->push([
            'type' => 'leave',
            'message' => "{$leave->first_name} {$leave->last_name} submitted a leave request.",
            'link' => route('leave.requests'),
            'time' => $leave->created_at,
        ]);
    }
    
     foreach ($serviceRequests as $service) {
        $notifications->push([
            'type' => 'service',
            'message' => "Service Request #{$service->service_request_id} is pending.",
            'link' => route('show.evaluateservices'),
            'time' => $service->created_at,
        ]);
    }

    foreach ($overtimeRequests as $ot) {
        $notifications->push([
            'type' => 'overtime',
            'message' => "{$ot->first_name} {$ot->last_name} filed an overtime request.",
            'link' => route('overtime.requests'),
            'time' => $ot->created_at,
        ]);
    }

    foreach ($pendingApplicants as $app) {
        $notifications->push([
            'type' => 'applicant',
            'message' => "New applicant: {$app->name} awaiting review.",
            'link' => route('applicants.pending'),
            'time' => $app->created_at,
        ]);
    }

    foreach ($releasedPayrolls as $payroll) {
        $notifications->push([
            'type' => 'payroll',
            'message' => "Payroll #{$payroll->payroll_id} has been released.",
            'link' => route('view.payroll'),
            'time' => $payroll->created_at,
        ]);
    }

    if ($failedCount > 0) {
        $notifications->push([
            'type' => 'queue',
            'message' => "{$failedCount} system queue failure(s) detected.",
            'link' => route('queue.failures'),
            'time' => now(),
        ]);
    }

    // Sort newest first
    $notifications = $notifications->sortByDesc('time')->values();

    // Pass to view
    return view('HR.queue-failures', compact('failedJobs', 'notifications'));
}
}
    // Retry a single job
    public function retryJob($id)
    {
        try {
            Artisan::call('queue:retry', [$id]);
            return back()->with('success', 'Job retried successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to retry job: ' . $e->getMessage());
        }
    }

    // Retry all failed jobs
    public function retryAll()
    {
        try {
            Artisan::call('queue:retry', ['all']);
            return back()->with('success', 'All failed jobs retried successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to retry jobs: ' . $e->getMessage());
        }
    }

    // (Optional) Delete a failed job
    public function deleteJob($id)
    {
        DB::table('failed_jobs')->where('id', $id)->delete();
        return back()->with('success', 'Failed job deleted successfully!');
    }

    // (Optional) Clear all failed jobs
    public function clearAll()
    {
        DB::table('failed_jobs')->truncate();
        return back()->with('success', 'All failed jobs cleared!');
    }
}
