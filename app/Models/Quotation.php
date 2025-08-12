<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    protected $fillable = [

        'services_id',
        'quotation_file',
        'created_at',
        'created_by'
    ];

    /** @use HasFactory<\Database\Factories\QuotationFactory> */
    use HasFactory;

    public function billings() {

        return $this->hasMany(Billing::class);
    }

    public function services()
    {
        return $this->belongsTo(Services::class);
    }
}
