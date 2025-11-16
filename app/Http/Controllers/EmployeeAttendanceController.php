<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Employeeprofiles;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EmployeeAttendanceController extends Controller
{
    // ========================= SHOW ATTENDANCE PAGE =========================
    public function showEmpAttendance()
    {
        $today = Carbon::today('Asia/Manila');

        // Ensure attendance record exists for all employees
        $employees = Employeeprofiles::all();
        foreach ($employees as $employee) {
            Attendance::firstOrCreate(
                ['employeeprofiles_id' => $employee->employeeprofiles_id, 'date' => $today],
                ['time_in' => null, 'time_out' => null, 'status' => 'Pending']
            );
        }

        // Auto-update attendance statuses
        $this->autoUpdateAttendanceStatus($today);

        $attendances = Attendance::whereDate('date', $today)
            ->orderBy('employeeprofiles_id')
            ->get();

        return view('EmpAttendance.Attendancepage', compact('attendances'));
    }

    // ========================= GET EMPLOYEE LIST =========================
    public function getEmployees()
    {
        $employees = Employeeprofiles::select(
            'employeeprofiles_id',
            DB::raw("CONCAT(first_name, ' ', last_name) as full_name"),
            'position'
        )->orderBy('last_name')->get();

        return response()->json(['success' => true, 'employees' => $employees]);
    }

    // ========================= GET FACE DESCRIPTOR =========================
    public function getDescriptor($id)
    {
        $employee = Employeeprofiles::find($id);

        if (!$employee) {
            return response()->json(['success' => false, 'message' => 'Employee not found.'], 404);
        }

        if (!$employee->face_descriptor) {
            return response()->json(['success' => false, 'message' => 'No face descriptor found.'], 404);
        }

        return response()->json([
            'success' => true,
            'employee' => [
                'id' => $employee->employeeprofiles_id,
                'name' => "{$employee->first_name} {$employee->last_name}",
                'position' => $employee->position
            ],
            'descriptor' => json_decode($employee->face_descriptor)
        ]);
    }

    // ========================= RECORD TIME IN / OUT =========================
    public function recordAttendance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|integer|exists:employeeprofiles,employeeprofiles_id',
            'action_type' => 'required|in:time_in,time_out',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        $employeeId = $request->input('employee_id');
        $action = $request->input('action_type');

        $now = Carbon::now('Asia/Manila');
        $today = Carbon::today('Asia/Manila');

        $attendance = Attendance::firstOrCreate(
            ['employeeprofiles_id' => $employeeId, 'date' => $today],
            ['time_in' => null, 'time_out' => null, 'status' => 'Pending']
        );

        // --- TIME IN ---
        if ($action === 'time_in') {
            if ($attendance->time_in) {
                return response()->json(['success' => false, 'message' => 'Already timed in.']);
            }

            $attendance->time_in = $now->format('H:i:s');
            $attendance->status = 'On Duty';
            $attendance->save();

            return response()->json([
                'success' => true,
                'message' => 'Time In recorded.',
                'time_in' => $attendance->time_in,
                'status' => $attendance->status
            ]);
        }

        // --- TIME OUT ---
        if ($action === 'time_out') {
            if (!$attendance->time_in) {
                return response()->json(['success' => false, 'message' => 'Cannot time out without time in.']);
            }

            if ($attendance->time_out) {
                return response()->json(['success' => false, 'message' => 'Already timed out.']);
            }

            $attendance->time_out = $now->format('H:i:s');
            $attendance->status = 'Present';
            $attendance->save();

            return response()->json([
                'success' => true,
                'message' => 'Time Out recorded.',
                'time_out' => $attendance->time_out,
                'status' => $attendance->status
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Invalid action.'], 400);
    }

    // ========================= MANUAL MASS UPDATE =========================
    public function manualUpdate(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|integer',
            'dates' => 'required|array'
        ]);

        foreach ($request->dates as $date) {
            Attendance::updateOrCreate(
                [
                    'employeeprofiles_id' => $request->employee_id,
                    'date' => Carbon::parse($date)->format('Y-m-d')
                ],
                [
                    'time_in' => '07:00:00',
                    'time_out' => '17:00:00',
                    'status' => 'Present'
                ]
            );
        }

        return response()->json(['success' => true, 'message' => 'Attendance updated successfully.']);
    }

    // ========================= AUTO STATUS UPDATE =========================
    private function autoUpdateAttendanceStatus($date)
    {
        $attendances = Attendance::whereDate('date', $date)->get();

        foreach ($attendances as $attendance) {
            if ($attendance->time_in && $attendance->time_out) {
                $attendance->status = 'Present';
            } elseif ($attendance->time_in && !$attendance->time_out) {
                $attendance->status = 'On Duty';
            } elseif (!$attendance->time_in && !$attendance->time_out) {
                $attendance->status = 'Absent';
            }
            $attendance->save();
        }
    }
}
