<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'tenant_name',
        'tenant_phone',
        'rent_amount',
        'tax_fee',
        'status',
        'rental_start_date',
        'rental_end_date',
        'reference'
    ];


    public function property()
    {
        return $this->belongsTo(Property::class);
    }


    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    // App\Models\Tenant.php
public function calculateBalance()
{
    $totalDebits = $this->transactions->sum('debit');
    $totalCredits = $this->transactions->sum('credit');

    return $totalDebits - $totalCredits;
}

}
