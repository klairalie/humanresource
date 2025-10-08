<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AirconServicePrice extends Model
{
    use HasFactory;

    protected $primaryKey = 'aircon_service_price_id';
    protected $fillable = [
        'services_id',
        'aircon_type_id',
        'price',
        'status',
    ];

    public function service()
    {
        return $this->belongsTo(Services::class, 'services_id', 'services_id');
    }

    public function airconType()
    {
        return $this->belongsTo(AirconType::class, 'aircon_type_id', 'aircon_type_id');
    }
}