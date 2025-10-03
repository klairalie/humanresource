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
      $attendances = Attendance::whereDate('date', Carbon::today())
    ->orderBy('date', 'desc')
    ->paginate(10);

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

	// Define allowed windows bound to "today"
	$timeInStart   = $today->copy()->setTime(20, 0, 0);
	$timeInEnd     = $today->copy()->setTime(21, 0, 0);
	$timeOutStart  = $today->copy()->setTime(22, 0, 0);
	$timeOutEnd    = $today->copy()->setTime(23, 0, 0);

	// Fetch today's attendance (if any)
	$attendance = Attendance::where('employeeprofiles_id', $employeeId)
		->whereDate('date', $today)
		->first();

	if ($action === 'time_in') {
		// Too early
		if ($now->lt($timeInStart)) {
			return back()->with('error', 'Time In is only allowed starting 6:00 AM.');
		}

		// Within time-in window: record time_in
		if ($now->between($timeInStart, $timeInEnd)) {
			if (!$attendance) {
				Attendance::create([
					'employeeprofiles_id' => $employeeId,
					'date'                => $today,
					'time_in'             => $now->format('H:i:s'),
					'status'              => 'In', // interim; will become Present on successful time_out
				]);
			} else {
				return back()->with('error', 'Already timed in today.');
			}

			Cache::forget("otp_{$employeeId}");
			return back()->with('success', 'Time In recorded.');
		}

		// After time-in window: mark Absent if no time-in yet
		if ($now->gt($timeInEnd)) {
			if (!$attendance) {
				Attendance::create([
					'employeeprofiles_id' => $employeeId,
					'date'                => $today,
					'time_in'             => null,
					'time_out'            => null,
					'status'              => 'Absent',
				]);

				Cache::forget("otp_{$employeeId}");
				return back()->with('error', 'You missed the time-in window. Marked Absent.');
			}

			return back()->with('error', 'Already timed in today.');
		}
	}

	if ($action === 'time_out') {
		// Too early
		if ($now->lt($timeOutStart)) {
			return back()->with('error', 'Time Out is only allowed starting 5:00 PM.');
		}

		// Within time-out window: complete and mark Present
		if ($now->between($timeOutStart, $timeOutEnd)) {
			if ($attendance && $attendance->time_in && !$attendance->time_out) {
				$attendance->update([
					'time_out' => $now->format('H:i:s'),
					'status'   => 'Present', // Present when both time_in and time_out are set
				]);

				Cache::forget("otp_{$employeeId}");
				return back()->with('success', 'Time Out recorded. Status set to Present.');
			}

			return back()->with('error', 'No valid time-in found today or already timed out.');
		}

		// After time-out window: if time_in exists but time_out missing, mark Absent
		if ($now->gt($timeOutEnd)) {
			if ($attendance && $attendance->time_in && !$attendance->time_out) {
				$attendance->update([
					'status' => 'Absent',
				]);

				Cache::forget("otp_{$employeeId}");
				return back()->with('error', 'You missed the time-out window. Status set to Absent.');
			}

			return back()->with('error', 'No valid time-in found today or already timed out.');
		}
	}

	Cache::forget("otp_{$employeeId}");
	return back()->with('error', 'Invalid action.');
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

