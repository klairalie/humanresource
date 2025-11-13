<?php

namespace App\Observers;

use App\Models\Employeeprofiles;
use App\Models\ActivityLog;

class EmployeeProfileObserver
{
    public function created(Employeeprofiles $employee)
    {
        ActivityLog::create([
            'action_type' => 'New Employee Added',
            'email' => session('user_email') ?? 'user@example.com', // permanently stored in DB
            'description' => "Added new employee: {$employee->first_name} {$employee->last_name}",
        ]);
    }

    public function updated(Employeeprofiles $employee)
    {
        $actorEmail = session('user_email') ?? 'user@example.com';
   
        // Check if 'status' column changed
        if ($employee->isDirty('status')) {
            $oldStatus = $employee->getOriginal('status');
            $newStatus = $employee->status;

            if ($newStatus === 'active') {
                ActivityLog::create([
                    'action_type' => 'Employee Reactivated',
                    'email' => $actorEmail,
                    'description' => "Reactivated employee: {$employee->first_name} {$employee->last_name}",
                ]);
            } elseif ($newStatus === 'inactive') {
                ActivityLog::create([
                    'action_type' => 'Employee Deactivated',
                    'email' => $actorEmail,
                    'description' => "Deactivated employee: {$employee->first_name} {$employee->last_name}",
                ]);
            } else {
                ActivityLog::create([
                    'action_type' => 'Employee Status Changed',
                    'email' => $actorEmail,
                    'description' => "Changed status of {$employee->first_name} {$employee->last_name} from '{$oldStatus}' to '{$newStatus}'.",
                ]);
            }
        } else {
            // Log regular updates
            ActivityLog::create([
                'action_type' => 'Employee Updated',
                'email' => $actorEmail,
                'description' => "Updated employee: {$employee->first_name} {$employee->last_name}",
            ]);
        }
    }

    public function deleted(Employeeprofiles $employee)
    {
        ActivityLog::create([
            'action_type' => 'Employee Deleted',
            'email' => session('user_email') ?? 'user@example.com',
            'description' => "Deleted employee: {$employee->first_name} {$employee->last_name}",
        ]);
    }

    public function restored(Employeeprofiles $employee)
    {
        ActivityLog::create([
            'action_type' => 'Employee Reactivated', // or 'Employee Restored'
            'email' => session('user_email') ?? 'user@example.com',
            'description' => "Restored employee: {$employee->first_name} {$employee->last_name}",
        ]);
    }

    public function deactivated(Employeeprofiles $employee)
    {
        ActivityLog::create([
            'action_type' => 'Employee Deactivated',
            'email' => session('user_email') ?? 'user@example.com',
            'description' => "Deactivated employee: {$employee->first_name} {$employee->last_name}",
        ]);
    }
}
