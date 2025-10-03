<?php

namespace Database\Seeders;
use App\Models\Employeeprofiles;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed employees first
        $this->call([
            EmployeeprofilesSeeder::class,
        ]);

        // For each employee, seed multiple attendances across Jan–Sep
        Employeeprofiles::all()->each(function ($employee) {
            Attendance::factory()->count(200)->create([
                'employeeprofiles_id' => $employee->employeeprofiles_id,
            ]);
        });
    }
}


