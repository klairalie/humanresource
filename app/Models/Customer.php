<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    // Matches your migration primary key
    protected $primaryKey = 'customer_id';

    // If you didn't set $table, Laravel will use 'customers' automatically
    // protected $table = 'customers';

    // Allow mass assignment for these fields
    protected $fillable = [
        'full_name',
        'business_name',
        'contact_info',
        'email',
    ];

    // Optional casts (if you later add JSON fields etc)
    protected $casts = [
        // 'contact_info' => 'array',
    ];

    /* ---------------------
       Relationships
       --------------------- */

    // One customer can have many addresses
    public function addresses()
    {
        return $this->hasMany(CustomerAddress::class, 'customer_id', 'customer_id');
    }

    // One customer can have many service requests
    public function serviceRequests()
    {
        return $this->hasMany(ServiceRequest::class, 'customer_id', 'customer_id');
    }

    /* ---------------------
       Convenience accessors
       --------------------- */

    // Display name for UI (Full name plus business in parentheses when present)
    public function getDisplayNameAttribute()
    {
        return $this->business_name
            ? trim($this->full_name . ' (' . $this->business_name . ')')
            : $this->full_name;
    }

    // Short contact (you can adjust parsing as needed)
    public function getContactShortAttribute()
    {
        return $this->contact_info;
    }
}