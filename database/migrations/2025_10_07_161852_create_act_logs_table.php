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
        Schema::create('act_logs', function (Blueprint $table) {
            $table->id('activity_log_id');
            $table->string('action_type'); // e.g., "New Employee Added", "Document Uploaded", etc.
            $table->string('email')->default('HR Manager'); // user responsible for the action
            $table->text('description')->nullable(); // optional details
            $table->dateTime('action_date')->default(now()); // ✅ exact date and time when action happened
            $table->timestamps(); // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('act_logs');
    }
};
