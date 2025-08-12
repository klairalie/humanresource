<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Administrativeaccount>
 */
class AdministrativeaccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            
            'username' => fake()->userName(),
            'password' => fake()->password(),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'contact_info' => fake()->phoneNumber(),
        ];
    }
}
