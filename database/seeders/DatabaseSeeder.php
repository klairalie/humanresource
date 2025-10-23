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


        // ✅ Final message
        $this->command->info('✅ All seeders completed successfully.');
    }
}
