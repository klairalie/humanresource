<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceOrderChange extends Model
{
    use HasFactory;

    protected $table = 'service_order_changes';

    protected $fillable = [
        'service_request_id',
        'item_id',
        'changed_by',
        'field',
        'old_value',
        'new_value',
        'reason',
    ];
}
