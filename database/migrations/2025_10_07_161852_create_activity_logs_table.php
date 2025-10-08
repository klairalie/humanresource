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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id('activity_log_id');
            $table->string('action_type'); // e.g., "New Employee Added", "Document uploaded", "System updated"
           $table->foreignId('employeeprofiles_id')
      ->nullable()
      ->constrained('employeeprofiles', 'employeeprofiles_id')
      ->onDelete('cascade');

            $table->text('description')->nullable(); // optional details
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
