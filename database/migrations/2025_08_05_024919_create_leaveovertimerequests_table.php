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
        Schema::create('leaveovertimerequests', function (Blueprint $table) {
            $table->id('request_id');
            $table->foreignId('employeeprofiles_id')->constrained('employeeprofiles', 'employeeprofiles_id')->onDelete('cascade');
            $table->integer('leave_days')->nullable()->default(0);
            $table->integer('overtime_hours')->nullable()->default(0);
            $table->string('leave_type')->nullable()->default('No request');
            $table->string('status')->default('No request');
            $table->dateTime('request_date')->nullable();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leaveovertimerequests');
    }
};
