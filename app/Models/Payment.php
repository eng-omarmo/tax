<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payments';

    protected $fillable = [
        'tax_id',
        'rent_id',
        'amount',
        'payment_date',
        'reference',
        'payment_method',
        'status',
    ];

   public function tax()
    {
        return $this->belongsTo(Tax::class);
    }
    public function rent()
    {
        return $this->belongsTo(Rent::class);
    }


}
