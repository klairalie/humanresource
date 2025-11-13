<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasTable('supplier_items')) {
            Schema::create('supplier_items', function (Blueprint $table) {
                $table->bigIncrements('supplier_item_id');
                $table->unsignedBigInteger('supplier_id');
                $table->string('name');
                $table->string('item_type')->nullable();
                $table->text('description')->nullable();
                $table->string('unit_of_measure', 50)->nullable();
                $table->decimal('unit_price', 12, 2)->default(0);
                $table->enum('status', ['active','inactive'])->default('active');
                $table->timestamps();

                $table->foreign('supplier_id')
                    ->references('supplier_id')
                    ->on('suppliers')
                    ->onDelete('cascade');
            });
        }
    }

    public function down(): void {
        Schema::dropIfExists('supplier_items');
    }
};
