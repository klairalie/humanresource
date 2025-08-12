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
            'date' => fake()->date(),
            'time_in' => fake()->time('H:i:s'),
            'time_out' => fake()->time('H:i:s'),
            'flag' => fake()->numberBetween(1, 10),
            'status' => fake()->randomElement(['Absent', 'Present', 'On Leave']),
            'employeeprofiles_id' => $employee->employeeprofiles_id,
        ];
    }
}
