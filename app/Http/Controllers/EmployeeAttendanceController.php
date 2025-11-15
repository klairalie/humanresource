<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Employeeprofiles;
use App\Notifications\SendOtpNotification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
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

        // Update status logic based on time rules
        $this->autoUpdateAttendanceStatus($today);

        $attendances = Attendance::whereDate('date', $today)
            ->orderBy('employeeprofiles_id')
            ->get();

        return view('EmpAttendance.Attendancepage', compact('attendances'));
    }

    // ========================= AUTO UPDATE ATTENDANCE STATUS =========================
   private function autoUpdateAttendanceStatus($date)
{
    $now = Carbon::now('Asia/Manila');
    $timeInStart  = $date->copy()->setTime(6, 0, 0);
    $timeInEnd    = $date->copy()->setTime(8, 0, 0);
    $timeOutStart = $date->copy()->setTime(17, 0, 0);
    $timeOutEnd   = $date->copy()->setTime(19, 0, 0);

    $attendances = Attendance::whereDate('date', $date)->get();

    foreach ($attendances as $attendance) {
        // 🕕 Before 6 AM — no workday started yet
        if ($now->lt($timeInStart)) {
            $attendance->update(['status' => 'Pending']);
            continue;
        }

        // 🚫 Past 8 AM and no Time In — mark Absent
        if ($now->gt($timeInEnd) && !$attendance->time_in) {
            $attendance->update(['status' => 'Absent']);
            continue;
        }

        // 🕘 Has Time In but no Time Out — working hours (6 AM–5 PM)
        if ($attendance->time_in && !$attendance->time_out && $now->lt($timeOutStart)) {
            $attendance->update(['status' => 'On Duty']);
            continue;
        }

        // ⚠️ Has Time In but missed Time Out after 7 PM — Incomplete
        if ($attendance->time_in && !$attendance->time_out && $now->gt($timeOutEnd)) {
            $attendance->update(['status' => 'Incomplete']);

            // Optional: Email notification for missing time-out
            $employee = Employeeprofiles::find($attendance->employeeprofiles_id);
            if ($employee && $employee->email) {
                Mail::raw(
                    "Dear {$employee->first_name},\n\nOur system detected that you forgot to time out today ({$date->format('F j, Y')}). Please contact HR for correction.\n\nThank you.",
                    function ($message) use ($employee) {
                        $message->to($employee->email)
                                ->subject('Incomplete Attendance Notice');
                    }
                );
            }
            continue;
        }

        // ✅ Has both Time In and Time Out — Present
        if ($attendance->time_in && $attendance->time_out) {
            $attendance->update(['status' => 'Present']);
        }
    }
}

    // ========================= GET EMPLOYEE BY RFID CARD =========================
    public function getEmployeeByCard($cardNumber)
    {
        $employee = Employeeprofiles::where('card_Idnumber', $cardNumber)->first();
        if (!$employee) {
            return response()->json(['success' => false, 'message' => 'Card not recognized']);
        }

        $today = Carbon::today('Asia/Manila');
        $attendance = Attendance::firstOrCreate(
            ['employeeprofiles_id' => $employee->employeeprofiles_id, 'date' => $today],
            ['time_in' => null, 'time_out' => null, 'status' => 'Pending']
        );

        if ($attendance->time_in && $attendance->time_out) {
            return response()->json([
                'success' => false,
                'message' => 'Attendance for today is already completed.'
            ]);
        }

        if ($attendance->time_in && !$attendance->time_out) {
            return response()->json([
                'success' => false,
                'message' => 'You have already timed in today. Please time out later.'
            ]);
        }

        // OTP logic same as before
        $cacheKey = "otp_{$employee->employeeprofiles_id}";
        $attemptKey = "otp_attempts_{$employee->employeeprofiles_id}";
        $cachedOtp = Cache::get($cacheKey);
        $attemptCount = Cache::get($attemptKey, 0);

        if ($cachedOtp) {
            return response()->json([
                'success' => false,
                'message' => 'You already have a valid OTP. Please use it first.'
            ]);
        }

        if ($attemptCount >= 2) {
            return response()->json([
                'success' => false,
                'message' => 'Maximum OTP requests reached. Please contact HR.'
            ]);
        }

        $otp = rand(100000, 999999);
        Cache::put($cacheKey, $otp, now()->addMinutes(2));
        Cache::put($attemptKey, $attemptCount + 1, now()->addMinutes(10));
        $employee->notify(new SendOtpNotification($otp));

        return response()->json([
            'success'     => true,
            'employee_id' => $employee->employeeprofiles_id,
            'first_name'  => $employee->first_name,
            'last_name'   => $employee->last_name,
            'position'    => $employee->position,
            'email'       => $employee->email,
            'message'     => 'OTP sent to your email.'
        ]);
    }

    // ========================= VERIFY OTP =========================
    public function verifyOtp(Request $request)
    {
        $employeeId = $request->input('employee_id');
        $otpEntered = $request->input('otp');
        $action     = $request->input('action_type');

        $cachedOtp = Cache::get("otp_{$employeeId}");
        if (!$cachedOtp || $cachedOtp != $otpEntered) {
            return back()->with('error', 'Invalid or expired OTP.');
        }

        $now   = Carbon::now('Asia/Manila');
        $today = Carbon::today('Asia/Manila');
        $timeInStart  = $today->copy()->setTime(6, 0, 0);
        $timeInEnd    = $today->copy()->setTime(8, 0, 0);
        $timeOutStart = $today->copy()->setTime(17, 0, 0);
        $timeOutEnd   = $today->copy()->setTime(19, 0, 0);

        $attendance = Attendance::firstOrCreate(
            ['employeeprofiles_id' => $employeeId, 'date' => $today],
            ['status' => 'Pending']
        );

        // --- TIME IN ---
        if ($action === 'time_in') {
            if ($now->lt($timeInStart)) {
                return back()->with('error', 'Time In starts at 6:00 AM.');
            }

            if ($now->gt($timeInEnd)) {
                $attendance->update(['status' => 'Absent']);
                Cache::forget("otp_{$employeeId}");
                return back()->with('error', 'Time In closed at 8:00 AM. Marked Absent.');
            }

            if (!$attendance->time_in) {
                $attendance->update([
                    'time_in' => $now->format('H:i:s'),
                    'status'  => 'Pending'
                ]);
                Cache::forget("otp_{$employeeId}");
                return back()->with('success', 'Time In recorded.');
            }

            return back()->with('error', 'Already timed in.');
        }

        // --- TIME OUT ---
if ($action === 'time_out') {
    // Prevent timeout if no time_in
    if (!$attendance->time_in) {
        Cache::forget("otp_{$employeeId}");
        return back()->with('error', 'Cannot time out without a valid Time In record.');
    }

    if ($now->lt($timeOutStart)) {
        return back()->with('error', 'Time Out starts at 5:00 PM.');
    }

    if ($now->gt($timeOutEnd)) {
        $attendance->update(['status' => 'Incomplete']);
        Cache::forget("otp_{$employeeId}");
        return back()->with('error', 'Time Out window closed. Marked Incomplete.');
    }

    if ($attendance->time_in && !$attendance->time_out) {
        $attendance->update([
            'time_out' => $now->format('H:i:s'),
            'status'   => 'Present'
        ]);
        Cache::forget("otp_{$employeeId}");
        return back()->with('success', 'Time Out recorded.');
    }

    return back()->with('error', 'No valid Time In found or already timed out.');
}


        Cache::forget("otp_{$employeeId}");
        return back()->with('error', 'Invalid action.');
    }

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

    return response()->json(['success' => true, 'message' => 'Selected attendance records updated successfully.']);
}

}
