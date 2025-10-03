<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicantSummary extends Model
{
    protected $table = 'applicant_summaries';
    protected $primaryKey = 'applicant_summary_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
    'applicant_id',
    'performance_rating',
    'good_moral_file',
    'coe_file',
    'resume_file',
    'skills',
    'achievements',
    'career_objective',
    'position',
    'matched_skills',
    'matched_career_objective',
    'total_score',        // ✅ add this
    'capability_result',
];


    use HasFactory;

    public function applicant()
    {
        return $this->belongsTo(Applicant::class, 'applicant_id', 'applicant_id');
    }

    
}
