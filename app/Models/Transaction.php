<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;


    protected $fillable = [
        'tenant_id',
        'property_id',
        'amount',
        'credit',
        'debit',
        'transaction_type',
        'description',
        'property_id',
        'status',
        'unit_id'
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
    public function property()
    {
        return $this->belongsTo(Property::class);
    }
    public function units ()
    {
        return $this->belongsTo(Unit::class);
    }
}
