<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Attendance extends Model
{
      protected $table = 'attendances';
    protected $primaryKey = 'attendance_id'; // 👈 change to your actual PK
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
    use HasFactory, Notifiable;

      public function employeeprofiles()
    {
        return $this->belongsTo(Employeeprofiles::class, 'employeeprofiles_id');
    }

    public function routeNotificationForMail($notification)
    {
        return $this->email;
    }
}
