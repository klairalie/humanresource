<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cross_project_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('position');
            $table->string('activity'); // e.g., "Visited HR Dashboard"
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cross_project_activity_logs');
    }
};
