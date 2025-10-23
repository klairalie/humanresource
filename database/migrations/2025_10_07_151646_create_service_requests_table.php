<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Prerequisites: Ensure 'customers' and 'customer_addresses' tables exist (migrations run first).
        // 'customer_addresses.address_id' must be the primary key (it is, per the provided migration).
        // If error persists, check for existing data violations or run: php artisan migrate:fresh

        Schema::create('service_requests', function (Blueprint $table) {
            $table->engine = 'InnoDB'; // Ensure engine matches other tables

            $table->id('service_request_id');
            $table->string('service_request_number')->nullable()->unique();

            // Customer reference
            $table->foreignId('customer_id')
                  ->constrained('customers', 'customer_id')
                  ->cascadeOnDelete()
                  ->cascadeOnUpdate();

            // Optional address reference
            $table->unsignedBigInteger('address_id')->nullable(); // Explicitly define column type to match customer_addresses.address_id
            $table->index('address_id'); // Ensure index for foreign key (Laravel adds this, but explicit for safety)

            // Manually add foreign key for better error handling (equivalent to constrained() with nullOnDelete and cascadeOnUpdate)
            $table->foreign('address_id')
                  ->references('address_id')
                  ->on('customer_addresses')
                  ->onDelete('set null') // Sets to NULL if referenced row deleted
                  ->onUpdate('cascade'); // Cascades updates

            // Order totals
            $table->decimal('order_total', 12, 2)->nullable();
            $table->decimal('overall_discount', 12, 2)->default(0);
            $table->decimal('overall_tax_amount', 12, 2)->default(0);

            // Payment & status
            $table->string('type_of_payment')->nullable();
            $table->enum('order_status', ['Pending', 'Ongoing', 'Completed', 'Cancelled'])->default('Pending');
            $table->enum('payment_status', ['Unpaid', 'Partially Paid', 'Paid', 'Cancelled'])->default('Unpaid');
            $table->date('accomplishment_date')->nullable();

            // Extra info
            $table->text('remarks')->nullable();

            // PDF storage fields
            $table->string('pdf_name')->nullable();            
            $table->string('pdf_mime')->default('application/pdf');
            $table->binary('pdf_file')->nullable();            
            $table->timestamp('pdf_generated_at')->nullable();

            $table->timestamps();

            // Useful indexes
            $table->index(['customer_id', 'order_status']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('service_requests');
    }
};
