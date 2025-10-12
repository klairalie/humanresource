<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{

        protected $primaryKey = 'payroll_id';   // 👈 set your actual PK column
    public $incrementing = true;            // if it’s auto-increment
    protected $keyType = 'int'; 
    
    protected $fillable = [

        
        'employeeprofiles_id',
        'total_days_of_work', 
        'pay_period',
        'pay_period_start',
        'pay_period_end',
        'basic_salary', 
        'overtime_pay', 
        'deductions',
        'bonuses',  
        'status', 
    ];

    /** @use HasFactory<\Database\Factories\PayrollFactory> */
    use HasFactory;

     protected $casts = [
        'pay_period_start' => 'date',
        'pay_period_end' => 'date',
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