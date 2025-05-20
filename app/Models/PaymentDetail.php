<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentDetail extends Model
{
    //

    protected $table = 'payment_details';

    protected $fillable = [
        'payment_id',
        'bank_name',
        'account_number',

        'additional_info',
    ];



    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

}
