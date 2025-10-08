<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ServiceRequest>
 */
class ServiceRequestFactory extends Factory
{
    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-1 month', '+1 month');
        $endDate = (clone $startDate)->modify('+1 day');

        return [
            'customer_id' => $this->faker->numberBetween(1, 20),
            // if you want address_id in future, add it here
            // 'address_id' => $this->faker->numberBetween(1, 20),

            'service_date' => $startDate->format('Y-m-d'),
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'start_time' => $this->faker->time('H:i'),
            'end_time' => $this->faker->time('H:i'),

            'type_of_payment' => $this->faker->randomElement(['Cash', 'Credit Card', 'GCash', 'Bank Transfer']),

            'order_status' => $this->faker->randomElement(['Pending', 'Ongoing', 'Completed', 'Cancelled']),
            'payment_status' => $this->faker->randomElement(['Unpaid', 'Partially Paid', 'Paid', 'Cancelled']),

            'accomplishment_date' => $this->faker->optional()->date(),
            'remarks' => $this->faker->optional()->sentence(8),

            // 'billing_id' => null,
            // 'quotation_id' => null,

            'service_request_number' => strtoupper('SR-' . Str::random(8)),

            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
