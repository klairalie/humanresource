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
        Schema::create('applicants', function (Blueprint $table) {
           $table->id('applicant_id');
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('contact_number', 20);
            $table->string('email', 150)->unique();
            $table->text('address');
            $table->date('date_of_birth');
            $table->string('emergency_contact', 150);
            $table->enum('position', [
                'Helper',
                'Assistant Technician',
                'Technician',
                'Human Resource Manager',
                'Administrative Manager',
                'Finance Manager'
            ]);
            $table->longText('career_objective');
            $table->longText('work_experience');   
            $table->longText('education');         
            $table->longText('skills');            
            $table->longText('achievements');      
            $table->longText('references');       
            $table->string('good_moral_file');    
            $table->string('coe_file');    
            $table->string('resume_file');        
            $table->enum('applicant_status', [
                'Pending',
                'On Screening',
                'Passed Screening',
                'Failed Screening',
                'Reviewed',
                'Scheduled Interview',
                'Hired',
                'Rejected'
            ])->default('Pending');
               

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applicants');
    }
};
