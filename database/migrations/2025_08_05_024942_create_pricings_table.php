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
        Schema::create('pricings', function (Blueprint $table) {
            $table->id('pricing_id');
            $table->foreignId('services_id')->constrained('employeeprofiles', 'employeeprofiles_id')->onDelete('cascade');
            $table->decimal('price')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->string('status')->nullable();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pricings');
    }
};
