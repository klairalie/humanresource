<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentQuestion extends Model
{
    use HasFactory;

    protected $primaryKey = 'assessment_question_id';

    protected $fillable = [
        'assessment_id',
        'position',
        'level',
        'question',
        'choice_a',
        'choice_b',
        'choice_c',
        'choice_d',
        'correct_answer',
    ];


    public function assessment(){
        $this->belongsTo(Assessment::class, 'assessment_id', 'assessment_id');
    }
}
