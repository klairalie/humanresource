<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('assignment_outcomes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('technician_id');
            $table->string('role')->nullable(); // Lead | Assistant
            $table->unsignedInteger('scheduled_minutes')->nullable(); // estimated or planned
            $table->unsignedInteger('actual_minutes')->nullable();
            $table->boolean('on_time')->default(false);
            $table->unsignedInteger('overrun_minutes')->default(0);
            $table->timestamps();

            $table->index(['technician_id']);
            $table->index(['item_id']);
            $table->foreign('item_id')->references('item_id')->on('service_request_items')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignment_outcomes');
    }
};
