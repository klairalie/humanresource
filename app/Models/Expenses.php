<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expenses extends Model
{
    protected $fillable = [
        'invoices_id',
        'employeeprofiles_id',
        'amount',
        'description',
        'date'
    ];

    /** @use HasFactory<\Database\Factories\ExpensesFactory> */
    use HasFactory;

    public function invoices() {

        return $this->belongsTo(Invoices::class);
    }

    public function employeeprofiles() {

        return $this->belongsTo(Employeeprofiles::class);
    }
}
