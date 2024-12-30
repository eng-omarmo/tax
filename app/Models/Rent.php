<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rent extends Model
{

    protected $fillable = [
        'tenant_id',
        'property_id',
        'rent_amount',
        'rent_start_date',
        'rent_end_date',
        'status',
    ];


    public function property()
    {
        return $this->belongsTo(Property::class);
    }
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
