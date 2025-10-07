<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEvaluationTokensTable extends Migration
{
    public function up()
    {
        Schema::create('evaluation_tokens', function (Blueprint $table) {
            $table->id('evaluation_tokens_id');

            // Evaluator (the one receiving the token)
            $table->foreignId('evaluator_id')
                  ->constrained('employeeprofiles', 'employeeprofiles_id')
                  ->onDelete('cascade');

            // Evaluatee (the one being evaluated) - nullable at first
            $table->foreignId('evaluatee_id')
                  ->nullable()
                  ->constrained('employeeprofiles', 'employeeprofiles_id')
                  ->onDelete('cascade');

            // Which assessment
            $table->foreignId('assessment_id')
                  ->constrained('assessments', 'assessment_id')
                  ->onDelete('cascade');

            $table->string('token')->unique();
            $table->timestamp('expires_at');
            $table->boolean('is_used')->default(false);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('evaluation_tokens');
    }
}
