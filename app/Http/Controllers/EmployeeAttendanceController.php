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
                [
                    'employeeprofiles_id' => $employee->employeeprofiles_id,
                    'date' => $today
                ],
                [
                    'time_in'  => null,
                    'time_out' => null,
                    'status'   => 'Pending'
                ]
            );
        }

        // Auto-update statuses
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
                'id'       => $employee->employeeprofiles_id,
                'name'     => "{$employee->first_name} {$employee->last_name}",
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
        $action     = $request->input('action_type');

        $now   = Carbon::now('Asia/Manila');
        $today = Carbon::today('Asia/Manila');

        $attendance = Attendance::firstOrCreate(
            ['employeeprofiles_id' => $employeeId, 'date' => $today],
            ['time_in' => null, 'time_out' => null, 'status' => 'Pending']
        );

        // Time windows
        $startTime  = Carbon::createFromTimeString('06:00:00', 'Asia/Manila');
        $cutOffTime = Carbon::createFromTimeString('08:00:00', 'Asia/Manila');
        $endTime    = Carbon::createFromTimeString('17:00:00', 'Asia/Manila');
        $halfStart   = Carbon::createFromTimeString('12:00:00', 'Asia/Manila');
        $halfEnd     = Carbon::createFromTimeString('13:00:00', 'Asia/Manila');

        // ---------------------- TIME IN ----------------------
        if ($action === 'time_in') {

            if ($attendance->time_in) {
                return response()->json(['success' => false, 'message' => 'Already timed in.']);
            }

            $attendance->time_in = $now->format('H:i:s');

            if ($now->between($startTime, $cutOffTime)) {
                $attendance->status = 'On Duty';
            } elseif ($now->gt($cutOffTime)) {
                $attendance->status = 'Late - On Duty';
            }

            $attendance->save();

            return response()->json([
                'success' => true,
                'message' => 'Time In recorded.',
                'time_in' => $attendance->time_in,
                'status'  => $attendance->status
            ]);
        }

        // ---------------------- TIME OUT ----------------------
        if ($action === 'time_out') {

            if (!$attendance->time_in) {
                return response()->json(['success' => false, 'message' => 'Cannot time out without time in.']);
            }

            if ($attendance->time_out) {
                return response()->json(['success' => false, 'message' => 'Already timed out.']);
            }

            $timeIn = Carbon::parse($attendance->time_in, 'Asia/Manila');
            // if ($now->diffInMinutes($timeIn) < 0) {
                // return response()->json(['success' => false, 'message' => 'Time out invalid wait for {0} minute.']);
            // }

            $attendance->time_out = $now->format('H:i:s');
            $timeOut = Carbon::parse($attendance->time_out, 'Asia/Manila');
            if ($timeIn->gt($cutOffTime)) {
                $attendance->status = 'Late - Present';
            } elseif ($timeOut->between($halfStart, $halfEnd, true)) {
                $attendance->status = 'Present - Halfday';
            } elseif ($timeOut->lt($endTime)) {
                $attendance->status = 'Present - Undertime';
            } else {
                $attendance->status = 'Present';
            }
            $attendance->save();

            return response()->json([
                'success'   => true,
                'message'   => $attendance->status,
                'time_out'  => $attendance->time_out,
                'status'    => $attendance->status
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Invalid action.'], 400);
    }

    // ========================= MANUAL MASS UPDATE =========================
    public function manualUpdate(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|integer',
            'dates'       => 'required|array'
        ]);

        foreach ($request->dates as $date) {
            Attendance::updateOrCreate(
                [
                    'employeeprofiles_id' => $request->employee_id,
                    'date'                => Carbon::parse($date)->format('Y-m-d')
                ],
                [
                    'time_in'  => '07:00:00',
                    'time_out' => '17:00:00',
                    'status'   => 'Present'
                ]
            );
        }

        return response()->json(['success' => true, 'message' => 'Attendance updated successfully.']);
    }
    // ========================= GET ALL DESCRIPTORS =========================
    // Returns array of { id, name, position, descriptor }
    public function getAllDescriptors()
    {
        // Eager load only necessary fields to reduce payload
        $employees = Employeeprofiles::select(
            'employeeprofiles_id',
            DB::raw("CONCAT(first_name, ' ', last_name) as name"),
            'position',
            'face_descriptor'
        )->whereNotNull('face_descriptor')->get();

        $data = [];

        foreach ($employees as $e) {
            // face_descriptor is stored as JSON/text — decode safely
            $decoded = json_decode($e->face_descriptor);
            if (is_null($decoded)) continue; // skip if invalid
            $data[] = [
                'id' => $e->employeeprofiles_id,
                'name' => $e->name,
                'position' => $e->position,
                'descriptor' => $decoded // an array of floats
            ];
        }

        return response()->json(['success' => true, 'data' => $data]);
    }

    // ========================= AUTO STATUS UPDATE =========================
    private function autoUpdateAttendanceStatus($date)
    {
        $attendances = Attendance::whereDate('date', $date)->get();

        $startTime  = Carbon::createFromTimeString('06:00:00', 'Asia/Manila');
        $cutOffTime = Carbon::createFromTimeString('08:00:00', 'Asia/Manila');
        $endTime    = Carbon::createFromTimeString('17:00:00', 'Asia/Manila');
        $halfStart  = Carbon::createFromTimeString('12:00:00', 'Asia/Manila');
        $halfEnd    = Carbon::createFromTimeString('13:00:00', 'Asia/Manila');
        $now        = Carbon::now('Asia/Manila');

        foreach ($attendances as $attendance) {

            $status = 'Pending';

            if ($attendance->time_in) {

                $timeIn = Carbon::parse($attendance->time_in, 'Asia/Manila');

                if ($timeIn->between($startTime, $cutOffTime)) {
                    $status = 'On Duty';
                } elseif ($timeIn->gt($cutOffTime)) {
                    $status = 'Late - On Duty';
                }

                if ($attendance->time_out) {
                    $timeOut = Carbon::parse($attendance->time_out, 'Asia/Manila');
                    if ($timeIn->gt($cutOffTime)) {
                        $status = 'Late - Present';
                    } elseif ($timeOut->between($halfStart, $halfEnd, true)) {
                        $status = 'Present - Halfday';
                    } elseif ($timeOut->lt($endTime)) {
                        $status = 'Present - Undertime';
                    } else {
                        $status = 'Present';
                    }

                } elseif ($now->gt($endTime)) {
                    $status = 'Incomplete';
                }

            } elseif (!$attendance->time_in && $now->gt($endTime)) {
                $status = 'Absent';
            }

            $attendance->status = $status;
            $attendance->save();
        }
    }

    // ========================= ADMIN UPDATE SELECTED ATTENDANCE =========================
    public function adminUpdate(Request $request)
    {
        $validated = $request->validate([
            'attendance_ids'   => 'required|array|min:1',
            'attendance_ids.*' => 'integer|exists:attendances,attendance_id',
            'time_out'         => 'required|date_format:H:i',
            'status'           => 'required|string'
        ]);

        $timeOutSec = Carbon::createFromFormat('H:i', $validated['time_out'], 'Asia/Manila')->format('H:i:s');

        $records = Attendance::whereIn('attendance_id', $validated['attendance_ids'])->get();
        $updates = [];

        foreach ($records as $att) {
            $att->time_out = $timeOutSec;
            $att->status = $validated['status'];
            $att->save();

            $updates[$att->attendance_id] = [
                'time_out' => $att->time_out,
                'time_out_display' => Carbon::parse($att->time_out, 'Asia/Manila')->format('h:i A'),
                'status' => $att->status,
            ];
        }

        return response()->json(['success' => true, 'updates' => $updates]);
    }
}
