<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Administrativeaccount extends Model
{
    protected $fillable = [

        'admin_id',
        'username',
        'password',
        'first_name',
        'last_name',
        'contact_info',

    ];

    /** @use HasFactory<\Database\Factories\AdministrativeaccountFactory> */
    use HasFactory;

    public function billing() {

        return $this->hasMany(Billing::class);
    }
}
