<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employeeprofiles;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\LeaveOvertimeRequest;

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

    public function showOvertime(Request $request)
{
    $search = $request->input('search');

    $overtimeRequests = LeaveOvertimeRequest::with('employeeprofiles')
        ->when($search, function ($query, $search) {
            $query->whereHas('employeeprofiles', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%");
            })
            ->orWhere('request_date', 'like', "%{$search}%");
        })
        ->get();

    return view('HR.list_overtime', [
        'overtimeRequests' => $overtimeRequests,
    ]);
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
  $attendance->time_in = $request->input('time_in');

    
    if ($validatedData['status'] === 'Present') {
      $attendance->time_out = $request->filled('time_out')
    ? Carbon::createFromFormat('H:i', $request->input('time_out'))->format('H:i:s')
    : null;
    }

    $attendance->save();

    return redirect()->back()->with('success', 'Attendance recorded successfully.');
}


}
