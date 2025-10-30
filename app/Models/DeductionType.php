<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeductionType extends Model
{

    protected $primaryKey = 'deductiontype_id';
    public $incrementing = true;
    protected $keyType = 'int'; 
    protected $fillable = [

        'deduction_type',
        'amount',
    ];

    /** @use HasFactory<\Database\Factories\DeductionFactory> */
    use HasFactory;

}
