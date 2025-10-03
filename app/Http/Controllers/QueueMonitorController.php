<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;

class QueueMonitorController extends Controller
{
    public function index()
    {
        // Get failed jobs from the database (latest first)
        $failedJobs = DB::table('failed_jobs')->orderByDesc('failed_at')->paginate(10);

        // Pass them to the Blade view
        return view('HR.queue-failures', compact('failedJobs'));
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
