<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employeeprofiles;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\LeaveOvertimeRequest;
use Illuminate\Support\Facades\DB;

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

        $attendances = $query->orderBy('date', 'desc')->paginate(20 )->appends($request->query());

        $positions = Employeeprofiles::select('position')
            ->distinct()
            ->pluck('position');

        return view('HR.view_attendance', compact('attendances', 'positions'));
    }

    
   

 public function showLeaverequest()
    {
         // Fetch only pending leave requests (case-insensitive)
        $leaveRequests = DB::table('leave_requests')
            ->join('employeeprofiles', 'leave_requests.employeeprofiles_id', '=', 'employeeprofiles.employeeprofiles_id')
            ->select(
                'leave_requests.leave_request_id as id',
                'employeeprofiles.employeeprofiles_id',
                'employeeprofiles.first_name',
                'employeeprofiles.last_name',
                'leave_requests.start_at',
                'leave_requests.end_at',
                'leave_requests.status',
                'leave_requests.reason'
            )
            ->whereRaw('LOWER(leave_requests.status) = ?', ['pending'])
            ->orderBy('leave_requests.filed_date', 'desc')
            ->get();

        return view('HR.manage_leave', compact('leaveRequests'));
        
    }

 public function approve($id)
    {
        DB::table('leave_requests')
            ->where('leave_request_id', $id)
            ->update([
                'status' => 'approved',
                'approved_date' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

        return redirect()->route('show.leaverequest')->with('success', 'Leave request approved successfully.');
    }

    // ❌ Reject a leave request
    public function reject($id)
    {
        DB::table('leave_requests')
            ->where('leave_request_id', $id)
            ->update([
                'status' => 'rejected',
                'updated_at' => Carbon::now(),
            ]);

        return redirect()->route('show.leaverequest')->with('error', 'Leave request rejected.');
    }


     public function showOvertime(Request $request)
{
    $overtimeRequests = DB::table('overtime_requests')
            ->join('employeeprofiles', 'overtime_requests.employeeprofiles_id', '=', 'employeeprofiles.employeeprofiles_id')
            ->select(
                'overtime_requests.overtime_request_id as id',
                'employeeprofiles.employeeprofiles_id',
                'employeeprofiles.first_name',
                'employeeprofiles.last_name',
                'overtime_requests.hours',
                'overtime_requests.amount',
                'overtime_requests.reason',
                'overtime_requests.status',
                'overtime_requests.filed_date'
            )
            ->whereRaw('LOWER(overtime_requests.status) = ?', ['pending'])
            ->orderBy('overtime_requests.filed_date', 'desc')
            ->get();

        return view('HR.list_overtime', compact('overtimeRequests'));
    }

     public function approveOvertime($id)
    {
        DB::table('overtime_requests')
            ->where('overtime_request_id', $id)
            ->update([
                'status' => 'approved',
                'approved_date' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

        return redirect()->route('overtime.index')->with('success', 'Overtime request approved successfully.');
    }

    // ❌ Reject overtime request
    public function rejectOvertime($id)
    {
        DB::table('overtime_requests')
            ->where('overtime_request_id', $id)
            ->update([
                'status' => 'rejected',
                'updated_at' => Carbon::now(),
            ]);

        return redirect()->route('overtime.index')->with('error', 'Overtime request rejected.');
    }
}
