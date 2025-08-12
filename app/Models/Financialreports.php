<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Financialreports extends Model
{
    protected $fillable = [

        
        'report_name',
        'generated_by',

    ];
    /** @use HasFactory<\Database\Factories\FinancialreportsFactory> */
    use HasFactory;
}
