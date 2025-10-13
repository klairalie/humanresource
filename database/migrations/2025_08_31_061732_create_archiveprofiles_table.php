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
        Schema::create('archiveprofiles', function (Blueprint $table) {
             $table->id('archiveprofile_id');
            $table->foreignId('employeeprofiles_id')->constrained('employeeprofiles', 'employeeprofiles_id')->onDelete('cascade');
            $table->string('status')->default('deactivated');
            $table->string('reason');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('position');
            $table->string('contact_number');
            $table->date('hire_date');
            $table->string('emergency_contact');
            $table->longText('card_Idnumber')->nullable();
            $table->date('archived_at')->useCurrent();
            $table->string('archived_by');
            $table->date('reactivated_at')->useCurrent();
            $table->string('reactivated_by')->nullable();
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('archiveprofiles');
    }
};
