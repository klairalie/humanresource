<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use PhpOffice\PhpWord\Settings;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use App\Models\Employeeprofiles;
use App\Observers\EmployeeProfileObserver;
class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Employeeprofiles::observe(EmployeeProfileObserver::class);
        // Configure PhpWord PDF rendering
        Settings::setPdfRendererName('TCPDF');
        Settings::setPdfRendererPath(base_path('vendor/tecnickcom/tcpdf'));

        // Share failedCount globally with all views
        View::composer('*', function ($view) {
            $failedCount = DB::table('failed_jobs')->count();
            $view->with('failedCount', $failedCount);
        });
    }
}

