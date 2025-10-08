<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AirconServicePrice>
 */
class AirconServicePriceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'services_id' => $this->faker->numberBetween(1, 10),
            'aircon_type_id' => $this->faker->numberBetween(1, 5),
            'price' => $this->faker->randomFloat(2, 500, 5000),
            'status' => $this->faker->randomElement(['Active', 'Inactive']),
        ];
    }
}
