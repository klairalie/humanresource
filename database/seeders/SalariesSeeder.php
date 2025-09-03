<?php

namespace Database\Seeders;

use App\Models\Salaries;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SalariesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Salaries::factory()->count(6)->create([

            'salary_amount' => 570, // Example fixed salary amount
            'position' => 'Technician',
             'salary_amount' => 450,
             'psosition' => 'Assistant Technician',
            'salary_amount' => 400,
            'position' => 'Apprentice Technician',
            'salary_amount' => 700,
            'position' => 'Human Resource Manager',
            'salary_amount' => 750,
            'position' => 'Administrative Manager',
            'salary_amount' => 700,
            'position' => 'Finance Manager',
        ])->each(function ($salary) {
            $salary->save();
        });
         
    }
}
