<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('evaluation_results', function (Blueprint $table) {
            $table->id('evaluation_results_id');

            $table->foreignId('evaluator_id')
                ->constrained('employeeprofiles', 'employeeprofiles_id')
                ->cascadeOnDelete();

            $table->foreignId('evaluatee_id')
                ->constrained('employeeprofiles', 'employeeprofiles_id')
                ->cascadeOnDelete();

            $table->foreignId('assessment_id')
                ->nullable()
                ->constrained('assessments', 'assessment_id')
                ->nullOnDelete();

            $table->foreignId('question_id')
                ->constrained('evaluation_questions', 'evaluation_question_id')
                ->cascadeOnDelete();

            $table->integer('rating')->nullable();
            $table->text('feedback')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluation_result');
    }
};
