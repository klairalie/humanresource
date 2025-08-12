<?php

namespace Database\Factories;

use App\Models\Employeeprofiles;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payroll>
 */
class PayrollFactory extends Factory
{
    public function definition(): array
    {
        
        $employee = Employeeprofiles::inRandomOrder()->first()
            ?? Employeeprofiles::factory()->create();

        
        $overtimeHours = fake()->numberBetween(0, 10);
        $overtimeRate = 200;
        $overtimePay = $overtimeHours * $overtimeRate;

        // Salary & deductions
        $basicSalary = fake()->numberBetween(10000, 20000);
        $deductions = fake()->numberBetween(0, 3000);

        // Net pay calculation
        $netPay = $basicSalary + $overtimePay - $deductions;

        return [
            'employeeprofiles_id' => $employee->employeeprofiles_id,
            'total_days_of_work' => 0,
            'pay_period' => fake()->randomElement(['1st Half', '2nd Half']),
            'pay_period_start' => fake()->date(),
            'pay_period_end' => fake() ->date(),
            'basic_salary' => $basicSalary,
            'overtime_pay' => $overtimePay,
            'deductions' => $deductions,
            'net_pay' => $netPay,
            'status' => fake()->randomElement(['Pending', 'Processed', 'Paid']),
        ];
    }
}
