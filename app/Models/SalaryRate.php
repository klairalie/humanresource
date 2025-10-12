<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryRate extends Model
{
    protected $table = 'salary_rates';
    protected $primaryKey = 'salary_rate_id';

    protected $fillable = [
        'position',
        'salary_rate',
        'status'
    ];

    protected $casts = [
        'salary_rate' => 'decimal:2',
    ];

    // public function employeeprofiles() {

    // }
}
