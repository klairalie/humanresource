<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employeeprofiles', function (Blueprint $table) {
            $table->id('employeeprofiles_id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('address');
            $table->string('email');
            $table->string('position');
            $table->date('date_of_birth');
            $table->string('contact_number');
            $table->date('hire_date')->nullable();
            $table->string('status');
            $table->string('emergency_contact')->nullable();
            $table->string('card_Idnumber')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employeeprofiles');
    }
};
