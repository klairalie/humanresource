<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Servicerequest extends Model
{
    protected $fillable = [

        'services_id',
        'category',
        'customer_name',
        'amount',
        'location',
        'assigned_technician_id',
        'status'
    ];

    /** @use HasFactory<\Database\Factories\ServicerequestFactory> */
    use HasFactory;

    public function services()
    {
        return $this->belongsTo(Services::class);
    }

    public function assignedTechnician()
    {
        return $this->belongsTo(Employeeprofiles::class, 'assigned_technician_id', 'id');
    }
}
