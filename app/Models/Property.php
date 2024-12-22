<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
         'property_name', 'property_phone', 'nbr', 'house_code', 'tenant_name', 'tenant_phone', 'branch', 'zone', 'designation', 'house_type', 'house_rent', 'quarterly_tax_fee', 'yearly_tax_fee', 'latitude', 'longitude', 'dalal_company_name', 'is_owner', 'monitoring_status', 'status'
    ];
}
