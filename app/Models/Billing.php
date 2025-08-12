<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Billing extends Model
{
    protected $fillable = [
        'quotation_id',
        'user_id',
        'amount',
        'status',
        'date',
    ];
    /** @use HasFactory<\Database\Factories\BillingFactory> */
    use HasFactory;

    public function quotation() {

        return $this->belongsTo(Quotation::class);
    }

    public function user(){

        return $this->belongsTo(Administrativeaccount::class, 'user_id', 'id');
    }

}
