<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\ServiceRequestItem;
use App\Models\Employeeprofiles;

class TechnicianAssignmentFactory extends Factory
{
    public function definition(): array
    {
        // Get valid existing IDs from DB
        $itemId = ServiceRequestItem::inRandomOrder()->value('item_id');
        $technicianId = Employeeprofiles::inRandomOrder()->value('employeeprofiles_id');

        // Just in case DB is empty, prevent nulls
        if (!$itemId) $itemId = 1;
        if (!$technicianId) $technicianId = 1;

        return [
            'item_id' => $itemId,
            'technician_id' => $technicianId,
            'role' => $this->faker->randomElement(['Lead', 'Assistant']),
            'status' => $this->faker->randomElement(['Assigned', 'Completed']),
        ];
    }
}
