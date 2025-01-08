<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

class receiptController extends Controller
{
    //

    public function taxReceipt(Request $request, $id)
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

        return view('receipt.tax', compact('data'));
    }

    public function rentReceipt(Request $request, $id)
    {
        $payment = Payment::with('rent.property.landlord.user', 'paymentDetail')->find($id);

        if (!$payment) {
            return redirect()->back()->with('error', 'Payment not found');
        }

        $data = [
            'amount' => $payment->amount,
            'rent_code' => $payment->rent->rent_code,
            'tenant' => $payment->rent->tenant->user->name,
            'property' => $payment->rent->property->house_code,
            'unit' => $payment->rent->unit->unit_type,
            'bank' => $payment->paymentDetail->bank_name  ,
            'account_number' => $payment->paymentDetail->account_number ,
            'mobile_number' => $payment->paymentDetail->mobile_number ,
            'payment_date' => $payment->payment_date,
            'reference' => $payment->reference,
            'phone' => $payment->rent->tenant->user->phone,
            'email' => $payment->rent->tenant->user->email,
            'address' => $payment->rent->tenant->user->address,
            'payment_method' => $payment->payment_method,
        ];

        return view('receipt.rent', compact('data'));
    }

}
