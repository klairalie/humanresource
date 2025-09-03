<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('salaries', function (Blueprint $table) {
            $table->id('salaries_id');
            $table->decimal('basic_salary', 10, 2);
            $table->string('position');
        });

         DB::table('salaries')->insert([
            ['position' => 'Technician', 'basic_salary' => 650.00],
            ['position' => 'Assistant Technician', 'basic_salary' => 600.00],
            ['position' => 'Helper', 'basic_salary' => 500.00],
            ['position' => 'Human Resource Manager', 'basic_salary' => 750.00],
            ['position' => 'Administrative Manager', 'basic_salary' => 800.00],
            ['position' => 'Finance Manager', 'basic_salary' => 750.00],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salaries');
    }
};
