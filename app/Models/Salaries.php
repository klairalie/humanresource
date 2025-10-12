<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salaries extends Model
{
    protected $fillable = [
        
        'basic_salary',
        'position',
        'effective_date',
        'status',
    
    ];
    /** @use HasFactory<\Database\Factories\SalariesFactory> */
    use HasFactory;
    protected $table = 'salaries';
    protected $primaryKey = 'salaries_id';
     protected $casts = [
        'basic_salary' => 'decimal:2',
        'effective_date' => 'date',
    ];
    public function employeeprofiles()
    {
        return $this->hasMany(Employeeprofiles::class, 'salaries_id', 'salaries_id');
    }

    public function employees()
{
    return $this->hasMany(Employeeprofiles::class, 'position', 'position');
}

}
