<?php

namespace App\Models;

use App\Models\PaymentDetail;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payments';

    protected $fillable = [
        'invoice_id',
        'amount',
        'payment_date',
        'reference',
        'status',
    ];

    public function invoice()
    {
        return $this->belongsTo(invoice::class);
    }

    public function paymentDetail()
    {
        return $this->hasOne(PaymentDetail::class);
    }

    public static function createPaymentDetail($data)
    {
        return PaymentDetail::create([
            'payment_id' => $data['payment_id'],
            'bank_name' => $data['bank_name'] ?? null,
            'account_number' => $data['account_number'] ?? null,
            'additional_info' => $data['additional_info'] ?? null,
        ]);
    }
}
