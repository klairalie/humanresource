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
            'employeeprofiles_id' => Auth::id(),
            'description' => "Added new employee: {$employee->first_name} {$employee->last_name}"
        ]);
    }

    public function updated(Employeeprofiles $employee)
    {
        // Check if the 'is_active' status changed
        if ($employee->isDirty('reactivated')) {
            if ($employee->is_active) {
                ActivityLog::create([
                    'action_type' => 'Employee Reactivated',
                    'employeeprofiles_id' => Auth::id(),
                    'description' => "Reactivated employee: {$employee->first_name} {$employee->last_name}"
                ]);
            } else {
                ActivityLog::create([
                    'action_type' => 'Employee Deactivated',
                    'employeeprofiles_id' => Auth::id(),
                    'description' => "Deactivated employee: {$employee->first_name} {$employee->last_name}"
                ]);
            }
        } else {
            // Regular update log
            ActivityLog::create([
                'action_type' => 'Employee Updated',
                'employeeprofiles_id' => Auth::id(),
                'description' => "Updated employee: {$employee->first_name} {$employee->last_name}"
            ]);
        }
    }

    public function deleted(Employeeprofiles $employee)
    {
        ActivityLog::create([
            'action_type' => 'Employee Deleted',
            'employeeprofiles_id' => Auth::id(),
            'description' => "Deleted employee: {$employee->first_name} {$employee->last_name}"
        ]);
    }
}
