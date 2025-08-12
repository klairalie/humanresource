<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employeeprofiles;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function showAttendance()
    {
        return view('HR.view_attendance');
    }

    public function showLeaverequest()
    {
        return view('HR.manage_leave');
    }

    public function showOvertime()
    {
        return view('HR.list_overtime');
    }


    public function showAttendanceform()
{
    $employees = Employeeprofiles::all();
    return view('HR.attendanceform', compact('employees'));
}

   public function submitAttendance(Request $request)
{
    $validatedData = $request->validate([
        'employeeprofiles_id' => 'required|integer|exists:employeeprofiles,employeeprofiles_id',
        'date' => 'required|date',
        'flag' => 'required|integer',
        'status' => 'required|string|in:Present,Absent,Leave',
    ]);

    $attendance = new Attendance();
    $attendance->employeeprofiles_id = $validatedData['employeeprofiles_id'];
    $attendance->status = $validatedData['status'];
    $attendance->date = $validatedData['date'];
    $attendance->flag = $validatedData['flag'];
    $attendance->time_in = now();

    // Only save time_out if status is Present
    if ($validatedData['status'] === 'Present') {
        $attendance->time_out = now();
    }

    $attendance->save();

    return redirect()->back()->with('success', 'Attendance recorded successfully.');
}

}
