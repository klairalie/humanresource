<?php

namespace Database\Factories;

use App\Models\Employeeprofiles;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attendance>
 */
class AttendanceFactory extends Factory
{
    public function definition(): array
    {
        // Get an existing employee, or create one if none exist
        $employee = Employeeprofiles::inRandomOrder()->first();

        return [
            'date' => fake()->dateTimeBetween('2025-01-01', '2025-09-30')->format('Y-m-d'),
            'time_in' => fake()->time('H:i:s'),
            'time_out' => fake()->time('H:i:s'),
            'status' => fake()->randomElement(['Absent', 'Present', 'Out']),
            'employeeprofiles_id' => $employee->employeeprofiles_id,
        ];
    }
}
