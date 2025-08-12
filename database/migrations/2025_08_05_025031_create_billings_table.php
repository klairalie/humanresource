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
        Schema::create('billings', function (Blueprint $table) {
            $table->id('billings_id');
            $table->foreignId('quotation_id')->constrained('quotations', 'quotation_id')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('administrativeaccounts', 'admin_id')->onDelete('cascade');
            $table->decimal('amount')->nullable();
            $table->string('status')->nullable();
            $table->date('date')->nullable();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billings');
    }
};
