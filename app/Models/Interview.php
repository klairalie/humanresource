<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interview extends Model
{
    protected $primaryKey = 'interviews_id';
    protected $fillable = [
        'applicant_id',
        'interview_date',
        'interview_time',
        'location',
        'hr_manager',
    ];
    /** @use HasFactory<\Database\Factories\InterviewFactory> */
    use HasFactory;

    public function applicant()
    {
        return $this->belongsTo(Applicant::class, 'applicant_id', 'applicant_id');
    }
}
