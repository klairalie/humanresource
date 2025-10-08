<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $table = 'activity_logs';
    protected $primaryKey = 'activity_log_id';
    
    protected $fillable = ['action_type', 'description', 'employeeprofiles_id'];  

    use HasFactory;

    

    public function employeeprofiles(){

        return $this->belongsTo(Employeeprofiles::class, 'employeeprofiles_id');
    }
}

