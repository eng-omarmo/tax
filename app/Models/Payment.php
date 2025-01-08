<?php

namespace App\Models;

use App\Models\PaymentDetail;
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
    public function paymentDetail()
    {
        return $this->hasOne(PaymentDetail::class); // or belongsTo if PaymentDetail holds the foreign key
    }

    public static function createPaymentDetail($data)
    {
        return PaymentDetail::create([
            'payment_id' => $data['payment_id'],
            'bank_name' => $data['bank_name'] ?? null,
            'account_number' => $data['account_number'] ?? null,
            'mobile_number' => $data['mobile_number'] ?? null,
            'additional_info' => $data['additional_info'] ?? null,
        ]);
    }

}
