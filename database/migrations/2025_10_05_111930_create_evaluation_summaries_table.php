<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('evaluation_summaries', function (Blueprint $table) {
            $table->id('evaluation_summary_id');

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

            $table->decimal('total_score', 6, 2)->nullable();
            $table->json('category_scores')->nullable();
            $table->text('feedback')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluation_summaries');
    }
};
