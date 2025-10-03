<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentResult extends Model
{
    use HasFactory;
protected $casts = [
    'submitted_at' => 'datetime',
];

    protected $primaryKey = 'assessment_result_id';

    protected $fillable = [
        'applicant_id',
        'assessment_id',
        'ability_score',
        'knowledge_score',
        'strength_score',
        'total_score',
        'performance_rating',
        'submitted_at',
    ];

    public function applicant()
    {
        return $this->belongsTo(Applicant::class, 'applicant_id', 'applicant_id');
    }

    public function assessment()
    {
        return $this->belongsTo(Assessment::class, 'assessment_id', 'assessment_id');
    }
}
