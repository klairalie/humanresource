<?php

namespace App\Http\Controllers;

use App\Models\Salaries;
use App\Models\Attendance;
use App\Models\Employeeprofiles;
use App\Models\Leaveovertimerequest;
use App\Models\Payroll;
use App\Models\Deduction;
use App\Models\SalaryRate;
use Illuminate\Http\Request;
use App\Models\OvertimeRequest;

class PayrollController extends Controller
{
    public function viewPayroll(Request $request)
    {
        $search = $request->input('search');
        $employees = Employeeprofiles::all();
        $payPeriod = $this->getCurrentPayPeriod();
        $today = now(); // current date

        foreach ($employees as $employee) {
            // ✅ Get daily salary rate
            $salaryRecord = SalaryRate::whereRaw('TRIM(LOWER(position)) = ?', [strtolower(trim($employee->position))])->first();
            $salaryRate = $salaryRecord ? $salaryRecord->salary_rate : 0;

            // ✅ Count total days worked
            $totalDaysOfWork = Attendance::where('employeeprofiles_id', $employee->employeeprofiles_id)
                ->whereBetween('date', [$payPeriod['pay_period_start'], $payPeriod['pay_period_end']])
                ->where('status', 'Out')
                ->count();

            // ✅ Handle bonuses
            $bonusAmount = 0;
            $bonuses = $request->input('bonuses');

            if ($bonuses === '13th Month Pay(Mandatory)') {
                $year = now()->year;
                $totalBasicSalary = Payroll::where('employeeprofiles_id', $employee->employeeprofiles_id)
                    ->whereYear('pay_period_start', $year)
                    ->sum('basic_salary');

                $bonusAmount = $totalBasicSalary / 12;

                $employee->update([
                    'bonuses' => '13th Month Pay(Mandatory)',
                    'bonus_amount' => $bonusAmount,
                ]);
            }

            // ✅ Compute basic salary and gross pay
            $basicSalary = $salaryRate * $totalDaysOfWork;
            $overtimePay = OvertimeRequest::where('employeeprofiles_id', $employee->employeeprofiles_id)
                ->where('status', 'Approved')
                ->whereBetween('approved_date', [$payPeriod['pay_period_start'], $payPeriod['pay_period_end']])
                ->sum('amount') ?? 0;

            $grossPay = $basicSalary + $overtimePay + $bonusAmount;

            // ✅ Initialize deductions
            $tax_deduction = 0;
            $sss_contribution = 0;
            $philhealth_contribution = 0;
            $pagibig_contribution = 0;
            $total_deductions = 0;

            // ✅ Tax threshold ~20,833/month (250k/year)
            $taxThreshold = 20833;

            if ($basicSalary > $taxThreshold) {
                // Income Tax (simplified first bracket)
                $annualGross = $grossPay * 12;
                $taxableIncome = max(0, $annualGross - 250000);
                $annualIncomeTax = $taxableIncome * 0.15;
                $tax_deduction = $annualIncomeTax / 12;

                // SSS 2025 (5% employee)
                $msc = $grossPay; // approximate MSC 5k–35k
                $sss_contribution = $msc * 0.05;

                // PhilHealth 2025 (5% split)
                $philhealth_contribution = ($grossPay * 0.05) / 2;

                // Pag-IBIG (fixed)
                $pagibig_contribution = 100;
            }

            $total_deductions = $tax_deduction + $sss_contribution + $philhealth_contribution + $pagibig_contribution;

            // ✅ Check if payroll record already exists
            $existingPayroll = Payroll::where('employeeprofiles_id', $employee->employeeprofiles_id)
                ->where('pay_period_start', $payPeriod['pay_period_start'])
                ->where('pay_period_end', $payPeriod['pay_period_end'])
                ->first();

            // ✅ Create or update payroll record
            if (!$existingPayroll) {
                Payroll::create([
                    'employeeprofiles_id' => $employee->employeeprofiles_id,
                    'salary_rate'         => $salaryRate,
                    'basic_salary'        => $basicSalary,
                    'total_days_of_work'  => $totalDaysOfWork,
                    'overtime_pay'        => $overtimePay,
                    'gross_pay'           => $grossPay,
                    'tax_deduction'       => $tax_deduction,
                    'sss_contribution'    => $sss_contribution,
                    'philhealth_contribution' => $philhealth_contribution,
                    'pagibig_contribution'=> $pagibig_contribution,
                    'deductions'          => $total_deductions,
                    'bonuses'             => $bonuses ?? 'none',
                    'bonus_amount'        => $bonusAmount,
                    'net_pay'             => $grossPay - $total_deductions,
                    'status'              => 'Pending',
                    'pay_period_start'    => $payPeriod['pay_period_start'],
                    'pay_period_end'      => $payPeriod['pay_period_end'],
                    'pay_period'          => $payPeriod['pay_period_start'] . ' to ' . $payPeriod['pay_period_end'],
                ]);
            } else {
                $existingPayroll->update([
                    'salary_rate'        => $salaryRate,
                    'basic_salary'       => $basicSalary,
                    'total_days_of_work' => $totalDaysOfWork,
                    'overtime_pay'       => $overtimePay,
                    'gross_pay'          => $grossPay,
                    'tax_deduction'      => $tax_deduction,
                    'sss_contribution'   => $sss_contribution,
                    'philhealth_contribution' => $philhealth_contribution,
                    'pagibig_contribution'=> $pagibig_contribution,
                    'deductions'         => $total_deductions,
                    'bonuses'            => $bonuses ?? 'none',
                    'bonus_amount'       => $bonusAmount,
                    'net_pay'            => $grossPay - $total_deductions,
                    'status'             => 'Pending',
                ]);
            }
        }

        // ✅ Search and paginate results
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



    private function getCurrentPayPeriod()
    {
        $today = now();
        $year = $today->year;
        $month = $today->month;
        $day = $today->day;

        if ($day <= 15) {
            $start = now()->setDate($year, $month, 1);
            $end   = now()->setDate($year, $month, 15);
        } else {
            $start = now()->setDate($year, $month, 16);
            $end   = now()->setDate($year, $month, $today->daysInMonth);
        }

        return [
            'pay_period_start' => $start->format('Y-m-d'),
            'pay_period_end'   => $end->format('Y-m-d'),
        ];
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


public function getEmployeePayroll($employeeprofiles_id)
{
    // Retrieve payroll records for the given employee ID
    $records = Payroll::where('employeeprofiles_id', $employeeprofiles_id)
        ->with('employeeprofiles')
        ->orderByDesc('created_at')
        ->get();

    if ($records->isEmpty()) {
        return response()->json([], 200); // Return empty array if no data
    }

    return response()->json($records);
}


}
