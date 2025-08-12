<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salaries extends Model
{
    protected $fillable = [

        'employeeprofiles_id',
        'basic_salary',
        'effective_date',
        'status'
    ];
    /** @use HasFactory<\Database\Factories\SalariesFactory> */
    use HasFactory;

    public function employeeprofiles()
    {
        return $this->belongsTo(Employeeprofiles::class);
    }
}
