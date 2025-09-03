<?php

namespace App\Http\Controllers;

use App\Models\Salaries;
use App\Models\Attendance;
use App\Models\Employeeprofiles;
use App\Models\Leaveovertimerequest;
use App\Models\Payroll;
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
                'deductions'          => 0,
                'bonuses'             => 'none as of the moment',
                'net_pay'             => 0,
                'status'              => 'Pending',
                'pay_period_start'    => $payPeriod['pay_period_start'],
                'pay_period_end'      => $payPeriod['pay_period_end'],
                'pay_period'          => $payPeriod['pay_period_start'] . ' to ' . $payPeriod['pay_period_end'],
            ]);
        } else {
            
            $existingPayroll->update([
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


      public function submitSummary(Request $request)
    {
        // ✅ Validate inputs
        $validated = $request->validate([
            'employeeprofiles_id' => 'required|exists:employeeprofiles,employeeprofiles_id',
            'overtime_pay'        => 'nullable|numeric',
            'deductions'          => 'nullable|numeric',
            'bonuses'             => 'nullable|string',
            'pay_period_start'    => 'required|date',
            'pay_period_end'      => 'required|date',
        ]);

        // ✅ Fetch employee
        $employee = Employeeprofiles::findOrFail($validated['employeeprofiles_id']);

        // ✅ Auto-count total days of work from attendances table
        $totalDaysOfWork = Attendance::where('employeeprofiles_id', $employee->employeeprofiles_id)
            ->where('status', 'Present')
            ->count();

        // ✅ Basic salary (readonly - taken from DB, not from request)
        $basicSalary = $employee->basic_salary;

        // ✅ Compute net pay
        $overtime   = $validated['overtime_pay'] ?? 0;
        $deductions = $validated['deductions'] ?? 0;
        $bonuses    = $validated['bonuses'] ?? 'none as of the moment';

        $netPay = ($basicSalary + $overtime) - $deductions;

        // ✅ Create new payroll record (not update)
        Payroll::create([
            'employeeprofiles_id' => $employee->employeeprofiles_id,
            'basic_salary'        => $basicSalary,
            'total_days_of_work'  => $totalDaysOfWork,
            'overtime_pay'        => $overtime,
            'deductions'          => $deductions,
            'bonuses'             => $bonuses,
            'net_pay'             => $netPay,
            'status'              => 'Pending',
            'pay_period_start'    => $validated['pay_period_start'],
            'pay_period_end'      => $validated['pay_period_end'],
            'pay_period'          => $validated['pay_period_start'] . ' to ' . $validated['pay_period_end'],
        ]);

        // ✅ Redirect to payroll list (avoid GET error on submit-summary)
        return redirect()->route('payroll.index')
            ->with('success', 'Payroll summary submitted successfully!');
    }

}
