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
use Illuminate\Support\Facades\Auth;
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
       Blade::if('authposition', function (...$positions) {
    $userPosition = session('user_position');
    return Auth::check() && in_array($userPosition, $positions);
});
    }
}

