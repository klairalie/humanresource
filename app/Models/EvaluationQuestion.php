<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Models\Assessment;
use App\Models\EvaluationResult;
class EvaluationQuestion extends Model
{
    use HasFactory;

    protected $primaryKey = 'evaluation_question_id'; // Custom PK
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'assessment_id',
        'position',
         'category',
        'question',
        'rating',
    ];

    public function assessment()
    {
        return $this->belongsTo(Assessment::class, 'assessment_id', 'assessment_id');
    }


    
   
}
