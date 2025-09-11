<?php

namespace App\Http\Controllers;

use App\Models\Salaries;
use App\Models\Attendance;
use App\Models\Employeeprofiles;
use App\Models\Leaveovertimerequest;
use App\Models\Payroll;
use App\Models\Deduction;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    public function viewPayroll(Request $request)
    {
        $search = $request->input('search');
        $employees = Employeeprofiles::all();
        $payPeriod = $this->getCurrentPayPeriod();

        foreach ($employees as $employee) {
            $salaryRecord = Salaries::whereRaw('TRIM(LOWER(position)) = ?', [strtolower(trim($employee->position))])->first();
            $basicSalary = $salaryRecord ? $salaryRecord->basic_salary : 0;

            $deduction = Deduction::where('employeeprofiles_id', $employee->employeeprofiles_id)
                ->whereBetween('deduction_date', [$payPeriod['pay_period_start'], $payPeriod['pay_period_end']])
                ->sum('amount');

            $totalDaysOfWork = Attendance::where('employeeprofiles_id', $employee->employeeprofiles_id)
                ->whereBetween('date', [$payPeriod['pay_period_start'], $payPeriod['pay_period_end']])
                ->where('status', 'Present')
                ->count();

            $overtimePay = 100 * Leaveovertimerequest::where('employeeprofiles_id', $employee->employeeprofiles_id)
                ->whereBetween('request_date', [$payPeriod['pay_period_start'], $payPeriod['pay_period_end']])
                ->sum('overtime_hours');

            $existingPayroll = Payroll::where('employeeprofiles_id', $employee->employeeprofiles_id)
                ->where('pay_period_start', $payPeriod['pay_period_start'])
                ->where('pay_period_end', $payPeriod['pay_period_end'])
                ->first();

            if (!$existingPayroll) {
                Payroll::create([
                    'employeeprofiles_id' => $employee->employeeprofiles_id,
                    'basic_salary'        => $basicSalary,
                    'total_days_of_work'  => $totalDaysOfWork,
                    'overtime_pay'        => $overtimePay,
                    'deductions'          => $deduction,
                    'bonuses'             => 'none as of the moment',
                    'status'              => 'Pending',
                    'pay_period_start'    => $payPeriod['pay_period_start'],
                    'pay_period_end'      => $payPeriod['pay_period_end'],
                    'pay_period'          => $payPeriod['pay_period_start'] . ' to ' . $payPeriod['pay_period_end'],
                ]);
            } else {
                $existingPayroll->update([
                    'deductions'         => $deduction,
                    'basic_salary'       => $basicSalary,
                    'total_days_of_work' => $totalDaysOfWork,
                    'overtime_pay'       => $overtimePay,
                ]);
            }
        }

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

    public function showPayrollform()
    {
        return view('HR.payrollform');
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
    // Validate the request
    $validated = $request->validate([
        'employeeprofiles_id' => 'required|exists:employeeprofiles,employeeprofiles_id',
        'total_days_of_work'  => 'required|integer',
        'basic_salary'        => 'required|numeric',
        'overtime_pay'        => 'required|numeric',
        'deductions'          => 'nullable|string',
        'bonuses'             => 'nullable|string',
        'pay_period_start'    => 'required|date',
        'pay_period_end'      => 'required|date',
        'pay_period'          => 'required|string',
    ]);

    // Merge defaults and status
    $data = array_merge($validated, [
        'deductions' => $validated['deductions'] ?? 'no deduction',
        'bonuses'    => $validated['bonuses'] ?? 'none as of the moment',
        'status'     => 'Pending'
    ]);

    // Create a new payroll record
    Payroll::create($data);

    // Return JSON response for AJAX
    return response()->json([
        'success' => true,
        'message' => 'Payroll record stored successfully!'
    ]);
}

public function getEmployeePayroll($employeeprofiles_id)
{
    $records = Payroll::where('employeeprofiles_id', $employeeprofiles_id)->get();
    return response()->json($records);
}

// public function updatePayroll(Request $request, $payroll_id)
// {
//     $validated = $request->validate([
//         'total_days_of_work'  => 'required|integer',
//         'basic_salary'        => 'required|numeric',
//         'overtime_pay'        => 'required|numeric',
//         'deductions'          => 'nullable|string',
//         'bonuses'             => 'nullable|string',
//         'pay_period_start'    => 'required|date',
//         'pay_period_end'      => 'required|date',
//         'pay_period'          => 'required|string',
//     ]);

//     $payroll = Payroll::findOrFail($payroll_id);

//     $payroll->update([
//         'total_days_of_work' => $validated['total_days_of_work'],
//         'basic_salary'       => $validated['basic_salary'],
//         'overtime_pay'       => $validated['overtime_pay'],
//         'deductions'         => $validated['deductions'] ?? 'no deduction',
//         'bonuses'            => $validated['bonuses'] ?? 'none as of the moment',
//         'pay_period_start'   => $validated['pay_period_start'],
//         'pay_period_end'     => $validated['pay_period_end'],
//         'pay_period'         => $validated['pay_period'],
//         'status'             => 'Updated',
//     ]);

//     return response()->json([
//         'success' => true,
//         'message' => 'Payroll record updated successfully!',
//         'updated_record' => $payroll
//     ]);
// }

}
