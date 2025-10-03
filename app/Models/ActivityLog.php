<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $table = 'activity_logs';
    protected $fillable = ['action_type', 'description', 'applicant_id'];  

    use HasFactory;

    public function applicant()
    {
        return $this->belongsTo(Applicant::class, 'applicant_id');
    }
}
