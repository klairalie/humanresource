<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leaveovertimerequest extends Model
{
    protected $fillable = [
        'employeeprofiles_id',
        'leave_days',
        'overtime_hours',
        'status',
        'request_date',
    ];
    /** @use HasFactory<\Database\Factories\LeaveovertimerequestFactory> */
    use HasFactory;
protected $table = 'leaveovertimerequests';

    public function employeeprofiles()
    {
        return $this->belongsTo(Employeeprofiles::class, 'employeeprofiles_id');
    }
}
