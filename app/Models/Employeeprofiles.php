<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employeeprofiles extends Model
{
    protected $table = 'employeeprofiles';
    protected $primaryKey = 'employeeprofiles_id'; // change this to your real PK column
    public $incrementing = true; // or false if not auto-increment
    protected $keyType = 'int';
    protected $fillable = [

        'first_name',
        'last_name',
        'address',
        'position',
        'contact_info',
        'hire_date',
        'status',
        'emergency_contact',
        'fingerprint_data',
    ];
    /** @use HasFactory<\Database\Factories\EmployeeprofilesFactory> */
    use HasFactory;

      public function attendances()
    {
        return $this->hasMany(Attendance::class);
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

    public function salaries()
    {
        return $this->hasMany(Salaries::class);
    }
}
