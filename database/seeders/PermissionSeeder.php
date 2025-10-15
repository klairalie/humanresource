<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['permission_key' => 'view_HR_dashboard', 'description' => 'Access the HR Dashboard'],
            ['permission_key' => 'employeeprofiles_view', 'description' => 'View Employee Profiles'],
            ['permission_key' => 'evaluateservices_view', 'description' => 'Evaluate Services'],
            ['permission_key' => 'booking_view', 'description' => 'View and Manage Bookings'],
            ['permission_key' => 'view_finance_dashboard', 'description' => 'Access Finance Dashboard'],
        ];

        foreach ($permissions as $perm) {
            Permission::updateOrCreate(
                ['permission_key' => $perm['permission_key']],
                ['description' => $perm['description']]
            );
        }
    }
}
