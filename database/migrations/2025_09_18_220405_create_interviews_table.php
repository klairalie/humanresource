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
        Schema::create('interviews', function (Blueprint $table) {
        $table->id('interviews_id');
        $table->timestamps();
        $table->foreignId('applicant_id')->constrained('applicants', 'applicant_id')->onDelete('cascade');
        $table->date('interview_date');
        $table->time('interview_time');
        $table->string('location');
        $table->string('hr_manager');
        $table->enum('status', ['Scheduled', 'Unattended', 'Done'])->default('Scheduled');
       
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interviews');
    }
};
