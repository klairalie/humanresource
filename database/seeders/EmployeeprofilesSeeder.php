<?php

namespace Database\Seeders;

use App\Models\Employeeprofiles;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class EmployeeprofilesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ✅ 1. Insert your manually defined employees
        Employeeprofiles::insert([
            [
                'first_name' => 'Kyla Marie',
                'last_name' => 'Llamedo',
                'address' => 'Cebu City, Philippines',
                'email' => 'llamedo.kylamarie@gmail.com',
                'position' => 'Human Resource Manager',
                'date_of_birth' => '1999-08-14',
                'contact_number' => '09171234567',
                'hire_date' => Carbon::parse('2022-06-01'),
                'status' => 'Active',
                'emergency_contact' => 'Maria Llamedo - 09180001111',
                'card_Idnumber' => 'HRM-001',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Mitchrey Ann',
                'last_name' => 'Dablo',
                'address' => 'Lapu-Lapu City, Philippines',
                'email' => 'kaiavlinder@gmail.com',
                'position' => 'Administrative Manager',
                'date_of_birth' => '1998-03-21',
                'contact_number' => '09181234567',
                'hire_date' => Carbon::parse('2021-07-10'),
                'status' => 'Active',
                'emergency_contact' => 'John Dablo - 09180002222',
                'card_Idnumber' => 'ADM-002',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Nicko',
                'last_name' => 'Simbit',
                'address' => 'Talisay City, Philippines',
                'email' => 'kylamarie.llamedo@gmail.com',
                'position' => 'Finance Manager',
                'date_of_birth' => '1997-11-05',
                'contact_number' => '09192223344',
                'hire_date' => Carbon::parse('2020-05-15'),
                'status' => 'Active',
                'emergency_contact' => 'Angela Simbit - 09180003333',
                'card_Idnumber' => 'FIN-003',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // ✅ 2. Generate random employees using your factory
        Employeeprofiles::factory()->count(10)->create();
    }
}
