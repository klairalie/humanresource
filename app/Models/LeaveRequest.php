<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class LeaveRequest extends Model
{
    protected $primaryKey = 'leave_request_id';
    protected $fillable = [
        'employeeprofiles_id','start_at','end_at','duration_days','filed_date','approved_date','status','reason','created_by'
    ];
    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'filed_date' => 'datetime',
        'approved_date' => 'datetime',
    ];
    public function employee(){ return $this->belongsTo(Employeeprofiles::class, 'employeeprofiles_id', 'employeeprofiles_id'); }
    public function creator(){ return $this->belongsTo(Administrativeaccount::class, 'created_by', 'admin_id'); }
}
