<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class OvertimeRequest extends Model
{
    protected $primaryKey = 'overtime_request_id';
    protected $fillable = [
        'employeeprofiles_id','service_request_item_id','hours','amount','filed_date','approved_date','status','created_by','release_date','reason'
    ];
    protected $casts = [
        'hours' => 'decimal:2',
        'amount' => 'decimal:2',
        'filed_date' => 'datetime',
        'approved_date' => 'datetime',
        'release_date' => 'datetime',
    ];
    public function employee(){ return $this->belongsTo(Employeeprofiles::class, 'employeeprofiles_id', 'employeeprofiles_id'); }
    public function creator(){ return $this->belongsTo(Administrativeaccount::class, 'created_by', 'admin_id'); }
    public function item(){ return $this->belongsTo(ServiceRequestItem::class, 'service_request_item_id', 'item_id'); }
}
