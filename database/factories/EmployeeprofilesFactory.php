<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employeeprofiles>
 */
class EmployeeprofilesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
  public function definition(): array
{
    

    return [
        'first_name' => fake()->firstName(),
        'last_name' => fake()->lastName(),
        'address' => fake()->streetAddress(),
        'position' => fake()->randomElement(['Technician', 'Assistant Technician', 'Helper', 'Human Resource Manager', 'Administrative Manager', 'Finance Manager']),
        'contact_number' => fake()->phoneNumber(11),
        'email' => fake()->unique()->safeEmail(),
        'date_of_birth' => fake()->date('Y-m-d', '-20 years'),
        'hire_date' => fake()->date(),
        'status' => fake()->randomElement(['active', 'reactivated', 'deactivated']),
        'emergency_contact' => fake()->phoneNumber(11),
        'card_Idnumber' => fake()->uuid(),
    ];
}
    
}
