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
        Schema::create('assessment_questions', function (Blueprint $table) {
        $table->id('assessment_question_id');
        $table->foreignId('assessment_id')->constrained('assessments', 'assessment_id')->onDelete('cascade');
        $table->enum('position', [
    'Helper',
    'Assistant Technician',
    'Technician',
    'Human Resource Manager',
    'Administrative Manager',
    'Finance Manager'
]);
        $table->enum('level', ['ability', 'knowledge', 'strength']);
        $table->text('question');
        $table->string('choice_a')->nullable();
        $table->string('choice_b')->nullable();
        $table->string('choice_c')->nullable();
        $table->string('choice_d')->nullable();
        $table->string('correct_answer', 10)->nullable(); // for MCQs
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_question');
    }
};
