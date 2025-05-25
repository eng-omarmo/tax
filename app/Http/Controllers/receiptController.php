<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class receiptController extends Controller
{
    //

    public function taxReceipt(Request $request, $id)
    {

        $payment = Payment::with('invoice.unit.property.landlord.user', 'paymentDetail')->where('invoice_id', $id)->first();

        if (!$payment) {
            return redirect()->back()->with('error', 'Payment not found');
        }

        $data = [
            'amount' => $payment->amount,
            'invoice_number' => $payment->invoice->invoice_number,
            'tax_code' => 'Tax-' . strtoupper(Str::random(3)) . '-' . rand(100, 999),
            'owner' => $payment->invoice->unit->property->landlord->name,
            'property' => $payment->invoice->unit->property->house_code,
            'bank' => $payment->paymentDetail->bank_name,
            'account_number' => $payment->paymentDetail->account_number,
            'payment_date' => $payment->payment_date,
            'reference' => $payment->reference,
            'phone' => $payment->invoice->unit->property->property_phone,
            'email' => $payment->invoice->unit->property->landlord->email,
            'address' => $payment->invoice->unit->property->landlord->address,
            'payment_method' => $payment->paymentDetail->bank_name,
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
            'bank' => $payment->paymentDetail->bank_name,
            'account_number' => $payment->paymentDetail->account_number,
            'mobile_number' => $payment->paymentDetail->mobile_number,
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
