<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Services extends Model
{
   protected $table = 'services';
    protected $primaryKey = 'services_id';

    protected $fillable = [
        'service_type',
        'status',
        'created_at',
        'updated_at'
    ];
    /** @use HasFactory<\Database\Factories\ServicesFactory> */
    use HasFactory;

    public function invoices() {

        return $this->hasMany(Invoices::class);
    }
    
    public function jobstatuses() {

        return $this->hasMany(Jobstatus::class);
    }

    public function pricings()
    {
        return $this->hasMany(Pricing::class);
    }

    public function quotations()
    {
        return $this->hasMany(Quotation::class);
    }

    public function servicerequests()
    {
        return $this->hasMany(Servicerequest::class);
    }
}
