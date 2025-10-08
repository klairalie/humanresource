<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employeeprofiles;
use App\Models\Attendance;
use App\Models\EvaluationSummary;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\AirconType;
use App\Models\Services;
use App\Models\AirconServicePrice;
use App\Models\ServiceRequest;
use App\Models\ServiceRequestItem;
use App\Models\TechnicianAssignment;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 🧑‍💼 Seed base employees first
        $this->call([
            EmployeeprofilesSeeder::class,
        ]);

        // 🗓️ For each employee, seed multiple attendances (Jan–Sep)
        Employeeprofiles::all()->each(function ($employee) {
            Attendance::factory()->count(200)->create([
                'employeeprofiles_id' => $employee->employeeprofiles_id,
            ]);
        });

        // 📊 Evaluation summaries
        if (Employeeprofiles::count() > 1) {
            EvaluationSummary::factory()->count(60)->create();
        }

        // ===============================
        // 🧱 Additional app-related data
        // ===============================

        // 👥 Customers
        $this->command->info('📦 Seeding customers...');
        Customer::factory()->count(20)->create();

        // 🏠 Addresses (need customers first)
        $this->command->info('🏠 Seeding customer addresses...');
        CustomerAddress::factory()->count(25)->create();

        // ❄️ Aircon types
        $this->command->info('❄️ Seeding aircon types...');
        AirconType::factory()->count(5)->create();

        // 🧾 Services
        $this->command->info('🧾 Seeding services...');
        Services::factory()->count(10)->create();

        // 🧰 Aircon service prices (depends on services + aircon types)
        $this->command->info('🧰 Seeding aircon service prices...');
        AirconServicePrice::factory()->count(10)->create();

        // 📅 Service requests
        $this->command->info('📅 Seeding service requests...');
        ServiceRequest::factory()->count(30)->create();

        // 🧩 Service request items (need service requests, aircon types, and services)
        $this->command->info('🧩 Seeding service request items...');
        ServiceRequestItem::factory()->count(50)->create();

        // 👷 Technician assignments (depends on employeeprofiles + service request items)
        $this->command->info('👷 Seeding technician assignments...');
        TechnicianAssignment::factory()->count(20)->create();

        // ✅ Final message
        $this->command->info('✅ All seeders completed successfully.');
    }
}
