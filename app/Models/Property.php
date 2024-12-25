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
        'latitude',
        'longitude',
        'dalal_company_name',
        'is_owner',
        'monitoring_status',
        'status',
        'district_id'
    ];

   
    // You can define other relationships if needed (e.g., for district or transactions)
    public function district()
    {
        return $this->belongsTo(District::class); // A property belongs to a district
    }
}
