<?php

namespace Database\Factories;

use App\Models\Employeeprofiles;
use App\Models\Invoices;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Expenses>
 */
class ExpensesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $invoice = Invoices::inRandomOrder()->first();
        $employee = Employeeprofiles::inRandomOrder()->first();
        return [
             'invoices_id' => $invoice->invoices_id,
        'employeeprofiles_id' => $employee->employeeprofiles_id,
        'amount' => fake()->random_int(1, 1000),
        'description' => fake()->sentence(2),
        'date' => fake()->date(),
        ];
    }
}
