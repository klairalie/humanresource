<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Administrativeaccount extends Model
{
    use HasFactory;
    
    protected $table = 'administrativeaccounts';
    protected $primaryKey = 'admin_id';

    protected $fillable = [
        'employeeprofiles_id',
        'username',
        'password',
        'admin_position'
    ];

    public function employee()
    {
        return $this->belongsTo(EmployeeProfiles::class, 'employeeprofiles_id', 'employeeprofiles_id');
    }
}
