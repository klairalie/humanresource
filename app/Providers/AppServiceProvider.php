<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use PhpOffice\PhpWord\Settings;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use App\Models\Employeeprofiles;
use Illuminate\Support\Facades\Session;
use App\Session\HybridSessionHandler;
use App\Observers\EmployeeProfileObserver;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use App\Observers\ApplicantObserver;
use App\Models\Applicant;
use App\Models\ServiceRequestItem;
use App\Observers\EvaluateServicesObserver;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {  if ($this->app->environment('local') || $this->app->environment('production')) {
        URL::forceScheme('https');
    }

        Employeeprofiles::observe(EmployeeProfileObserver::class);
        Applicant::observe(ApplicantObserver::class);
        ServiceRequestItem::observe(EvaluateServicesObserver::class);
        // Configure PhpWord PDF rendering
        Settings::setPdfRendererName('TCPDF');
        Settings::setPdfRendererPath(base_path('vendor/tecnickcom/tcpdf'));

        // Share failedCount globally with all views
        View::composer('*', function ($view) {
            $failedCount = DB::table('failed_jobs')->count();
            $view->with('failedCount', $failedCount);

        });
       Blade::if('authposition', function (...$positions) {
    $userPosition = session('user_position');
    return Auth::check() && in_array($userPosition, $positions);
});

  View::composer('*', function ($view) {
    // Build notifications
    $leaveRequests = DB::table('leave_requests')
        ->join('employeeprofiles', 'leave_requests.employeeprofiles_id', '=', 'employeeprofiles.employeeprofiles_id')
        ->where('leave_requests.status', 'Pending')
        ->select('employeeprofiles.first_name', 'employeeprofiles.last_name', 'leave_requests.created_at')
        ->get();

    $serviceRequests = DB::table('service_request_items')
        ->where('status', 'Pending')
        ->select('service_request_id', 'created_at')
        ->get();

    $overtimeRequests = DB::table('overtime_requests')
        ->join('employeeprofiles', 'overtime_requests.employeeprofiles_id', '=', 'employeeprofiles.employeeprofiles_id')
        ->where('overtime_requests.status', 'Pending')
        ->select('employeeprofiles.first_name', 'employeeprofiles.last_name', 'overtime_requests.created_at')
        ->get();

    $pendingApplicants = DB::table('applicants')
        ->where('applicant_status', 'Pending')
        ->select('first_name', 'last_name', 'created_at')
        ->get();

    $releasedPayrolls = DB::table('payrolls')
        ->where('status', 'Released')
        ->select('payroll_id', 'created_at')
        ->get();

    $failedCount = DB::table('failed_jobs')->count();

    $notifications = collect();

    // Leave requests notifications
    foreach ($leaveRequests as $leave) {
        $notifications->push([
            'type' => 'leave',
            'message' => "{$leave->first_name} {$leave->last_name} submitted a leave request.",
            'link' => route('show.leaverequest'),
            'time' => $leave->created_at,
        ]);
    }

    // Service requests notifications
    foreach ($serviceRequests as $service) {
        $notifications->push([
            'type' => 'service',
            'message' => "Service Request #{$service->service_request_id} is pending.",
            'link' => route('show.evaluateservices'),
            'time' => $service->created_at,
        ]);
    }

    // Overtime notifications
    foreach ($overtimeRequests as $ot) {
        $notifications->push([
            'type' => 'overtime',
            'message' => "{$ot->first_name} {$ot->last_name} filed an overtime request.",
            'link' => route('show.overtime'),
            'time' => $ot->created_at,
        ]);
    }

    // Pending applicants notifications
    foreach ($pendingApplicants as $app) {
        $fullName = trim("{$app->first_name} {$app->last_name}");
        $notifications->push([
            'type' => 'applicant',
            'message' => "New applicant: {$fullName} awaiting review.",
            'link' => route('show.listapplicants'),
            'time' => $app->created_at,
        ]);
    }

    // Released payroll notifications
    foreach ($releasedPayrolls as $payroll) {
        $notifications->push([
            'type' => 'payroll',
            'message' => "Payroll #{$payroll->payroll_id} has been released.",
            'link' => route('view.payroll'),
            'time' => $payroll->created_at,
        ]);
    }

    // HOLIDAY NOTIFICATION
    $today = now()->toDateString();
    $holidayToday = DB::table('holidays')
        ->where('holiday_date', $today)
        ->first();

    if ($holidayToday) {
        $notifications->push([
            'type' => 'holiday',
            'message' => "Today is {$holidayToday->holiday_type} Holiday: {$holidayToday->holiday_name}",
            'link' => '#', // Optional: link to holiday management page
            'time' => now(),
        ]);
    }

    // Failed jobs notifications
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

    // Share with all views
    $view->with('notifications', $notifications);
});
    }
}

