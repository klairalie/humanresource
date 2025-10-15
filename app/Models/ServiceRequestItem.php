<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceRequestItem extends Model
{
    use HasFactory;

    protected $primaryKey = 'item_id';
    protected $table = 'service_request_items';

    protected $fillable = [
        'service_request_id',
        'services_id',
        'aircon_type_id',
        'service_type',
        'unit_type',
        'quantity',
        'unit_price',
        'discount',
        'tax',
        'line_total',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'assigned_technician_id',
        'status',
        'bill_separately',
        'billed',
        'service_notes',
        'requested_service_date',
        'requested_service_time',
    ];

    /**
     * Relationships
     */
    public function serviceRequest()
    {
        return $this->belongsTo(ServiceRequest::class, 'service_request_id', 'service_request_id');
    }

    public function service()
    {
        return $this->belongsTo(Services::class, 'services_id', 'services_id');
    }

    // public function airconType()
    // {
    //     return $this->belongsTo(AirconType::class, 'aircon_type_id', 'aircon_type_id');
    // }

    public function assignedTechnician()
    {
        return $this->belongsTo(Employeeprofiles::class, 'assigned_technician_id', 'employeeprofiles_id');
    }

    public function technicianAssignments()
    {
        return $this->hasMany(TechnicianAssignment::class, 'item_id', 'item_id');
    }

    public function technician()
{
    return $this->belongsTo(Employeeprofiles::class, 'assigned_technician_id', 'employeeprofiles_id');
}

    public function assistantTechnicians()
    {
        return $this->belongsToMany(
            Employeeprofiles::class,
            'technician_assignments',
            'item_id',
            'technician_id'
        )->wherePivot('role', 'Assistant');
    }

    public function leadTechnicians()
    {
        return $this->belongsToMany(
            Employeeprofiles::class,
            'technician_assignments',
            'item_id',
            'technician_id'
        )->wherePivot('role', 'Lead');
    }
}