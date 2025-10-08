<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AirconType>
 */
class AirconTypeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement([
                'Window Type',
                'Split Type',
                'Cassette Type',
                'Tower Type',
                'Ceiling Mounted',
            ]),
            'brand' => $this->faker->randomElement([
                'Daikin', 'Carrier', 'Panasonic', 'LG', 'Samsung', 'Midea',
            ]),
            'capacity' => $this->faker->randomElement([
                '1.0 HP', '1.5 HP', '2.0 HP', '2.5 HP', '3.0 HP',
            ]),
            'model' => strtoupper($this->faker->bothify('AC-###??')),
            'category' => $this->faker->randomElement([
                'Residential', 'Commercial', 'Industrial',
            ]),
            'base_price' => $this->faker->randomFloat(2, 5000, 30000),
            'description' => $this->faker->sentence(8),
            'status' => $this->faker->randomElement(['Active', 'Inactive']),
        ];
    }
}
