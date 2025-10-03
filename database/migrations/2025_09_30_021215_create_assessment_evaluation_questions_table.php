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
        Schema::create('assessment_evaluation_results', function (Blueprint $table) {
             $table->id('assessment_evaluation_result_id');
            $table->foreignId('assessment_id')->constrained('assessments', 'assessment_id')->onDelete('cascade');
            $table->foreignId('assessment_evaluation_question_id')->constrained('assessment_evaluation_questions', 'assessment_evaluation_question_id')->onDelete('cascade');
            $table->foreignId('employeeprofiles_id')->constrained('employeeprofiles', 'employeeprofiles_id')->onDelete('cascade'); 
            $table->foreignId('rated_employeeprofiles_id')->constrained('employeeprofiles', 'employeeprofiles_id')->onDelete('cascade'); // evaluatee
            $table->tinyInteger('rating')->checkBetween(1, 5); // scale 1-5
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_evaluation_results');
    }
};
