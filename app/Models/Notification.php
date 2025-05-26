<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'property_id',
        'is_notified',
        'quarter',
        'year',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
