<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    use HasFactory;

    protected $primaryKey = 'assessment_id';

    protected $fillable = [
        'position_name',
        'title',
        'description',
    ];

    public function tokens()
    {
        return $this->hasMany(AssessmentToken::class, 'assessment_id', 'assessment_id');
    }

    public function results()
    {
        return $this->hasMany(AssessmentResult::class, 'assessment_id', 'assessment_id');
    }

    public function questions(){

        return $this->hasMany(AssessmentQuestion::class, 'assessment_id', 'assessment_id');
    }
}
