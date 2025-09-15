<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('resume_formats', function (Blueprint $table) {
    $table->id('resume_format_id');
    $table->string('file_name');
    $table->timestamps();
});

// Manually alter file_data to LONGBLOB
DB::statement('ALTER TABLE resume_formats ADD file_data LONGBLOB');

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resume_formats');
    }
};
