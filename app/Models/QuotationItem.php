<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuotationItem extends Model
{
    protected $table = 'quotation_items';
    protected $primaryKey = 'item_id';
    protected $fillable = [
        'quotation_id',
        'description',
        'qty',
        'unit_price',
        'total',
        'overall_total'
    ];
    /** @use HasFactory<\Database\Factories\QuotationItemFactory> */
    use HasFactory;

    public function quotation()
    {
        return $this->belongsTo(Quotation::class, 'quotation_id', 'quotation_id');
    }
}
