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
            'date' => fake()->dateTimeBetween('2025-01-01', '2025-11-08')->format('Y-m-d'),
            'time_in' => fake()->dateTimeBetween('06:00:00', '08:00:00')->format('H:i:s'),
'time_out' => fake()->dateTimeBetween('17:00:00', '19:00:00')->format('H:i:s'),

            'status' => fake()->randomElement(['Absent', 'Present']),
            'employeeprofiles_id' => $employee->employeeprofiles_id,
        ];
    }
}
