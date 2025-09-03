<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Employeeprofiles;
use App\Models\Attendance;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboard(Request $request)
{
     $employeeCount = Employeeprofiles::count();

$selectedDate = $request->input('date') 
    ? \Carbon\Carbon::parse($request->input('date'))->startOfDay() 
    : \Carbon\Carbon::today();

// Attendance Count (In & Out combined, unique employees)
$attendanceCount = Attendance::whereDate('date', $selectedDate)
    ->where(function ($q) {
        $q->whereBetween('time_in', ['06:00:00', '17:00:00'])
          ->orWhereBetween('time_out', ['17:00:00', '18:00:00']);
    })
    ->distinct('employeeprofiles_id')   // ensures one count per employee
    ->count('employeeprofiles_id');

// Attendance list with employee info
$attendances = Attendance::with('employeeprofiles') // assumes relation in Attendance model
    ->whereDate('date', $selectedDate)
    ->get();

$formattedDate = $selectedDate->format('l, F d Y');

return view('HR.dashboard', [
    "employeeCount"   => $employeeCount,
    "attendanceCount" => $attendanceCount,
    "formattedDate"   => $formattedDate,
    "attendances"     => $attendances,
]);

}
}
