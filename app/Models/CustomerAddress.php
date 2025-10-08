<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerAddress extends Model
{
    use HasFactory; // ✅ Required for ::factory() support

    protected $primaryKey = 'address_id';

    protected $fillable = [
        'customer_id',
        'label',
        'street_address',
        'barangay',
        'city',
        'province',
        'zip_code',
        'is_default',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }
}
