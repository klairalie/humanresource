<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AirconType extends Model
{
    use HasFactory;

    protected $table = 'aircon_types';
    protected $primaryKey = 'aircon_type_id';

    // Mass assignable fields
    protected $fillable = [
        'name',
        'brand',
        'capacity',
        'model',
        'category',
        'base_price',
        'description',
        'features',
        'image_path',
        'status',
        'supplier_id',
    ];
    public function servicePrices()
    {
        return $this->hasMany(AirconServicePrice::class, 'aircon_type_id', 'aircon_type_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'supplier_id');
    }
}
