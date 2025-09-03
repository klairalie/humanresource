<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    protected $fillable = [

         'services_id',
        'company_name',
        'contact_number',
        'company_email',
        'client_name',
        'attention',
        'subject',
        'quotation_date',
        'equipment_terms',
        'installation_terms',
        'warranty',
        'payable_to',
        'exception',
        'guarantee',
        'proprietor_name',
    ];

    /** @use HasFactory<\Database\Factories\QuotationFactory> */
    use HasFactory;

    public function billings() {

        return $this->hasMany(Billing::class);
    }

     public function items()
    {
        return $this->hasMany(QuotationItem::class, 'quotation_id', 'quotation_id');
 }

    public function services()
    {
        return $this->belongsTo(Services::class, 'services_id', 'services_id');
    }
}
