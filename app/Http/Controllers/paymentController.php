<?php

namespace App\Http\Controllers;

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
        $query = Payment::with('tenant');
        if ($request->has('search') && $request->search) {
            $query->whereHas('tenant', function ($q) use ($request) {
                $q->where('tenant_name', 'like', '%' . $request->search . '%')
                    ->orWhere('tenant_phone', 'like', '%' . $request->search . '%');
            });
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
        $tenant = Tenant::where('tenant_phone', trim($request->search_tenant))->first();

        if (!$tenant) {
            return back()->with('error', 'Tenant not found.');
        }

        return view('payment.create', compact('tenant'));
    }

    public function store(Request $request)
    {

        try {
            $request->validate([
                'tenant_id' => 'required|exists:tenants,id',
                'amount' => 'required|numeric|min:0',
                'payment_date' => 'required|date',
                'reference' => 'required|string|max:255',
            ]);
            DB::beginTransaction();
            $payment =   Payment::create([
                'tenant_id' => $request->tenant_id,
                'amount' => $request->amount,
                'payment_date' => now(),
                'reference' => $request->reference,
                'payment_method' => $request->payment_method,
                'status' => 'completed',
            ]);
            $tenant = Tenant::where('id', $payment->tenant_id)->first();
            if ($request->reference == 'Rent') {
                if ($tenant->rent_amount < $payment->amount) {
                    return back()->with('error', 'Payment amount cannot be greater than rent amount.');
                }
                $this->createTransactionRent($tenant, $payment->amount);
            }
            if ($request->reference == 'Tax') {
                if ($tenant->tax_fee < $payment->amount) {
                    return back()->with('error', 'Payment amount cannot be greater than tax fee.');
                }
                $this->createTransactionForTaxFee($tenant, $payment->amount);
            }
            DB::commit();
            return redirect()->route('payment.index')->with('success', 'Payment created successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
            Log::info($th->getMessage());
        }
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

    private function createTransactionRent($tenant, $amount)
    {
        try {

            $transaction = transaction::where('tenant_id', $tenant->id)->where('transaction_type', 'Rent')->first();

            return Transaction::create([
                'tenant_id' => $transaction->tenant_id,
                'transaction_type' => $transaction->transaction_type,
                'amount' => $amount,
                'description' => 'Tenant Paid Rent',
                'credit' => $amount,
                'debit' => 0,
                'status' => 'Pending',
            ]);
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
        }
    }

    private function createTransactionForTaxFee($tenant, $amount)
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
