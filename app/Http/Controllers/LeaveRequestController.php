<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeaveRequestController extends Controller
{
    public function showRequests()
    {
        // Fetch only pending leave requests (case-insensitive)
        $leaveRequests = DB::table('leave_requests')
            ->join('employeeprofiles', 'leave_requests.employeeprofiles_id', '=', 'employeeprofiles.employeeprofiles_id')
            ->select(
                'leave_requests.leave_request_id as id',
                'employeeprofiles.employeeprofiles_id',
                'employeeprofiles.firstname',
                'employeeprofiles.lastname',
                'leave_requests.start_at',
                'leave_requests.end_at',
                'leave_requests.status',
                'leave_requests.reason'
            )
            ->whereRaw('LOWER(leave_requests.status) = ?', ['pending'])
            ->orderBy('leave_requests.filed_date', 'desc')
            ->get();

        return view('HR.manageleaves', compact('leaveRequests'));
    }
}
