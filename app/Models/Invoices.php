<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoices extends Model
{
    protected $fillable = [

        'services_id',
        'amount',
        'tax',
        'discount',
        'total',
        'status',
        'date'
    ];
    /** @use HasFactory<\Database\Factories\InvoicesFactory> */
    use HasFactory;

    public function expenses() {

        return $this->hasMany(Expenses::class);
    }

    public function service() {

        return $this->belongsTo(Services::class);
    }
}
