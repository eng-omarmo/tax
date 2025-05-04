<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rent extends Model
{

    protected $fillable = [
        'property_id',
        'tenant_name',
        'tenant_phone',
        'rent_amount',
        'rent_total_amount',
        'rent_start_date',
        'rent_end_date',
        'status',
        'rent_document',
        'unit_id',
        'rent_code',
    ];


    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
