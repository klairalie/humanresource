<?php

namespace App\Observers;

use App\Models\Employeeprofiles;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class EmployeeProfileObserver
{
    public function created(Employeeprofiles $employee)
    {
        ActivityLog::create([
            'action_type' => 'New Employee Added',
            'applicant_id' => Auth::id(),
            'description' => "Added new employee: {$employee->first_name} {$employee->last_name}"
        ]);
    }

    public function updated(Employeeprofiles $employee)
    {
        ActivityLog::create([
            'action_type' => 'Employee Updated',
            'applicant_id' => Auth::id(),
            'description' => "Updated employee: {$employee->first_name} {$employee->last_name}"
        ]);
    }

    public function deleted(Employeeprofiles $employee)
    {
        ActivityLog::create([
            'action_type' => 'Employee Deleted',
            'applicant_id' => Auth::id(),
            'description' => "Deleted employee: {$employee->first_name} {$employee->last_name}"
        ]);
    }
}
