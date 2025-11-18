<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employeeprofiles;
use App\Models\Payroll;
use App\Models\SalaryRate;
use Illuminate\Http\Request;
use App\Models\OvertimeRequest;
use App\Models\Bonus;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PayrollExport;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf as PDF; // Corrected alias

class PayrollController extends Controller
{
// ------------------ APPLY BONUS TO PRESENT EMPLOYEES ------------------
public function applyBonusToPresent(Request $request)
{
    $validated = $request->validate([
        'bonus_type' => 'required|string',
        'bonus_amount' => 'nullable|numeric|min:0',
    ]);

    $bonusType = $validated['bonus_type'];
    $bonusInputAmount = $validated['bonus_amount'] ?? null;
    $count = 0;
    $today = now()->toDateString();
    $payPeriod = $this->getCurrentPayPeriod();
    $year = now()->year;

    // Holiday checks remain unchanged
    if (in_array($bonusType, ['Holiday (special)', 'Holiday (regular)'])) {
        $isHoliday = DB::table('holidays')->whereDate('holiday_date', $today)->exists();
        if (!$isHoliday) {
            return response()->json([
                'success' => false,
                'message' => "Today ({$today}) is not a holiday. You cannot apply {$bonusType}."
            ], 403);
        }
    }

    // SPECIAL CASE: 13th Month Pay -> apply to all employees (or those with payroll in the year)
    if ($bonusType === '13th Month Pay (Mandatory)') {
    $year = now()->year;

    // Prevent global reapplication
    $alreadyApplied = Bonus::where('bonus_type', '13th Month Pay (Mandatory)')
        ->whereYear('bonus_date', $year)
        ->exists();

    if ($alreadyApplied) {
        return response()->json([
            'success' => false,
            'message' => "13th Month Pay has already been applied for year {$year}.",
        ], 400);
    }

    $employees = Employeeprofiles::all();
    $count = 0;
    $today = now()->toDateString();

    foreach ($employees as $employee) {
        $employeeId = $employee->employeeprofiles_id ?? $employee->id;

        // Extra safety: ensure not duplicated per employee
        $alreadyReceived = Bonus::where('employeeprofiles_id', $employeeId)
            ->where('bonus_type', '13th Month Pay (Mandatory)')
            ->whereYear('bonus_date', $year)
            ->exists();

        if ($alreadyReceived) continue;

        $totalBasicSalary = Payroll::where('employeeprofiles_id', $employeeId)
            ->whereYear('pay_period_start', $year)
            ->sum('basic_salary');

        $bonusAmount = $totalBasicSalary / 12;

        Bonus::create([
            'employeeprofiles_id' => $employeeId,
            'bonus_type' => '13th Month Pay (Mandatory)',
            'bonus_amount' => $bonusAmount,
            'bonus_date' => $today,
        ]);

        $count++;
    }

    return response()->json([
        'success' => true,
        'count' => $count,
        'message' => " 13th Month Pay applied successfully to {$count} employee(s) for year {$year}.",
    ]);
}


    // EXISTING FLOW (for Holiday/Christmas/manual) - unchanged behavior
    $presentAttendances = Attendance::with('employeeprofiles')
        ->where('status', 'Present')
        ->whereDate('date', $today)
        ->get();

    if ($presentAttendances->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => "Can't apply bonus ({$bonusType}) — no Present employees."
        ], 200);
    }

    foreach ($presentAttendances as $attendance) {
        $employee = $attendance->employeeprofiles;
        if (!$employee) continue;

        $employeeId = $employee->id ?? $employee->employeeprofiles_id;

        $existingBonus = Bonus::where('employeeprofiles_id', $employeeId)
            ->where('bonus_type', $bonusType)
            ->whereDate('bonus_date', $today)
            ->exists();

        if ($existingBonus) {
            continue; // skip duplicate bonus for today
        }

        $salaryRecord = SalaryRate::whereRaw('TRIM(LOWER(position)) = ?', [strtolower(trim($employee->position))])->first();
        $salaryRate = $salaryRecord ? $salaryRecord->salary_rate : 0;

        switch ($bonusType) {
            case 'Holiday (special)':
                $bonusAmount = $salaryRate * 1.30;
                break;

            case 'Holiday (regular)':
                $bonusAmount = $salaryRate * 2.00;
                break;

           case 'Christmas Bonus':
    if ($bonusInputAmount === null) {
        return response()->json([
            'success' => false,
            'message' => 'Please input the Christmas Bonus amount before applying.'
        ], 400);
    }
    $bonusAmount = $bonusInputAmount;
    break;


            default:
                $bonusAmount = $bonusInputAmount ?? 0;
                break;
        }

        // ensure present today (double-check)
        $isPresentToday = Attendance::where('employeeprofiles_id', $employeeId)
            ->whereDate('date', $today)
            ->whereIn('status', ['Present', 'Present - Undertime', 'Present - Halfday'])
            ->exists();

        if (!$isPresentToday) continue;

        Bonus::create([
            'employeeprofiles_id' => $employeeId,
            'bonus_type' => $bonusType,
            'bonus_amount' => $bonusAmount,
            'bonus_date' => $today,
        ]);

        $count++;
    }

    return response()->json([
        'success' => true,
        'count' => $count,
        'message' => "{$bonusType} applied successfully to {$count} employee(s) (Present today only)."
    ]);
}



public function viewPayroll(Request $request)
{
    $search = $request->input('search');
    $employees = Employeeprofiles::all();
    $payPeriod = $this->getCurrentPayPeriod();

    foreach ($employees as $employee) {

        // Get salary rate
        $salaryRecord = SalaryRate::whereRaw('TRIM(LOWER(position)) = ?', [strtolower(trim($employee->position))])->first();
        $salaryRate = $salaryRecord ? $salaryRecord->salary_rate : 0;

        $basicSalary = 0;

        // Get attendance records within pay period
        $attendanceRecords = Attendance::where('employeeprofiles_id', $employee->employeeprofiles_id)
            ->whereBetween('date', [$payPeriod['pay_period_start'], $payPeriod['pay_period_end']])
            ->whereIn('status', [
                'Present',
                'Present - Undertime',
                'Present - Halfday',
                'Late - Present',
                'Late - On Duty',
            ])
            ->get();

        $totalDaysOfWork = $attendanceRecords->count();

        // Lateness calculation
        $hourlyRateLate = 20; // per hour
        $perMinuteLateRate = $hourlyRateLate / 60; // per minute
        $totalLateMinutes = 0;

        $lateRecords = $attendanceRecords->whereIn('status', ['Late - Present', 'Late - On Duty']);

        foreach ($lateRecords as $att) {
            if (!empty($att->time_in)) {
                $timeIn = Carbon::parse($att->time_in);
                $cutOff = Carbon::parse($att->date . ' 08:00:00');
                if ($timeIn->gt($cutOff)) {
                    $totalLateMinutes += $timeIn->diffInMinutes($cutOff);
                }
            }
        }

        $lateDeduction = $totalLateMinutes * $perMinuteLateRate;

        // Compute daily pay
        foreach ($attendanceRecords as $att) {
            $status = $att->status;

            if ($status === 'Present') {
                $basicSalary += $salaryRate;
            } elseif ($status === 'Late - Present' || $status === 'Late - On Duty') {
                $basicSalary += $salaryRate; // apply lateness as deduction separately
            } elseif ($status === 'Present - Halfday') {
                $basicSalary += $salaryRate / 2;
            } elseif ($status === 'Present - Undertime') {
                $hoursWorked = 0;

                if (!empty($att->time_in) && !empty($att->time_out)) {
                    try {
                        $in = Carbon::parse($att->time_in);
                        $out = Carbon::parse($att->time_out);

                        $morningEnd = Carbon::createFromTimeString('12:00:00');
                        $afternoonStart = Carbon::createFromTimeString('13:00:00');

                        $morningMinutes = 0;
                        $afternoonMinutes = 0;

                        // Morning session
                        if ($in->lt($morningEnd)) {
                            $morningEndTime = $out->lt($morningEnd) ? $out : $morningEnd;
                            $morningMinutes = $in->diffInMinutes($morningEndTime);
                        }

                        // Afternoon session
                        if ($out->gt($afternoonStart)) {
                            $afternoonStartTime = $in->gt($afternoonStart) ? $in : $afternoonStart;
                            $afternoonMinutes = $afternoonStartTime->diffInMinutes($out);
                        }

                        $totalMinutes = max(0, $morningMinutes) + max(0, $afternoonMinutes);
                        $hoursWorked = $totalMinutes / 60;

                        // Adjustments for business rules
                        if ($out->format('H:i') <= '11:59') {
                            $hoursWorked = $morningMinutes / 60;
                        } elseif ($out->format('H:i') >= '13:00' && $out->format('H:i') <= '16:59') {
                            $hoursWorked = min(9, $hoursWorked);
                        }

                    } catch (\Exception $e) {
                        $hoursWorked = 0;
                    }
                }

                $standardHours = 9;
                $undertimeHours = max(0, $standardHours - $hoursWorked);
                $hourlyRateCalc = $salaryRate / $standardHours;
                $dailyPay = max(0, $salaryRate - ($hourlyRateCalc * $undertimeHours));
                $basicSalary += $dailyPay;
            }
        }

        // Bonuses
        $bonusRecords = Bonus::where('employeeprofiles_id', $employee->employeeprofiles_id)
            ->whereBetween('bonus_date', [$payPeriod['pay_period_start'], $payPeriod['pay_period_end']])
            ->get();

        $bonusTypes = $bonusRecords->pluck('bonus_type')->unique()->implode(', ') ?: 'none';
        $bonusAmount = $bonusRecords->sum('bonus_amount');

        // Overtime
        $overtimePay = OvertimeRequest::where('employeeprofiles_id', $employee->employeeprofiles_id)
            ->where('status', 'Approved')
            ->whereBetween('approved_date', [$payPeriod['pay_period_start'], $payPeriod['pay_period_end']])
            ->sum('amount');

        $grossPay = $basicSalary + $overtimePay + $bonusAmount;

        // Cash advances
        $cashAdvance = \App\Models\CashAdvance::where('employeeprofiles_id', $employee->employeeprofiles_id)
            ->whereBetween('filed_date', [$payPeriod['pay_period_start'], $payPeriod['pay_period_end']])
            ->sum('amount');

        $netPay = $grossPay - $cashAdvance - abs($lateDeduction);

        // Save or update payroll
        $existingPayroll = Payroll::where('employeeprofiles_id', $employee->employeeprofiles_id)
            ->where('pay_period_start', $payPeriod['pay_period_start'])
            ->where('pay_period_end', $payPeriod['pay_period_end'])
            ->first();

        $payrollData = [
            'employeeprofiles_id' => $employee->employeeprofiles_id,
            'salary_rate' => $salaryRate,
            'basic_salary' => $basicSalary,
            'total_days_of_work' => $totalDaysOfWork,
            'overtime_pay' => $overtimePay,
            'gross_pay' => $grossPay,
            'cash_advance' => $cashAdvance,
            'late_deduction' => abs($lateDeduction),
            'deductions' => abs($lateDeduction) + $cashAdvance,
            'bonuses' => $bonusTypes,
            'bonus_amount' => $bonusAmount,
            'net_pay' => $netPay,
            'status' => 'Pending',
            'pay_period_start' => $payPeriod['pay_period_start'],
            'pay_period_end' => $payPeriod['pay_period_end'],
            'pay_period' => $payPeriod['pay_period_start'] . ' to ' . $payPeriod['pay_period_end'],
        ];

        if (!$existingPayroll) {
            Payroll::create($payrollData);
        } else {
            $existingPayroll->update($payrollData);
        }
    } // end foreach employees

    // Fetch payroll for view
    $payroll = Payroll::with('employeeprofiles')
        ->whereIn('payroll_id', function ($sub) {
            $sub->selectRaw('MAX(payroll_id)')
                ->from('payrolls')
                ->groupBy('employeeprofiles_id');
        })
        ->when($search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('employeeprofiles', function ($emp) use ($search) {
                    $emp->where('first_name', 'LIKE', "%$search%")
                        ->orWhere('last_name', 'LIKE', "%$search%")
                        ->orWhere('employeeprofiles_id', 'LIKE', "%$search%");
                })
                ->orWhere('pay_period', 'LIKE', "%$search%");
            });
        })
        ->orderBy('employeeprofiles_id', 'asc')
        ->paginate(10);

    return view('HR.view_payroll', compact('payroll', 'search'));
}



    // ------------------ HELPER: Get current pay period ------------------
  private function getCurrentPayPeriod()
{
    $today = now();
    $year = $today->year;
    $month = $today->month;

    if ($today->day <= 15) {
        $start = Carbon::create($year, $month, 1);
        $end   = Carbon::create($year, $month, 15);
    } else {
        $start = Carbon::create($year, $month, 16);
        $end   = Carbon::create($year, $month, $today->daysInMonth);
    }

    return [
        'pay_period_start' => $start->toDateString(),
        'pay_period_end'   => $end->toDateString(),
    ];
}



  


public function getEmployeePayroll($employeeprofiles_id)
{
    // Get only the latest payroll record per employee
    $record = Payroll::where('employeeprofiles_id', $employeeprofiles_id)
        ->with('employeeprofiles')
        ->orderByDesc('pay_period_end') // latest pay period
        ->first(); // only one record

    if (!$record) {
        return response()->json([], 200);
    }

    return response()->json([$record]); // keep it as an array for frontend consistency
}



public function storePayroll(Request $request)
{
    $validated = $request->validate([
        'payroll_id' => 'required|exists:payrolls,payroll_id',
        'bonuses' => 'nullable|string',
        'bonus_amount' => 'required_with:bonuses|nullable|numeric|min:0',
    ]);

    $payroll = \App\Models\Payroll::findOrFail($validated['payroll_id']);

    if (empty($validated['bonuses'])) {
        $validated['bonus_amount'] = null;
    }

    $payroll->update([
        'bonuses' => $validated['bonuses'],
        'bonus_amount' => $validated['bonus_amount'],
    ]);

    return response()->json(['success' => true]);
}

/**
     * Display payroll records with search functionality
     */
    public function payrollRecord(Request $request)
    {
        $search = $request->input('search');

        // Query unique employees that have payroll records
        $employees = Employeeprofiles::with(['payrolls' => function ($q) {
                $q->orderBy('created_at', 'desc');
            }])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'LIKE', "%$search%")
                      ->orWhere('last_name', 'LIKE', "%$search%")
                      ->orWhere('employeeprofiles_id', 'LIKE', "%$search%");
                });
            })
            ->whereHas('payrolls') // only employees with payroll
            ->orderBy('employeeprofiles_id')
            ->paginate(6);

        return view('HR.payrollrecords', [
            'employees' => $employees,
            'search'    => $search,
        ]);
    }

    /**
     * Filter records via AJAX for modal
     */
    // public function filterRecords(Request $request, $id)
    // {
    //     $employee = Employeeprofiles::where('employeeprofiles_id', $id)->first();
    //     if ($employee) {
    //         $payPeriod = $this->getCurrentPayPeriod();

    //         $salaryRecord = SalaryRate::whereRaw('TRIM(LOWER(position)) = ?', [strtolower(trim($employee->position))])->first();
    //         $salaryRate = $salaryRecord ? $salaryRecord->salary_rate : 0;

    //         $attendanceRecords = Attendance::where('employeeprofiles_id', $employee->employeeprofiles_id)
    //             ->whereBetween('date', [$payPeriod['pay_period_start'], $payPeriod['pay_period_end']])
    //             ->whereIn('status', [
    //                 'Present',
    //                 'Present - Undertime',
    //                 'Present - Halfday',
    //                 'Late - Present',
    //                 'Late - On Duty',
    //             ])
    //             ->get();

    //         $totalDaysOfWork = $attendanceRecords->count();
    //         $hourlyRateLate = 20;
    //         $hourlyRateCalc = ($salaryRate > 0) ? ($salaryRate / 9) : 0;

    //         $basicSalary = 0;
    //         $totalLatenessDeduction = 0.0;
    //         $lateProcessedDates = [];

    //         foreach ($attendanceRecords as $att) {
    //             $status = $att->status;

    //             $thisRowLatenessMinutes = 0;
    //             try {
    //                 if (!empty($att->time_in)) {
    //                     $rawTimeIn = Carbon::parse($att->time_in);
    //                     $attendanceDate = !empty($att->date) ? Carbon::parse($att->date) : null;
    //                     $dateKey = $attendanceDate ? $attendanceDate->toDateString() : $rawTimeIn->toDateString();
    //                     if (!isset($lateProcessedDates[$dateKey])) {
    //                         $cutOffBase = $attendanceDate ? $attendanceDate->copy() : $rawTimeIn->copy();
    //                         $cutOff = $cutOffBase->setTime(8, 0, 0);
    //                         $timeIn = $attendanceDate
    //                             ? $attendanceDate->copy()->setTime($rawTimeIn->hour, $rawTimeIn->minute, $rawTimeIn->second)
    //                             : $rawTimeIn->copy();
    //                         if ($timeIn->gt($cutOff)) {
    //                             $thisRowLatenessMinutes = $timeIn->diffInMinutes($cutOff);
    //                         }
    //                         $lateProcessedDates[$dateKey] = true;
    //                     }
    //                 }
    //             } catch (\Exception $e) {
    //                 $thisRowLatenessMinutes = 0;
    //             }

    //             $thisRowLatenessDeduction = ($hourlyRateLate / 60) * $thisRowLatenessMinutes;
    //             if ($thisRowLatenessDeduction < 0) { $thisRowLatenessDeduction = 0; }
    //             $totalLatenessDeduction += $thisRowLatenessDeduction;

    //             if ($status === 'Present') {
    //                 $basicSalary += $salaryRate;
    //             } elseif ($status === 'Late - Present' || $status === 'Late - On Duty') {
    //                 $basicSalary += $salaryRate;
    //             } elseif ($status === 'Present - Halfday') {
    //                 $basicSalary += ($salaryRate / 2);
    //             } elseif ($status === 'Present - Undertime') {
    //                 $hoursWorked = 0;
    //                 if (!empty($att->time_in) && !empty($att->time_out)) {
    //                     try {
    //                         $in = Carbon::parse($att->time_in);
    //                         $out = Carbon::parse($att->time_out);

    //                         $morningEnd = Carbon::createFromTimeString('12:00:00');
    //                         $afternoonStart = Carbon::createFromTimeString('13:00:00');

    //                         $morningMinutes = 0;
    //                         $afternoonMinutes = 0;

    //                         if ($in->lt($morningEnd)) {
    //                             $morningEndTime = $out->lt($morningEnd) ? $out : $morningEnd;
    //                             $morningMinutes = $in->diffInMinutes($morningEndTime);
    //                         }

    //                         if ($out->gt($afternoonStart)) {
    //                             $afternoonStartTime = $in->gt($afternoonStart) ? $in : $afternoonStart;
    //                             $afternoonMinutes = $afternoonStartTime->diffInMinutes($out);
    //                         }

    //                         $totalMinutes = max(0, $morningMinutes) + max(0, $afternoonMinutes);
    //                         $hoursWorked = $totalMinutes / 60;

    //                         if ($out->format('H:i') <= '11:59') {
    //                             $hoursWorked = $morningMinutes / 60;
    //                         } elseif ($out->format('H:i') >= '13:00' && $out->format('H:i') <= '16:59') {
    //                             $hoursWorked = min(9, $hoursWorked);
    //                         }
    //                     } catch (\Exception $e) {
    //                         $hoursWorked = 0;
    //                     }
    //                 }

    //                 $standardHours = 9;
    //                 $undertimeHours = max(0, $standardHours - $hoursWorked);
    //                 $dailyPay = max(0, $salaryRate - ($hourlyRateCalc * $undertimeHours));
    //                 $basicSalary += $dailyPay;
    //             }
    //         }

    //         $bonusRecords = Bonus::where('employeeprofiles_id', $employee->employeeprofiles_id)
    //             ->whereBetween('bonus_date', [$payPeriod['pay_period_start'], $payPeriod['pay_period_end']])
    //             ->get();
    //         $bonusTypes = $bonusRecords->pluck('bonus_type')->unique()->implode(', ');
    //         $bonusAmount = $bonusRecords->sum('bonus_amount');
    //         if ($bonusAmount == 0) $bonusTypes = 'none';

    //         $overtimePay = OvertimeRequest::where('employeeprofiles_id', $employee->employeeprofiles_id)
    //             ->where('status', 'Approved')
    //             ->whereBetween('approved_date', [$payPeriod['pay_period_start'], $payPeriod['pay_period_end']])
    //             ->sum('amount') ?? 0;

    //         $grossPay = $basicSalary + $overtimePay + $bonusAmount;

    //         $cashAdvance = \App\Models\CashAdvance::where('employeeprofiles_id', $employee->employeeprofiles_id)
    //             ->whereBetween('filed_date', [$payPeriod['pay_period_start'], $payPeriod['pay_period_end']])
    //             ->sum('amount') ?? 0;

    //         $lateDeduction = max(0, $totalLatenessDeduction);
    //         $cashAdvance = max(0, $cashAdvance);
    //         $total_deductions = $cashAdvance + $lateDeduction;

    //         if ($grossPay == 0 && $total_deductions > 0) {
    //             $netPay = -abs($total_deductions);
    //         } else {
    //             $netPay = round($grossPay - $total_deductions, 2);
    //         }

    //         $basicSalary = round($basicSalary, 2);
    //         $grossPay = round($grossPay, 2);
    //         $overtimePay = round($overtimePay, 2);
    //         $bonusAmount = round($bonusAmount, 2);
    //         $cashAdvance = round($cashAdvance, 2);
    //         $lateDeduction = round($lateDeduction, 2);
    //         $total_deductions = round($total_deductions, 2);

    //         $existingPayroll = Payroll::where('employeeprofiles_id', $employee->employeeprofiles_id)
    //             ->where('pay_period_start', $payPeriod['pay_period_start'])
    //             ->where('pay_period_end', $payPeriod['pay_period_end'])
    //             ->first();

    //         $payrollData = [
    //             'employeeprofiles_id'     => $employee->employeeprofiles_id,
    //             'salary_rate'             => $salaryRate,
    //             'basic_salary'            => $basicSalary,
    //             'total_days_of_work'      => $totalDaysOfWork,
    //             'overtime_pay'            => $overtimePay,
    //             'gross_pay'               => $grossPay,
    //             'cash_advance'            => $cashAdvance,
    //             'late_deduction'          => $lateDeduction,
    //             'deductions'              => $total_deductions,
    //             'bonuses'                 => $bonusTypes,
    //             'bonus_amount'            => $bonusAmount,
    //             'net_pay'                 => $netPay,
    //             'status'                  => 'Pending',
    //             'pay_period_start'        => $payPeriod['pay_period_start'],
    //             'pay_period_end'          => $payPeriod['pay_period_end'],
    //             'pay_period'              => $payPeriod['pay_period_start'] . ' to ' . $payPeriod['pay_period_end'],
    //         ];

    //         if (!$existingPayroll) {
    //             Payroll::create($payrollData);
    //         } else {
    //             $existingPayroll->update($payrollData);
    //         }
    //     }

    //     $query = Payroll::where('employeeprofiles_id', $id);

    //     if ($request->has('month') && $request->month !== '') {
    //         $query->whereMonth('created_at', $request->month);
    //     }

    //     if ($request->has('period') && $request->period !== '') {
    //         $query->where('pay_period', 'LIKE', "%{$request->period}%");
    //     }

    //     $records = $query->orderBy('created_at','desc')->get([
    //         'payroll_id',
    //         'pay_period',
    //         'pay_period_start',
    //         'pay_period_end',
    //         'total_days_of_work',
    //         'salary_rate',
    //         'basic_salary',
    //         'overtime_pay',
    //         'cash_advance',
    //         'late_deduction',
    //         'deductions',
    //         'bonuses',
    //         'bonus_amount',
    //         'gross_pay',
    //         'net_pay',
    //         'status',
    //         'created_at'
    //     ]);

    //     return response()->json($records);
    // }

    /**
     * Export to Excel
     */
    public function excel(Employeeprofiles $employee, Request $request)
    {
        return Excel::download(
            new PayrollExport($employee, $request),
            $employee->last_name . '_payroll.xlsx'
        );
    }

    /**
     * Export to PDF - CORRECTED PDF FACADE USAGE
     */
    public function pdf(Employeeprofiles $employee, Request $request)
    {
        $records = $this->getFilteredRecords($employee, $request);

        // Corrected PDF usage
        $pdf = PDF::loadView('exports.payroll_pdf', compact('employee', 'records'));
        return $pdf->download($employee->last_name . '_payroll.pdf');
    }

    /**
     * Print view
     */
    public function print(Employeeprofiles $employee, Request $request)
    {
        $records = $this->getFilteredRecords($employee, $request);
        return view('HR.payroll_print', compact('employee', 'records'));
    }

    /**
     * Company-wide payroll print
     */
    public function printCompany(Request $request)
    {
        // Your company-wide print logic here
        $employees = Employeeprofiles::with(['payrolls' => function($q) use ($request) {
            $this->applyFilters($q, $request);
        }])->whereHas('payrolls')->get();

        return view('HR.payroll_print_company', compact('employees'));
    }

    /**
     * Helper method to get filtered records
     */
    private function getFilteredRecords($employee, $request)
    {
        $query = $employee->payrolls();

        $this->applyFilters($query, $request);

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Helper method to apply filters to query
     */
    private function applyFilters($query, $request)
    {
        if ($request->has('month') && $request->month !== '') {
            $query->whereMonth('created_at', $request->month);
        }

        if ($request->has('period') && $request->period !== '') {
            $query->where('pay_period', 'LIKE', "%{$request->period}%");
        }

        return $query;
    }
}
