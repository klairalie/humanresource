<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
      Schema::create('evaluation_questions', function (Blueprint $table) {
    $table->id('evaluation_question_id');
    $table->foreignId('assessment_id')
          ->constrained('assessments', 'assessment_id')
          ->onDelete('cascade');
    $table->string('position');
    $table->enum('category', ['Knowledge', 'Ability', 'Strength']); // 👈 add this
    $table->text('question');
    $table->integer('rating')->nullable(); // 1–5 rating, optional
    $table->timestamps();
});
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluation_questions');
    }
};
