<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visit_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('token', 64)->unique();    // Unique token
            $table->string('admin_email');            // Admin who clicked
            $table->string('acting_as');              // Acting role
            $table->string('target_module');          // e.g., HR Dashboard
            $table->timestamp('expires_at')->nullable(); // Optional expiration
            $table->timestamps();

            $table->index('expires_at');              // Optional for cleanup
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visit_tokens');
    }
};
