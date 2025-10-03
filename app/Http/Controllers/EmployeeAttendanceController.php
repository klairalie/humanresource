<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Employeeprofiles;
use App\Notifications\SendOtpNotification;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class EmployeeAttendanceController extends Controller
{
    // Attendance page
    public function showEmpAttendance()
    {
        $attendances = Attendance::orderBy('date', 'desc')->get();
        return view('EmpAttendance.Attendancepage', compact('attendances'));
    }

    // Get employee by RFID card
    public function getEmployeeByCard($cardNumber)
    {
        $employee = Employeeprofiles::where('card_Idnumber', $cardNumber)->first();

        if (!$employee) {
            return response()->json(['success' => false, 'message' => 'Card not recognized']);
        }

        // Generate OTP (6 digits)
        $otp = rand(100000, 999999);

        // Store in cache for 2 mins
        Cache::put("otp_{$employee->employeeprofiles_id}", $otp, now()->addMinutes(2));

        // Send email notification
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

    // Verify OTP and record attendance
public function verifyOtp(Request $request)
{
    $employeeId = $request->input('employee_id');
    $otpEntered = $request->input('otp');
    $action     = $request->input('action_type');

    $cachedOtp = Cache::get("otp_{$employeeId}");

    if (!$cachedOtp || $cachedOtp != $otpEntered) {
        return back()->with('error', 'Invalid or expired OTP.');
    }

    // Force timezone Asia/Manila
    $now   = Carbon::now('Asia/Manila');
    $today = Carbon::today('Asia/Manila');

    // Restrict allowed times
    if ($action === 'time_in' && !($now->between(Carbon::parse('06:00', 'Asia/Manila'), Carbon::parse('08:00', 'Asia/Manila')))) {
        return back()->with('error', 'Time In is only allowed between 6:00 AM and 8:00 AM.');
    }

    if ($action === 'time_out' && !($now->between(Carbon::parse('17:00', 'Asia/Manila'), Carbon::parse('19:00', 'Asia/Manila')))) {
        return back()->with('error', 'Time Out is only allowed between 5:00 PM and 7:00 PM.');
    }

    // Fetch today's attendance
    $attendance = Attendance::where('employeeprofiles_id', $employeeId)
        ->whereDate('date', $today)
        ->first();

    if ($action === 'time_in') {
        if (!$attendance) {
            Attendance::create([
                'employeeprofiles_id' => $employeeId,
                'date'                => $today,
                'time_in'             => $now->format('H:i:s'), // ✅ 24h format for DB
                'status'              => 'Present',
            ]);
        } else {
            return back()->with('error', 'Already timed in today.');
        }
    }

    if ($action === 'time_out') {
        if ($attendance && !$attendance->time_out) {
            $attendance->update([
                'time_out' => $now->format('H:i:s'), // ✅ 24h format for DB
                'status'   => 'Out',
            ]);
        } else {
            return back()->with('error', 'No valid time-in found today or already timed out.');
        }
    }

    Cache::forget("otp_{$employeeId}");

    return back()->with('success', 'Attendance recorded successfully.');
}


}

//  public function sendOtp(Request $request)
//     {
//         $employee = Auth::user(); // must be Employeeprofiles model

//         $otp = rand(100000, 999999);

//         // store in cache for 2 minutes
//         Cache::put("otp_{$employee->employeeprofiles_id}", $otp, now()->addMinutes(2));

//         // send via Gmail
//         $employee->notify(new SendOtpNotification($otp));

//         return response()->json(['success' => true, 'message' => 'OTP sent to your email.']);
//     }

//     // 🔸 Verify OTP & record attendance
//     public function verifyAttendance(Request $request)
//     {
//         $employee = Auth::user();
//         $otpInput = $request->input('otp');
//         $action = $request->input('action_type');

//         $validOtp = Cache::get("otp_{$employee->employeeprofiles_id}");

//         if (!$validOtp || $otpInput != $validOtp) {
//             return back()->with('error', 'Invalid or expired OTP.');
//         }

//         // OTP valid → clear from cache
//         Cache::forget("otp_{$employee->employeeprofiles_id}");

//         // proceed with attendance
//         return $this->recordAttendance($request);
//     }

