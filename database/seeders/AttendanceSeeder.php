<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;
use App\Models\Employeeprofiles;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        Employeeprofiles::all()->each(function ($employee) {
            Attendance::factory()
                ->count(16)
                ->create([
                    'employeeprofiles_id' => $employee->employeeprofiles_id,
                ]);
        });
    }
}
