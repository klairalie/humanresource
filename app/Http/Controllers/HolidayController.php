<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class HolidayController extends Controller
{
    // 📅 Calendar page
    public function index()
    {
        $holidays = DB::table('holidays')->select('holiday_name', 'holiday_type', 'holiday_date')->get();

        // Format data for the calendar
        $events = $holidays->map(function ($holiday) {
            return [
                'title' => $holiday->holiday_name . ' (' . $holiday->holiday_type . ')',
                'start' => $holiday->holiday_date,
                'color' => $holiday->holiday_type === 'Regular' ? '#2563eb' : '#f59e0b', // blue for regular, orange for special
            ];
        });

        return view('HR.holidays', compact('events'));
    }

    // 🔄 Trigger seeder manually
    public function updateHolidays()
    {
        Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\HolidaysSeeder']);
        $output = Artisan::output();

        return response()->json([
            'message' => '✅ Holidays updated successfully!',
            'details' => $output,
        ]);
    }
}
