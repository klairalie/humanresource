<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->bigIncrements('leave_request_id');
            $table->unsignedBigInteger('employeeprofiles_id');
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->unsignedInteger('duration_days');
            $table->dateTime('filed_date');
            $table->dateTime('approved_date')->nullable();
            $table->enum('status', ['pending','approved','rejected','deleted'])->default('pending');
            $table->text('reason')->nullable();
            $table->unsignedBigInteger('created_by')->nullable(); // admin_id
            $table->timestamps();

            $table->foreign('employeeprofiles_id')->references('employeeprofiles_id')->on('employeeprofiles');
            $table->foreign('created_by')->references('admin_id')->on('administrativeaccounts');
        });
    }
    public function down(): void {
        Schema::dropIfExists('leave_requests');
    }
};
