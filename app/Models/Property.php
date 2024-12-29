<?php

namespace App\Models;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Property extends Model
{
    use HasFactory;

    // Define fillable attributes for the property table
    protected $fillable = [
        'property_name',
        'property_phone',
        'nbr',
        'house_code',
        'branch',
        'zone',
        'designation',
        'house_type',
        'house_rent',
        'quarterly_tax_fee',
        'yearly_tax_fee',
        'latitude',
        'longitude',
        'dalal_company_name',
        'monitoring_status',
        'status',
        'district_id',
        'landlord_id'
    ];
    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function landlord()
    {
        return $this->belongsTo(Landlord::class);
    }
}
