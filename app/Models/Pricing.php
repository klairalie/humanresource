<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pricing extends Model
{
    protected $fillable = [

        'services_id',
        'price',
        'created_at',
        'status'
    ];

    /** @use HasFactory<\Database\Factories\PricingFactory> */
    use HasFactory;

    public function services()
    {
        return $this->belongsTo(Services::class);
    }
}
