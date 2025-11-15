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
            ->where('status', 'Present')
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

// // ----- Basic salary & gross pay -----
// $basicSalary = $salaryRate * $totalDaysOfWork;
// $daysInMonth = cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y'));
// $basicSalaryTax = $salaryRate * $daysInMonth;

// $overtimePay = OvertimeRequest::where('employeeprofiles_id', $employee->employeeprofiles_id)
//     ->where('status', 'Approved')
//     ->whereBetween('approved_date', [$payPeriod['pay_period_start'], $payPeriod['pay_period_end']])
//     ->sum('amount') ?? 0;

// $grossPay = $basicSalary + $overtimePay + $bonusAmount;

// // ----- Deductions -----
// $tax_deduction = 0;
// $sss_contribution = 0;
// $philhealth_contribution = 0;
// $pagibig_contribution = 0;

// $isSecondPay = date('d', strtotime($payPeriod['pay_period_end'])) > 15;
// $taxThreshold = 20833;

// // Calculate the *full* monthly deductions first
// if ($basicSalaryTax > $taxThreshold) {
//     $annualGross = $grossPay * 12;
//     $taxableIncome = max(0, $annualGross - 250000);
//     $annualIncomeTax = $taxableIncome * 0.15;
//     $monthlyTax = $annualIncomeTax / 12;
// } else {
//     $monthlyTax = 0;
// }

// $full_sss = $grossPay * 0.05;
// $full_philhealth = ($grossPay * 0.05) / 2;
// $full_pagibig = ($grossPay <= 1500) ? $grossPay * 0.01 : min($grossPay, 5000) * 0.02;

// // ----- Apply half per cutoff -----
// $sss_contribution = $full_sss / 2;
// $philhealth_contribution = $full_philhealth / 2;
// $pagibig_contribution = $full_pagibig / 2;
// $tax_deduction = $monthlyTax / 2;



  // ------------------ VIEW PAYROLL ------------------
public function viewPayroll(Request $request)
{
    $search = $request->input('search');
    $employees = Employeeprofiles::all();
    $payPeriod = $this->getCurrentPayPeriod();

    foreach ($employees as $employee) {

        // ----- Salary rate -----
        $salaryRecord = SalaryRate::whereRaw('TRIM(LOWER(position)) = ?', [strtolower(trim($employee->position))])->first();
        $salaryRate = $salaryRecord ? $salaryRecord->salary_rate : 0;

        // ----- Days worked -----
        $totalDaysOfWork = Attendance::where('employeeprofiles_id', $employee->employeeprofiles_id)
            ->whereBetween('date', [$payPeriod['pay_period_start'], $payPeriod['pay_period_end']])
            ->where('status', 'Present')
            ->count();

        // ----- Bonuses -----
        $bonusRecords = Bonus::where('employeeprofiles_id', $employee->employeeprofiles_id)
            ->whereBetween('bonus_date', [$payPeriod['pay_period_start'], $payPeriod['pay_period_end']])
            ->get();

        $bonusTypes = $bonusRecords->pluck('bonus_type')->unique()->implode(', ');
        $bonusAmount = $bonusRecords->sum('bonus_amount');

        if ($bonusAmount == 0) {
            $bonusTypes = 'none';
        }

        // ----- Basic salary & gross pay -----
        $basicSalary = $salaryRate * $totalDaysOfWork;
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y'));
        $basicSalaryTax = $salaryRate * $daysInMonth;

        $overtimePay = OvertimeRequest::where('employeeprofiles_id', $employee->employeeprofiles_id)
            ->where('status', 'Approved')
            ->whereBetween('approved_date', [$payPeriod['pay_period_start'], $payPeriod['pay_period_end']])
            ->sum('amount') ?? 0;

        $grossPay = $basicSalary + $overtimePay + $bonusAmount;

        // ----- Deductions -----
        $tax_deduction = 0;
        $sss_contribution = 0;
        $philhealth_contribution = 0;
        $pagibig_contribution = 0;

        $isSecondPay = date('d', strtotime($payPeriod['pay_period_end'])) > 14;
        $taxThreshold = 20833;

        if ($isSecondPay && $basicSalaryTax > $taxThreshold) {
            $annualGross = $grossPay * 12;
            $taxableIncome = max(0, $annualGross - 250000);
            $annualIncomeTax = $taxableIncome * 0.15;
            $tax_deduction = $annualIncomeTax / 12;

            $sss_contribution = $grossPay * 0.05;
            $philhealth_contribution = ($grossPay * 0.05) / 2;
            $pagibig_contribution = ($grossPay <= 1500) ? $grossPay * 0.01 : min($grossPay, 5000) * 0.02;
        }

        // ✅ Fetch cash advance (nullable)
        $cashAdvance = \App\Models\CashAdvance::where('employeeprofiles_id', $employee->employeeprofiles_id)
            ->whereBetween('filed_date', [$payPeriod['pay_period_start'], $payPeriod['pay_period_end']])
            ->sum('amount') ?? 0;

        // ----- Compute total deductions -----
        $total_deductions = $tax_deduction + $sss_contribution + $philhealth_contribution + $pagibig_contribution + $cashAdvance;

        $netPay = round((float)$grossPay - (float)$total_deductions, 2);

        // ----- Create or update Payroll -----
        $existingPayroll = Payroll::where('employeeprofiles_id', $employee->employeeprofiles_id)
            ->where('pay_period_start', $payPeriod['pay_period_start'])
            ->where('pay_period_end', $payPeriod['pay_period_end'])
            ->first();

        $payrollData = [
            'employeeprofiles_id'     => $employee->employeeprofiles_id,
            'salary_rate'             => $salaryRate,
            'basic_salary'            => $basicSalary,
            'total_days_of_work'      => $totalDaysOfWork,
            'overtime_pay'            => $overtimePay,
            'gross_pay'               => $grossPay,
            'tax_deduction'           => $tax_deduction,
            'sss_contribution'        => $sss_contribution,
            'philhealth_contribution' => $philhealth_contribution,
            'pagibig_contribution'    => $pagibig_contribution,
            'cash_advance'            => $cashAdvance, // ✅ NEW FIELD
            'deductions'              => $total_deductions,
            'bonuses'                 => $bonusTypes,
            'bonus_amount'            => $bonusAmount,
            'net_pay'                 => $netPay,
            'status'                  => 'Pending',
            'pay_period_start'        => $payPeriod['pay_period_start'],
            'pay_period_end'          => $payPeriod['pay_period_end'],
            'pay_period'              => $payPeriod['pay_period_start'] . ' to ' . $payPeriod['pay_period_end'],
        ];

        if (!$existingPayroll) {
            Payroll::create($payrollData);
        } else {
            $existingPayroll->update($payrollData);
        }
    }

    // ----- Pagination and search -----
    $payroll = Payroll::with('employeeprofiles')
        ->when($search, function ($query, $search) {
            $query->whereHas('employeeprofiles', function ($q) use ($search) {
                $q->where('first_name', 'LIKE', "%$search%")
                  ->orWhere('last_name', 'LIKE', "%$search%")
                  ->orWhere('employeeprofiles_id', 'LIKE', "%$search%");
            })->orWhere('pay_period', 'LIKE', "%$search%");
        })
        ->paginate(10);

    return view('HR.view_payroll', compact('payroll', 'search'));
}


    // ------------------ HELPER: Get current pay period ------------------
    private function getCurrentPayPeriod()
    {
        $today = now();
        $month = $today->month;
        $year = $today->year;

        // Example: first half and second half
        if ($today->day <= 15) {
            $start = date("$year-$month-01");
            $end = date("$year-$month-14"); //I EDIT IT HERE
        } else {
            $start = date("$year-$month-16");
            $end = date("$year-$month-" . cal_days_in_month(CAL_GREGORIAN, $month, $year));
        }

        return [
            'pay_period_start' => $start,
            'pay_period_end' => $end,
        ];
    }


    // private function getCurrentPayPeriod()
    // {
    //     $today = now();
    //     $year = $today->year;
    //     $month = $today->month;
    //     $day = $today->day;

    //     if ($day <= 15) {
    //         $start = now()->setDate($year, $month, 1);
    //         $end   = now()->setDate($year, $month, 15);
    //     } else {
    //         $start = now()->setDate($year, $month, 16);
    //         $end   = now()->setDate($year, $month, $today->daysInMonth);
    //     }

    //     return [
    //         'pay_period_start' => $start->format('Y-m-d'),
    //         'pay_period_end'   => $end->format('Y-m-d'),
    //     ];
    // }



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
}
