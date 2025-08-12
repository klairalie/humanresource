<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jobstatus extends Model
{
    protected $fillable = [

        'services_id',
        'employeeprofiles_id',
        'status',
    ];

    /** @use HasFactory<\Database\Factories\JobstatusFactory> */
    use HasFactory;

    public function service() {

        return $this->belongsTo(Services::class);
    }

    public function employeeprofiles() {

        return $this->belongsTo(Employeeprofiles::class);
    }
}
