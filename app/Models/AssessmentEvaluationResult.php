<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentEvaluationResult extends Model
{
    use HasFactory;

    protected $primaryKey = 'assessment_evaluation_result_id';

    protected $fillable = [
        'assessment_id',
        'assessment_evaluation_question_id',
        'employeeprofiles_id',
        'rated_employeeprofiles_id',
        'rating',
    ];

    public function assessment()
    {
        return $this->belongsTo(Assessment::class, 'assessment_id');
    }

    public function question()
    {
        return $this->belongsTo(AssessmentEvaluationQuestion::class, 'assessment_evaluation_question_id');
    }
}
