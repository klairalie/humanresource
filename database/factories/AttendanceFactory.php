<?php

namespace Database\Factories;

use App\Models\Employeeprofiles;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceFactory extends Factory
{
    public function definition(): array
    {
        // Random attendance status
        $status = fake()->randomElement(['Present', 'Late - On Duty', 'Absent']);

        // If Absent → no time_in / time_out
        if ($status === 'Absent') {
            return [
                'date' => fake()->dateTimeBetween('2025-11-01', '2025-11-30')->format('Y-m-d'),
                'time_in' => null,
                'time_out' => null,
                'status' => $status,
            ];
        }

        // For Present or Late
        $date = fake()->dateTimeBetween('2025-11-01', '2025-11-17');
        $timeIn = fake()->dateTimeBetween($date->format('Y-m-d').' 06:00:00', $date->format('Y-m-d').' 08:00:00');
        $timeOut = fake()->dateTimeBetween($date->format('Y-m-d').' 17:00:00', $date->format('Y-m-d').' 19:00:00');

        return [
            'date' => $date->format('Y-m-d'),
            'time_in' => $timeIn->format('H:i:s'),
            'time_out' => $timeOut->format('H:i:s'),
            'status' => $status,
        ];
    }
}
