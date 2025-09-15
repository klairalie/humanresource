<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentToken extends Model
{
    use HasFactory;

    protected $primaryKey = 'assessment_token_id';

    protected $fillable = [
        'applicant_id',
        'assessment_id',
        'token',
        'expires_at',
        'used_at',
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

