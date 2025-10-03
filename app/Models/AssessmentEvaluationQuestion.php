<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentEvaluationQuestion extends Model
{
    use HasFactory;

    protected $primaryKey = 'assessment_evaluation_question_id';

    protected $fillable = [
        'assessment_id',
        'question',
    ];

    public function assessment()
    {
        return $this->belongsTo(Assessment::class, 'assessment_id');
    }

    public function results()
    {
        return $this->hasMany(AssessmentEvaluationResult::class, 'assessment_evaluation_question_id');
    }
}
