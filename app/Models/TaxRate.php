<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxRate extends Model
{
    //

    protected $table = 'tax_rates';

    protected $fillable = [
        'rate',
        'tax_type',
        'effective_date',
        'status',
    ];
}
