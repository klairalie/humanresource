<?php

namespace Database\Seeders;

use App\Models\Employeeprofiles;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmployeeprofilesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Employeeprofiles::factory()->count(10)->create();
    }
}
