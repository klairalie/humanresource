<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
class Employeeprofiles extends Model
{
     

    protected $table = 'employeeprofiles';
    protected $primaryKey = 'employeeprofiles_id';
    protected $fillable = [

        'first_name',
        'last_name',
        'address',
        'email',
        'position',
        'date_of_birth',
        'contact_number',
        'hire_date',
        'status',
        'emergency_contact',
        'card_Idnumber',

    ];
    /** @use HasFactory<\Database\Factories\EmployeeprofilesFactory> */
    use HasFactory, Notifiable;
   
      public function attendances()
    {
        return $this->hasMany(Attendance::class, 'employeeprofiles_id', 'employeeprofiles_id');
    }

      public function expenses()
    {
        return $this->hasMany(Expenses::class);
    }

    public function jobstatuses()
    {
        return $this->hasMany(Jobstatus::class);
    }

     public function leaveovertimerequests()
    {
        return $this->hasMany(Leaveovertimerequest::class);
    }

    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }
    
    public function archiveprofiles()
    {
        return $this->hasMany(Archiveprofile::class);
    }

    public function deductions()
    {
        return $this->hasMany(Deduction::class, 'employeeprofiles_id', 'employeeprofiles_id');
    }
    public function routeNotificationForMail($notification)
    {
        return $this->email;
    }
    
    public function evaluatee()
{
    return $this->belongsTo(Employeeprofiles::class, 'evaluatee_id', 'employeeprofiles_id');
}

public function evaluator()
{
    return $this->belongsTo(Employeeprofiles::class, 'evaluator_id', 'employeeprofiles_id');
}

public function activitylogs(){

    return $this->hasMany(ActivityLog::class, 'employeeprofiles_id');
}

public function salary()
{
    return $this->hasOne(Salaries::class, 'position', 'position');
}

}
