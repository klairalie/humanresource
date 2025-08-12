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
    $fakeBase64Fingerprint = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR4nGNgYAAAAAMAAWgmWQ0AAAAASUVORK5CYII=';

    return [
        'first_name' => fake()->firstName(),
        'last_name' => fake()->lastName(),
        'address' => fake()->streetAddress(),
        'position' => fake()->sentence(2),
        'contact_info' => fake()->phoneNumber(11),
        'hire_date' => fake()->date(),
        'status' => fake()->randomElement(['pending', 'paid', 'cancelled']),
        'emergency_contact' => fake()->phoneNumber(11),
        'fingerprint_data' => $fakeBase64Fingerprint, // static test image
    ];
}
    
}
