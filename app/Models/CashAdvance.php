<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class CashAdvance extends Model
{
    protected $primaryKey = 'cash_advance_id';
    protected $fillable = [
        'employeeprofiles_id','amount','reason','filed_date','approved_date','status','created_by'
    ];
    protected $casts = [
        'amount' => 'decimal:2',
        'filed_date' => 'datetime',
        'approved_date' => 'datetime',
    ];
    public function employee(){ return $this->belongsTo(Employeeprofiles::class, 'employeeprofiles_id', 'employeeprofiles_id'); }
    public function creator(){ return $this->belongsTo(Administrativeaccount::class, 'created_by', 'admin_id'); }
}
