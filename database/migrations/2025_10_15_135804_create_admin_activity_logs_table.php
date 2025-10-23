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
        // Use centralized database connection if needed
        Schema::connection('capstone_central')->create('admin_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->string('actor_email');      // Admin who performed the action
         $table->string('target_email')->nullable(); // instead of ->string('target_email')     // Admin whose account was affected
            $table->string('module');           // e.g., HR, Finance, EmployeeProfiles
            $table->string('action');           // e.g., "updated password", "changed role"
            $table->json('changes')->nullable(); // Record before/after values if needed
            $table->string('ip_address')->nullable(); // Optional: IP of the admin performing the action
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('capstone_central')->dropIfExists('admin_activity_logs');
    }
};
