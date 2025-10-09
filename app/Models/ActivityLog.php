<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $table = 'activity_logs';

    protected $fillable = [
        'action_type',
        'employeeprofiles_id',
        'applicant_id',
        'description',
    ];

    // For employee logs
    public function employeeprofiles()
    {
        return $this->belongsTo(EmployeeProfiles::class, 'employeeprofiles_id');
    }

    // For applicant logs
    public function applicant()
    {
        return $this->belongsTo(Applicant::class, 'applicant_id');
    }
}
