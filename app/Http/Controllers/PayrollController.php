<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employeeprofiles;
use App\Models\Payroll;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    public function viewpayroll()
    {

        $payroll = Payroll::with('employeeprofiles')->get();

        return view('HR.view_payroll', ["payroll" => $payroll]);
    }


    public function showPayrollform()
    {

        return view('HR.payrollform');
    }

public function createPayroll(Request $request)
{
    $validated = $request->validate([
        'basic_salary' => ['required', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
    ]);

   
    $latestEmployee = Employeeprofiles::orderBy('employeeprofiles_id', 'desc')->first();

    if (!$latestEmployee) {
        return redirect()->back()->withErrors('No employee profiles found. Please add an employee first.');
    }

    
    $payPeriod = $this->getCurrentPayPeriod();

    
    $existingPayroll = Payroll::where('employeeprofiles_id', $latestEmployee->employeeprofiles_id)
        ->where('pay_period_start', $payPeriod['pay_period_start'])
        ->where('pay_period_end', $payPeriod['pay_period_end'])
        ->first();

   if ($existingPayroll) {
    $employee = $existingPayroll->employeeprofiles; // Make sure relationship is set in Payroll model
    $employeeId = $employee->employeeprofiles_id ?? 'Unknown ID';
    $employeeName = trim(($employee->first_name ?? '') . ' ' . ($employee->last_name ?? ''));

    return redirect()->back()->withErrors(
        "Payroll already exists for Employee #{$employeeId} - {$employeeName} in the current pay period."
    );
}

 
    Payroll::create([
        'employeeprofiles_id' => $latestEmployee->employeeprofiles_id,
        'first_name'          => $latestEmployee->first_name,
        'last_name'           => $latestEmployee->last_name,
        'basic_salary'        => $validated['basic_salary'],
        'pay_period_start'    => $payPeriod['pay_period_start'],
        'pay_period_end'      => $payPeriod['pay_period_end'],
        'pay_period'          => $payPeriod['pay_period_start'] . ' to ' . $payPeriod['pay_period_end'], // ADD THIS
    ]);

    return redirect()->route('view.payroll')->with('success', 'Payroll created successfully!');
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




}