<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Tax;
use App\Models\Rent;
use App\Models\Tenant;
use App\Models\Payment;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\PaymentDetail;
use App\Models\Property;
use App\Services\PaymentService;
use App\Services\TimeService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class paymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with('invoice', 'paymentDetail');
        $payments = $query->paginate(5);

        foreach ($payments as $payment) {
            $paymentDetail = $payment->paymentDetail->first();
            $payment->paymentDetails = [
                'account' => $paymentDetail ? $paymentDetail->account_number : 'N/A',
                'mobile'  => $paymentDetail ? $paymentDetail->mobile_number : 'N/A',
                'bank'    => $paymentDetail ? $paymentDetail->bank_name : 'N/A',
            ];
        }

        return view('payment.index', [
            'payments' => $payments
        ]);
    }
    public function taxIndex(Request $request)
    {
        $query = Payment::with('invoice', 'paymentDetail');

        $payments = $query->paginate(10);

        return view('payment.tax.index', [
            'payments' => $payments,
        ]);
    }



    public function taxCreate()
    {
        return view('payment.tax.create');
    }


    public function show($id)
    {
        $payment = Payment::findorFail($id);
        return view('payment.show', [
            'payment' => $payment
        ]);
    }

    public function create()
    {
        return view('payment.create');
    }
    public function search(Request $request)
    {

        $rent =  Rent::with('tenant', 'property')->where('rent_code', $request->rent_code)->first();
        if (!$rent) {
            return back()->with('error', 'Tenant not found.');
        }

        return view('payment.create', compact('rent'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'rent_id' => 'nullable|exists:rents,id',
                'amount' => 'required|numeric|min:0',
                'payment_date' => 'required|date',
                'reference' => 'nullable|string|max:255',
            ]);
            DB::beginTransaction();

            $rent = Rent::where('id', $request->rent_id)->first();
            if ($rent->rent_amount < $request->amount) {
                return back()->with('error', 'Payment amount cannot be greater than rent amount.');
            }
            $payment =   Payment::create([
                'rent_id' => $request->rent_id,
                'tax_id' => null,
                'amount' => $request->amount,
                'payment_date' => now(),
                'reference' => 'Rent' . rand(1000, 9999) . rand(1000, 9999),
                'payment_method' => $request->payment_method,
                'status' => 'completed',
            ]);
            $dueAmount = $this->calculateMonthsBetween($rent->rent_start_date, $rent->rent_end_date) * $rent->rent_amount;
            $this->createTransactionRent($rent, $dueAmount, $payment->amount);

            $this->createpaymentDetail($payment, $request);

            DB::commit();
            return redirect()->route('receipt.rent', ['id' => $payment->id]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
            Log::info($th->getMessage());
        }
    }

    public function taxStore(Request $request)
    {
        try {
            $request->validate([
                'tax_id' => 'nullable|exists:taxes,id',
                'amount' => 'required|numeric|min:0',
                'payment_date' => 'required|date',
                'reference' => 'nullable|string|max:255',
            ]);

            DB::beginTransaction();
            $tax = Tax::where('id', $request->tax_id)->first();
            if ($tax->tax_amount < $request->amount) {
                return back()->with('error', 'Payment amount cannot be greater than tax amount.');
            }
            $payment = Payment::create([
                'rent_id' => null,
                'tax_id' => $request->tax_id,
                'amount' => $request->amount,
                'payment_date' => now(),
                'reference' => 'Tax' . rand(1000, 9999) . rand(1000, 9999),
                'payment_method' => $request->payment_method,
                'status' => 'completed',
            ]);


            $this->createTransactionTaxFee($tax, $payment->amount);

            $this->createpaymentDetail($payment, $request);
            DB::commit();
            return redirect()->route('receipt.tax', ['id' => $payment->id]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
            Log::info($th->getMessage());
        }
    }


    function calculateMonthsBetween($startDate, $endDate)
    {

        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);


        if ($start->greaterThan($end)) {
            throw new \InvalidArgumentException("Start date cannot be after the end date.");
        }

        $months = $start->diffInMonths($end) + 1;

        return $months;
    }

    public function getPaymentAmount($tenantId, $paymentType)
    {
        Log::info('Getting payment amount for tenant ID: ' . $tenantId . ' and payment type: ' . $paymentType);
        try {
            $transaction = Transaction::where('tenant_id', $tenantId)
                ->where('transaction_type', $paymentType)
                ->first();
            if ($transaction) {
                return response()->json(['payment_amount' => $transaction->debit]);
            }
            Log::info('No transaction found for tenant ID: ' . $tenantId . ' and payment type: ' . $paymentType);
            return response()->json(['payment_amount' => 0]);
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $payment = Payment::findOrFail($id);

            $payment->update([
                'amount' => $request->amount,
                'status' => $request->status,
                'reference' => $request->reference
            ]);
            return redirect()->route('payment.index')->with('success', 'Payment updated successfully.');  //code...
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $payment = Payment::findOrFail($id);
            $payment->delete();
            return redirect()->route('payment.index')->with('success', 'Payment deleted successfully.');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    private function createTransactionRent($rent, $dueAmount, $paidAmount)
    {
        try {
            return Transaction::create([
                'tenant_id' => $rent->tenant_id ?? null,
                'transaction_id' => 'Tran' . rand(1000, 9999) . rand(1000, 9999),
                'property_id' => $rent->property_id ?? null,
                'transaction_type' => 'Rent',
                'unit_id' => $rent->unit_id ?? null,
                'amount' => $dueAmount,
                'description' => 'Tenant Paid Rent',
                'credit' => $paidAmount,
                'debit' => 0,
                'status' => 'Pending',
            ]);
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
        }
    }

    private function createTransactionTaxFee($tax, $paidAmount)
    {
        try {
            return Transaction::create([
                'tenant_id' => $tax->property->tenant_id ?? null,
                'property_id' => $tax->property_id ?? null,
                'unit_id' => null,
                'transaction_type' => 'Tax',
                'amount' => $tax->tax_amount,
                'description' => 'Paid Tax Fee',
                'credit' => $paidAmount,
                'debit' => 0,
                'status' => 'Pending',
            ]);
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
        }
    }

    //search tax code
    public function searchTax(Request $request)
    {
        $tax = Tax::with(['property'])->where('tax_code', $request->tax_code)->first();

        if (!$tax) {
            return back()->with('error', 'Tax code not found.');
        }

        // if ($tax->status != 'Pending') {
        //     return back()->with('error', 'Tax already paid.');
        // }

        $balance = $tax->property->transactions->sum(function ($transaction) {
            return $transaction->debit - $transaction->credit;
        });

        return view('payment.tax.create', compact('tax', 'balance'));
    }



    private function createpaymentDetail($payment, $request)
    {
        $provider = null;
        $phone = $request->mobile_number;
        if (str_starts_with($phone, '+252')) {
            $phone = substr($phone, 4);
        }

        if (str_starts_with($phone, '61')) {
            $provider = 'Hormuud';
        }
        return Payment::createPaymentDetail([
            'payment_id' => $payment->id,
            'bank_name' => $request->bank_name ?? $provider,
            'account_number' => $request->account_number,
            'mobile_number' => $request->mobile_number,
            'additional_info' => $request->additional_info,
        ]);
    }
}
