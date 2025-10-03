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
        Schema::create('assessment_results', function (Blueprint $table) {
        $table->id('assessment_result_id');
        $table->foreignId('applicant_id')->constrained('applicants', 'applicant_id')->onDelete('cascade');
        $table->foreignId('assessment_id')->constrained('assessments', 'assessment_id')->onDelete('cascade');
        $table->integer('ability_score')->default(0);
        $table->integer('knowledge_score')->default(0);
        $table->integer('strength_score')->default(0);
        $table->integer('total_score')->default(0);
        $table->enum('performance_rating', ['High', 'Average', 'Low']);
        $table->timestamp('submitted_at')->useCurrent();
        $table->string('assessment_result_status')->nullable();
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_result');
    }
};
