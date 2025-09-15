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
        Schema::create('applicant_summaries', function (Blueprint $table) {
            $table->id('applicant_summary_id');
    $table->foreignId('applicant_id')->constrained('applicants', 'applicant_id')->onDelete('cascade');
    $table->string('performance_rating')->nullable();
    $table->string('good_moral_file')->nullable();
    $table->string('coe_file')->nullable();
    $table->string('resume_file')->nullable();
    $table->text('skills')->nullable();
    $table->text('achievements')->nullable();
    $table->text('career_objective')->nullable();
    $table->string('position')->nullable();
    $table->text('matched_skills')->nullable();
    $table->text('matched_career_objective')->nullable();
    $table->timestamps();
   
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applicant_summaries');
    }
};
