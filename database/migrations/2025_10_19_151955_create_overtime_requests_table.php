<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('overtime_requests', function (Blueprint $table) {
            $table->bigIncrements('overtime_request_id');
            $table->unsignedBigInteger('employeeprofiles_id');
            $table->unsignedBigInteger('service_request_item_id');
            $table->decimal('hours', 5, 2);
            $table->decimal('amount', 10, 2);
            $table->dateTime('filed_date');
            $table->dateTime('approved_date')->nullable();
            $table->enum('status', ['pending','approved','rejected','deleted'])->default('pending');
            $table->unsignedBigInteger('created_by')->nullable(); // admin_id
            $table->dateTime('release_date')->nullable();
            $table->text('reason')->nullable();
            $table->timestamps();

            $table->foreign('employeeprofiles_id')->references('employeeprofiles_id')->on('employeeprofiles');
            $table->foreign('service_request_item_id')->references('item_id')->on('service_request_items');
            $table->foreign('created_by')->references('admin_id')->on('administrativeaccounts');
        });
    }
    public function down(): void {
        Schema::dropIfExists('overtime_requests');
    }
};
