<?php

namespace Database\Factories;

use App\Models\Administrativeaccount;
use App\Models\Quotation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Billing>
 */
class BillingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $quotation = Quotation::inRandomOrder()->first();
        $user = Administrativeaccount::inRandomOrder()->first();

        return [
            
            'quotation_id' => $quotation->quotation_id,
            'user_id' => $user->user_id,
            'amount' => fake()->random_int(1, 1000),
            'status' => fake()->randomElement(['Paid', 'Cancelled', 'Partial']),
            'date' => fake()->date(),
        ];
    }
}
