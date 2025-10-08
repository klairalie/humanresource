<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CustomerAddress>
 */
class CustomerAddressFactory extends Factory
{
    public function definition(): array
    {
        return [
            'customer_id'    => $this->faker->numberBetween(1, 20),
            'label'          => $this->faker->randomElement(['Home', 'Office', 'Condo', 'Warehouse']),
            'street_address' => $this->faker->streetAddress(),
            'barangay'       => $this->faker->streetName(),
            'city'           => $this->faker->randomElement([
                'Quezon City', 'Makati', 'Pasig', 'Taguig', 'Cebu City', 'Davao City',
            ]),
            'province'       => $this->faker->randomElement([
                'Metro Manila', 'Cebu', 'Davao del Sur', 'Laguna', 'Batangas',
            ]),
            'zip_code'       => $this->faker->postcode(),
            'is_default'     => $this->faker->boolean(30), // 30% chance to be default
        ];
    }
}
