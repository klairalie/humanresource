<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceRequest extends Model
{
    use HasFactory;

    // explicit table name (matches your DB)
    protected $table = 'service_requests';

    // match your migration PK
    protected $primaryKey = 'service_request_id';

    // if primary key is not auto-incrementing int, adjust these:
    public $incrementing = true;
    protected $keyType = 'int';

    // mass assignment
    protected $fillable = [
        'customer_id','address_id','service_date','start_date','end_date','start_time','end_time',
        'type_of_payment','order_status','payment_status','accomplishment_date','remarks','service_request_number'
    ];

    // relationships...
    public function customer() { return $this->belongsTo(Customer::class, 'customer_id', 'customer_id'); }
    public function address() { return $this->belongsTo(CustomerAddress::class, 'address_id', 'address_id'); }
    public function items() { return $this->hasMany(ServiceRequestItem::class, 'service_request_id', 'service_request_id'); }
}