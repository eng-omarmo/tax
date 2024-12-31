<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Rent;
use App\Models\Tenant;
use App\Models\Payment;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class paymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with('tax', 'rent');
        if ($request->has('search') && $request->search) {
            $query->where('reference', 'like', '%' . $request->search . '%')
                ->orWhere('amount', 'like', '%' . $request->search . '%');
        }

        $payments = $query->paginate(5);

        return view('payment.index', [
            'payments' => $payments
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
            if ($request->rent_id) {
                $rent = Rent::where('id', $request->rent_id)->first();
                if ($rent->rent_amount < $request->amount) {
                    return back()->with('error', 'Payment amount cannot be greater than rent amount.');
                }
                $payment =   Payment::create([
                    'rent_id' => $request->rent_id,
                    'tax_id' => null,
                    'amount' => $request->amount,
                    'payment_date' => now(),
                    'reference' => 'Rent',
                    'payment_method' => $request->payment_method,
                    'status' => 'completed',
                ]);
               $dueAmount= $this->calculateMonthsBetween($rent->rent_start_date, $rent->rent_end_date) * $rent->rent_amount;
                $this->createTransactionRent($rent, $dueAmount, $payment->amount);
            } else {
                $payment =   Payment::create([
                    'rent_id' => null,
                    'tax_id' => $request->tax_id,
                    'amount' => $request->amount,
                    'payment_date' => now(),
                    'reference' => 'Tax',
                    'payment_method' => $request->payment_method,
                    'status' => 'completed',
                ]);
                $this->createTransactionTaxFee($payment->tax_id, $payment->amount);
            }
            DB::commit();
            return redirect()->route('payment.index')->with('success', 'Payment created successfully.');
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
                'tenant_id' => $rent->tenant_id ?? null ,
                'property_id' => $rent->property_id ?? null,
                'transaction_type' => 'Rent',
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

    private function createTransactionTaxFee($tenant, $amount)
    {
        try {
            $transaction = transaction::where('tenant_id', $tenant->id)->where('transaction_type', 'Tax')->first();

            return Transaction::create([
                'tenant_id' => $tenant->id,
                'transaction_type' => $transaction->transaction_type,
                'amount' => $amount,
                'description' => 'Paid Tax Fee',
                'credit' => $tenant->tax_fee,
                'debit' => 0,
                'status' => 'Pending',
            ]);
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
        }
    }
}
