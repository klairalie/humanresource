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
        Schema::create('quotations', function (Blueprint $table) {
            $table->id('quotation_id');
            $table->foreignId('services_id')->constrained('services', 'services_id')->onDelete('cascade');
            $table->string('contact_number');
            $table->string('company_email');
            $table->string('client_name');
            $table->string('attention')->nullable();
            $table->string('subject');
            $table->date('quotation_date')->nullable();
            $table->string('equipment_terms')->nullable();
            $table->string('installation_terms')->nullable();
            $table->string('warranty')->nullable();
            $table->string('payable_to')->nullable();
            $table->string('prepared_by')->nullable();
            $table->text('exception')->nullable();
            $table->string('proprietor_name')->nullable();
            $table->timestamps();
        });

        Schema::create('quotation_items', function (Blueprint $table) {
            $table->id('item_id');
            $table->foreignId('quotation_id')->constrained('quotations', 'quotation_id')->onDelete('cascade');
            $table->text('description');
            $table->integer('qty')->default(1);
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->decimal('overall_total', 12, 2)->default(0);
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotation_items');
        Schema::dropIfExists('quotations');
    }
};
