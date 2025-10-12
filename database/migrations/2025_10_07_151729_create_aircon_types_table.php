<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aircon_types', function (Blueprint $table) {
            $table->id('aircon_type_id');
            $table->string('name');
            $table->string('brand')->nullable();
            $table->string('capacity')->nullable();
            $table->string('model')->nullable();
            $table->string('category')->nullable();
            $table->decimal('base_price', 10, 2)->nullable();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->enum('status', ['active','inactive'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aircon_types');
    }
};