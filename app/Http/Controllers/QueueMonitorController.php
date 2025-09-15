<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class QueueMonitorController extends Controller
{
    public function index()
    {
        // Get failed jobs from the database (latest first)
        $failedJobs = DB::table('failed_jobs')->orderByDesc('failed_at')->paginate(10);

        // Pass them to the Blade view
        return view('HR.queue-failures', compact('failedJobs'));
    }
}
