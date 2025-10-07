<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationToken extends Model
{
    use HasFactory;

    protected $table = 'evaluation_tokens';
    protected $primaryKey = 'evaluation_tokens_id'; // match migration
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'token',
        'assessment_id',
        'evaluator_id',
        'evaluatee_id',
        'is_used',
        'expires_at',
    ];

    protected $casts = [
        'is_used' => 'boolean',
        'expires_at' => 'datetime', // Carbon instance
    ];

    // Relationships
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
