<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    public function definition(): array
    {
        $first = $this->faker->firstName();
        $last = $this->faker->lastName();

        return [
            'full_name' => "{$first} {$last}",
            'business_name' => $this->faker->optional(0.3)->company(),
            'contact_info' => $this->faker->numerify('09#########'),
            'email' => $this->faker->unique()->safeEmail(),
        ];
    }
}
