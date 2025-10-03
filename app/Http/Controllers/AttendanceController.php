<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employeeprofiles;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\LeaveOvertimeRequest;

class AttendanceController extends Controller
{
    public function showAttendance(Request $request)
    {
        $query = Attendance::with('employeeprofiles');

        // 🔍 Search by name
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('employeeprofiles', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        // 🎯 Filter by position
        if ($request->filled('position')) {
            $query->whereHas('employeeprofiles', function ($q) use ($request) {
                $q->where('position', $request->input('position'));
            });
        }

        // 📅 Filter by date
        if ($request->filled('date')) {
            $query->whereDate('date', $request->input('date'));
        }

        $attendances = $query->orderBy('date', 'desc')->get();

        $positions = Employeeprofiles::select('position')
            ->distinct()
            ->pluck('position');

        return view('HR.view_attendance', compact('attendances', 'positions'));
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

 

}
