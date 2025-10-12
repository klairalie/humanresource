<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('service_time_reference', function (Blueprint $table) {
            $table->id();
            $table->string('service_type');
            $table->unsignedBigInteger('aircon_type_id')->nullable();
            $table->string('unit_type')->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->unsignedInteger('avg_minutes')->default(120);
            $table->unsignedInteger('samples_count')->default(0);
            $table->timestamps();

            $table->index(['service_type', 'aircon_type_id', 'unit_type', 'quantity'], 'str_comp_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_time_reference');
    }
};
