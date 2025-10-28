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
            'employeeprofiles_id' => session('employeeprofiles_id') ?? Auth::user()->employeeprofiles_id ?? null,
            'description' => "Added new employee: {$employee->first_name} {$employee->last_name}"
        ]);
    }

    public function updated(Employeeprofiles $employee)
    {
        $actorId = session('employeeprofiles_id') ?? Auth::user()->employeeprofiles_id ?? null;

        // Check if 'status' column changed
        if ($employee->isDirty('status')) {
            $oldStatus = $employee->getOriginal('status');
            $newStatus = $employee->status;

            if ($newStatus === 'active') {
                ActivityLog::create([
                    'action_type' => 'Employee Reactivated',
                    'employeeprofiles_id' => $actorId,
                    'description' => "Reactivated employee: {$employee->first_name} {$employee->last_name}"
                ]);
            } elseif ($newStatus === 'inactive') {
                ActivityLog::create([
                    'action_type' => 'Employee Deactivated',
                    'employeeprofiles_id' => $actorId,
                    'description' => "Deactivated employee: {$employee->first_name} {$employee->last_name}"
                ]);
            } else {
                ActivityLog::create([
                    'action_type' => 'Employee Status Changed',
                    'employeeprofiles_id' => $actorId,
                    'description' => "Changed status of {$employee->first_name} {$employee->last_name} from '{$oldStatus}' to '{$newStatus}'."
                ]);
            }
        } else {
            // Log normal updates
            ActivityLog::create([
                'action_type' => 'Employee Updated',
                'employeeprofiles_id' => $actorId,
                'description' => "Updated employee: {$employee->first_name} {$employee->last_name}"
            ]);
        }
    }

    public function deleted(Employeeprofiles $employee)
    {
        ActivityLog::create([
            'action_type' => 'Employee Deleted',
            'employeeprofiles_id' => session('employeeprofiles_id') ?? Auth::user()->employeeprofiles_id ?? null,
            'description' => "Deleted employee: {$employee->first_name} {$employee->last_name}"
        ]);
    }

    public function restored(Employeeprofiles $employee)
    {
        ActivityLog::create([
            'action_type' => 'Employee Restored',
            'employeeprofiles_id' => session('employeeprofiles_id') ?? Auth::user()->employeeprofiles_id ?? null,
            'description' => "Restored employee: {$employee->first_name} {$employee->last_name}"
        ]);
    }

    public function deactivated(Employeeprofiles $employee)
    {
        ActivityLog::create([
            'action_type' => 'Employee Deactivated',
            'employeeprofiles_id' => session('employeeprofiles_id') ?? Auth::user()->employeeprofiles_id ?? null,
            'description' => "Deactivated employee: {$employee->first_name} {$employee->last_name}"
        ]);
    }
}
