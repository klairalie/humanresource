<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
     public $timestamps = false;

    protected $fillable =[

        'employeeprofiles_id',
        'date',
        'time_in',
        'time_out',
        'flag',
        'status',
    ];

    /** @use HasFactory<\Database\Factories\AttendanceFactory> */
    use HasFactory;

      public function employeeprofiles()
    {
        return $this->belongsTo(Employeeprofiles::class, 'employeeprofiles_id');
    }
}
