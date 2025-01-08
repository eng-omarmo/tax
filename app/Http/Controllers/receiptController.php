<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

class receiptController extends Controller
{
    //

    public function receipt(Request $request, $id)
    {
        $payment = Payment::with('tax.property.landlord.user', 'paymentDetail')->find($id);

        if (!$payment) {
            return redirect()->back()->with('error', 'Payment not found');
        }

        $data = [
            'amount' => $payment->amount,
            'tax_code' => $payment->tax->tax_code,
            'owner' => $payment->tax->property->landlord->user->name,
            'property' => $payment->tax->property->house_code,
            'bank' => $payment->paymentDetail->bank_name  ,
            'account_number' => $payment->paymentDetail->account_number ,
            'mobile_number' => $payment->paymentDetail->mobile_number ,
            'payment_date' => $payment->payment_date,
            'reference' => $payment->reference,
            'phone' => $payment->tax->property->landlord->user->phone,
            'email' => $payment->tax->property->landlord->user->email,
            'address' => $payment->tax->property->landlord->user->address,
            'payment_method' => $payment->payment_method,
        ];

        return view('receipt.index', compact('data'));
    }

}
