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
        Schema::create('servicerequests', function (Blueprint $table) {
            $table->id('servicerequest_id');
            $table->foreignId('services_id')->constrained('services', 'services_id')->onDelete('cascade');
            $table->string('category')->nullable();
            $table->string('customer_name')->nullable();
            $table->decimal('amount')->nullable();
            $table->string('location')->nullable();
            $table->foreignId('assigned_technician_id')->constrained('employeeprofiles', 'employeeprofiles_id')->onDelete('cascade');
            $table->string('status')->nullable();
            $table->timestamps();

            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servicerequests');
    }
};
