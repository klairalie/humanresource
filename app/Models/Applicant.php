<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Applicant extends Model 
{
    use Notifiable;
    
    protected $primaryKey = 'applicant_id';
     protected $guarded = [];
    protected $fillable = [
        'first_name',
        'last_name',
        'contact_number',
        'email',
        'address',
        'date_of_birth',
        'emergency_contact',
        'position',
        'career_objective',
        'work_experience',
        'education',
        'skills',
        'achievements',
        'references',
        'good_moral_file',
        'coe_file',
        'resume_file',
        'applicant_status'
    ];

    /** @use HasFactory<\Database\Factories\ApplicantFactory> */
    use HasFactory, Notifiable;

    public function summary()
{
    return $this->hasOne(ApplicantSummary::class, 'applicant_id');
}

public function routeNotificationForMail($notification)
    {
        return $this->email;
    }

    public function tokens()
{
    return $this->hasMany(AssessmentToken::class, 'applicant_id', 'applicant_id');
}

public function result()
{
    return $this->hasOne(AssessmentResult::class, 'applicant_id');
}

public function interviews()
{
    return $this->hasOne(Interview::class, 'applicant_id', 'applicant_id');
}

public function activitylogs(){

    return $this->hasMany(ActivityLog::class, 'applicant_id');
}
}
