<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitToken extends Model
{
    protected $fillable = [
        'token', 'admin_email', 'acting_as', 'target_module', 'expires_at'
    ];

    protected $dates = ['expires_at'];
}
