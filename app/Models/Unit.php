<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $fillable = [
        'property_id',
        'unit_name',
        'unit_number',
        'unit_type',
        'unit_price',
        'is_available',
        'is_owner'
    ];

    /**
     * Get the current active rent for the unit
     */
    public function currentRent()
    {
        return $this->hasOne(Rent::class)
            ->where('status', 'active')
            ->latest();
    }

    /**
     * Get the property this unit belongs to
     */
    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Get all transactions associated with this unit
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get all rent payments for this unit
     */
    public function rents()
    {
        return $this->hasMany(Payment::class);
    }
}
