<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationResult extends Model
{
    use HasFactory;

    protected $table = 'evaluation_results';
    public $timestamps = true;

    protected $fillable = [
        'evaluator_id',
        'evaluatee_id',
        'assessment_id',
        'question_id',
        'rating',
        'feedback',
    ];

    // Relationships
    public function evaluatee()
    {
        return $this->belongsTo(EmployeeProfiles::class, 'evaluatee_id', 'employeeprofiles_id');
    }

    public function evaluator()
    {
        return $this->belongsTo(EmployeeProfiles::class, 'evaluator_id', 'employeeprofiles_id');
    }

    public function assessment()
    {
        return $this->belongsTo(Assessment::class, 'assessment_id');
    }

    public function question()
    {
        return $this->belongsTo(EvaluationQuestion::class, 'question_id', 'evaluation_question_id');
    }
}

