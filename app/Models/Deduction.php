<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deduction extends Model
{

     protected $primaryKey = 'deduction_id';
    public $incrementing = true;
    protected $keyType = 'int'; 
    protected $fillable = [

        'employeeprofiles_id',
        'deduction_type',
        'partial_payment',
        'amount',
        'deduction_date',
        'partialpay_date',
    ];

    /** @use HasFactory<\Database\Factories\DeductionFactory> */
    use HasFactory;


    public function employeeprofiles()
    {
        return $this->belongsTo(Employeeprofiles::class, 'employeeprofiles_id');
    }
}
