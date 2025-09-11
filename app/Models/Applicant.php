<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Applicant extends Model
{
    protected $primaryKey = 'applicant_id';
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
        'applicant_status'
    ];

    /** @use HasFactory<\Database\Factories\ApplicantFactory> */
    use HasFactory;

    public function summary()
{
    return $this->hasOne(ApplicantSummary::class, 'applicant_id', 'applicant_id');
}

}
