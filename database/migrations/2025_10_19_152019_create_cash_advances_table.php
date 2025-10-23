<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('cash_advances', function (Blueprint $table) {
            $table->bigIncrements('cash_advance_id');
            $table->unsignedBigInteger('employeeprofiles_id');
            $table->decimal('amount', 10, 2);
            $table->text('reason')->nullable();
            $table->dateTime('filed_date');
            $table->dateTime('approved_date')->nullable();
            $table->enum('status', ['pending','approved','rejected','deleted'])->default('pending');
            $table->unsignedBigInteger('created_by')->nullable(); // admin_id
            $table->timestamps();

            $table->foreign('employeeprofiles_id')->references('employeeprofiles_id')->on('employeeprofiles');
            $table->foreign('created_by')->references('admin_id')->on('administrativeaccounts');
        });
    }
    public function down(): void {
        Schema::dropIfExists('cash_advances');
    }
};
