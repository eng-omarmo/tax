<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Accounts extends Model
{
    protected $fillable = [
        'payment_method_id',
        'account_number',
        'status'
    ];

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }
}
