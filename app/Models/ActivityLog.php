<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $table = 'act_logs';
    protected $primaryKey = 'activity_log_id';

    protected $fillable = [
        'action_type',
        'email',
        'description',
        'action_date',
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

    public function servicerequestitem(){
        return $this->belongsTo(ServiceRequestItem::class, 'item_id');
    }
}
