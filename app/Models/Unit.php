<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    //

    protected $fillable = [
        'property_id',
        'unit_name',
        'unit_number',
        'unit_type',
        'unit_price',
        'unit_area',
        'is_available',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
    public  function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
