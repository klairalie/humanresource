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
        Schema::create('assessment_tokens', function (Blueprint $table) {
           $table->id('assessment_token_id');
        $table->foreignId('applicant_id')->constrained('applicants', 'applicant_id')->onDelete('cascade');
        $table->foreignId('assessment_id')->constrained('assessments', 'assessment_id')->onDelete('cascade');
        $table->string('token')->unique();
        $table->timestamp('expires_at');
        $table->timestamp('used_at')->nullable();
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_token');
    }
};
