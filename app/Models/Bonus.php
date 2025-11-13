<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bonus extends Model
{
    protected $table = 'bonuses';
    protected $primaryKey = 'bonus_id';
    protected $fillable = [
        'employeeprofiles_id',
        'bonus_type',
        'bonus_amount',
        'bonus_date',
    ];

    public function employeeprofile()
    {
        return $this->belongsTo(Employeeprofiles::class, 'employeeprofiles_id', 'employeeprofiles_id');
    }
}
