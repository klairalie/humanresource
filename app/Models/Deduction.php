<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deduction extends Model
{

    protected $primaryKey = 'deduction_id'; 
    protected $fillable = [
        'payroll_id',
        'employeeprofiles_id',
        'deduction_type',
        'partial_payment',
        'amount',
        'date',
    ];

    /** @use HasFactory<\Database\Factories\DeductionFactory> */
    use HasFactory;

    public function payroll()
    {
        return $this->belongsTo(Payroll::class, 'payroll_id', 'payroll_id');
    }

    public function employeeprofiles()
    {
        return $this->belongsTo(Employeeprofiles::class, 'employeeprofiles_id', 'employeeprofiles_id');
    }
}
