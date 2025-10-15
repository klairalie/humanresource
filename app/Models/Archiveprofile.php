<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Archiveprofile extends Model
{

    protected $table = 'archiveprofiles';
    protected $primaryKey = 'archiveprofile_id';

    protected $fillable = [

        'employeeprofiles_id',
        'status',
        'reason',
        'first_name',
        'last_name',
        'address',
        'email',
        'date_of_birth',
        'position',
        'contact_number',
        'emergency_contact',
        'hire_date',
        'card_Idnumber',
        'archived_at',
        'archived_by',
        'reactivated_at',
        'reactivated_by',
       
    ];
    /** @use HasFactory<\Database\Factories\ArchiveprofileFactory> */
    use HasFactory;

    public function employeeprofiles()
    {
        return $this->belongsTo(Employeeprofiles::class, 'employeeprofiles_id', 'employeeprofiles_id');
    }
}