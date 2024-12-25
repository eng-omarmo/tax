<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\Payment;
use App\Models\Transaction;
use Illuminate\Http\Request;
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
            Payment::create([
                'tenant_id' => $request->tenant_id,
                'amount' => $request->amount,
                'payment_date' => now(),
                'reference' => $request->reference,
                'payment_method' => $request->payment_method,
                'status' => 'completed',
            ]);
            return redirect()->route('payment.index')->with('success', 'Payment created successfully.');
        } catch (\Throwable $th) {
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
}
