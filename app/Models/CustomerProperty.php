<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerProperty extends Model
{
    protected $fillable = [
        'customer_id',
        'name',
        'address',
        'suburb',
        'state',
        'postcode',
        'property_type',
        'contact_name',
        'contact_phone',
        'contact_email',
        'latitude',
        'longitude',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
