<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [

                ['permission_key' => 'hrDashboard', 'description' => 'Access the HR Dashboard'],
            ['permission_key' => 'recentActivities', 'description' => 'View HR Recent Activities'],
            ['permission_key' => 'employeeProfile', 'description' => 'View Employee Profiles'],
            ['permission_key' => 'attendanceRecord', 'description' => 'View Attendance Records'],
            ['permission_key' => 'evaluationResults', 'description' => 'View Evaluation Results of Each Employee'],
            ['permission_key' => 'applicationTestResult', 'description' => 'View Applicants Test Results'],
            ['permission_key' => 'archivedProfiles', 'description' => 'View Archived Profiles'],
            ['permission_key' => 'serviceReports', 'description' => 'View Services Summary Reports'],
            //Finance Module Permissions

            ['permission_key' => 'billingPage', 'description' => 'Visit Billing Page'],
            ['permission_key' => 'purchaseOrders', 'description' => 'View Purchase Orders'],
            ['permission_key' => 'inventoryPage', 'description' => 'Visit Inventory Page'],
          
        ];

        foreach ($permissions as $perm) {
            Permission::updateOrCreate(
                ['permission_key' => $perm['permission_key']],
                ['description' => $perm['description']]
            );
        }
    }
}
