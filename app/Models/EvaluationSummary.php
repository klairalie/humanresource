<?php

// app/Models/EvaluationSummary.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationSummary extends Model
{
    use HasFactory;

    protected $table = 'evaluation_summaries';
    protected $primaryKey = 'evaluation_summary_id';

    protected $fillable = [
        'evaluator_id',
        'evaluatee_id',
        'assessment_id',
        'total_score',
        'category_scores',
        'feedback',
    ];

    protected $casts = [
        'category_scores' => 'array',
    ];

    public function evaluator()
{
    return $this->belongsTo(Employeeprofiles::class, 'evaluator_id', 'employeeprofiles_id');
}

public function evaluatee()
{
    return $this->belongsTo(Employeeprofiles::class, 'evaluatee_id', 'employeeprofiles_id');
}

public function assessment()
{
    return $this->belongsTo(Assessment::class, 'assessment_id', 'assessment_id');
}

}
