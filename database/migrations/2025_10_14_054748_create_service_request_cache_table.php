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
        Schema::create('service_request_cache', function (Blueprint $table) {
            $table->id();
            $table->string('cache_key')->unique();
            $table->unsignedBigInteger('service_request_id')->nullable();
            $table->unsignedBigInteger('technician_id')->nullable();
            $table->string('cache_type')->default('general'); // 'suggestions', 'assignments', 'holds', etc.
            $table->json('cache_data');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['service_request_id', 'cache_type']);
            $table->index(['technician_id', 'cache_type']);
            $table->index(['cache_type', 'expires_at']);
            
            // Foreign key constraints (optional, depends on your schema)
            // $table->foreign('service_request_id')->references('service_request_id')->on('service_requests')->onDelete('cascade');
            // $table->foreign('technician_id')->references('employeeprofiles_id')->on('employeeprofiles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_request_cache');
    }
};
