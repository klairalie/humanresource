<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    protected $fillable = [

        
        'employeeprofiles_id',
        'total_days_of_work', 
        'pay_period',
        'pay_period_start',
        'pay_period_end',
        'basic_salary', 
        'overtime_pay', 
        'deductions', 
        'net_pay', 
        'status', 
    ];

    /** @use HasFactory<\Database\Factories\PayrollFactory> */
    use HasFactory;

     protected $casts = [
        'basic_salary' => 'decimal:2',
        'overtime_pay' => 'decimal:2',
        'deductions' => 'decimal:2',
        'net_pay' => 'decimal:2',
    ];


    public function employeeprofiles()
{
    return $this->belongsTo(Employeeprofiles::class, 'employeeprofiles_id', 'employeeprofiles_id');
}

}
