<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\ServiceRequest;
use App\Models\Services;
use App\Models\AirconType;
use App\Models\Employeeprofiles;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ServiceRequestItem>
 */
class ServiceRequestItemFactory extends Factory
{
    public function definition(): array
    {
        // Fetch existing IDs safely
        $serviceRequestId = ServiceRequest::inRandomOrder()->value('service_request_id');
        $serviceId        = Services::inRandomOrder()->value('services_id');
        $airconTypeId     = AirconType::inRandomOrder()->value('aircon_type_id');
        $technicianId     = Employeeprofiles::inRandomOrder()->value('employeeprofiles_id');

        return [
            'service_request_id'     => $serviceRequestId,
            'services_id'            => $serviceId,
            'aircon_type_id'         => $airconTypeId,
            'service_type'           => $this->faker->randomElement(['Cleaning', 'Repair', 'Installation']),
            'quantity'               => $this->faker->numberBetween(1, 3),
            'unit_price'             => $this->faker->randomFloat(2, 800, 3500),
            'discount'               => $this->faker->randomFloat(2, 0, 300),
            'tax'                    => $this->faker->randomFloat(2, 50, 500),
            'line_total'             => $this->faker->randomFloat(2, 1000, 7000),
            'start_date'             => $this->faker->dateTimeBetween('-1 month', 'now'),
            'end_date'               => $this->faker->dateTimeBetween('now', '+1 month'),
            'start_time'             => $this->faker->time(),
            'end_time'               => $this->faker->time(),
            'assigned_technician_id' => $technicianId,
            'status'                 => $this->faker->randomElement(['Pending', 'In Progress', 'Completed', 'Rescheduled']),
            'billed'                 => $this->faker->boolean(),
            'service_notes'          => $this->faker->sentence(),
        ];
    }
}
