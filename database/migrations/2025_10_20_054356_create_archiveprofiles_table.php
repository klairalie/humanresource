<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('archiveprofiles', function (Blueprint $table) {
            $table->id('archiveprofiles_id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('address');
            $table->string('email');
            $table->string('position');
            $table->date('date_of_birth');
            $table->string('contact_number');
            $table->date('hire_date');
            $table->string('status')->default('deactivated');
            $table->string('emergency_contact');
            $table->string('card_Idnumber');
            $table->string('reason');
            $table->string('archived_by');
            $table->timestamp('archived_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('archiveprofiles');
    }
};
