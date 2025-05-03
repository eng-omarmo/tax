<?php

namespace App\Models;

use App\Models\Branch;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Property extends Model
{
    use HasFactory;

    // Define fillable attributes for the property table
    protected $fillable = [
        'property_name',
        'property_phone',
        'house_code',
        'branch_id',
        'zone',
        'house_type',
        'latitude',
        'longitude',
        'monitoring_status',
        'status',
        'district_id',
        'landlord_id',
        'image'
    ];
    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function taxs()
    {
        return $this->hasMany(Tax::class);
    }

    public function landlord()
    {
        return $this->belongsTo(Landlord::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function units()
    {
        return $this->hasMany(Unit::class);
    }
    public static function calculateTax($houseType, $monthlyRent)
    {
        $message = '';

        $taxRate = TaxRate::where('tax_type', $houseType)->first();

        if (!$taxRate) {
            $message = 'Tax rate not found for house type: ' . $houseType;
        }

        $monthlyTax = $monthlyRent * ($taxRate->rate / 100);
        $quarterlyTax = $monthlyTax * 3;
        $yearlyTax = $monthlyTax * 12;

        $yearlyHouseRent=  $monthlyTax * 12;

        $data = [
            'quarterly_tax' => $quarterlyTax,
            'yearly_tax' => $yearlyTax,
            'message' => $message,
            'yearlyHouseRent' => $yearlyHouseRent

        ];

        return $data;
    }
}
