<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Property;
use App\Models\Transaction;
use App\Models\PaymentDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\TimeService;
use App\Services\PaymentService;

class SelfPaymentController extends Controller
{
    public function selfPayment($id)
    {
        try {
            $data = $this->common($id);
            $property = $data['property'];

            DB::beginTransaction();

            foreach ($property->units as $unit) {
                foreach ($unit->invoices as $invoice) {
                    if (!Payment::where('invoice_id', $invoice->id)->exists()) {
                        $this->createPaymentForInvoice(
                            $invoice,
                            $property->property_phone,
                            $property->id,
                            $data['year'],
                            $data['quarter'],
                            $unit->id
                        );
                    }
                }
            }

            $transData = $this->generateTransactionPayload($property, $data['amount'], $data['year'], $data['quarter']);
            DB::commit();

            return redirect((new PaymentService())->createTransaction($transData));
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Payment processing error', [
                'property_id' => $id,
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ]);
            return back()->with('error', $th->getMessage());
        }
    }

    public function success($id)
    {
        DB::beginTransaction();
        try {
            $timeService = new TimeService();
            $quarter = $timeService->currentQuarter();
            $year = $timeService->currentYear();
            $property = Property::with('units.invoices')->findOrFail($id);
            $now = now();

            $updatedInvoiceCount = 0;
            $totalAmount = 0;

            foreach ($property->units as $unit) {
                foreach ($unit->invoices as $invoice) {
                    if ($invoice->payment_status !== 'Paid') {
                        $this->markInvoiceAsPaid($invoice, $now, $property->id, $year, $quarter, $unit->id);
                        $updatedInvoiceCount++;
                        $totalAmount += $invoice->amount;
                    }
                }
            }

            Log::info('Payment processed successfully', [
                'property_id' => $id,
                'invoices_updated' => $updatedInvoiceCount,
                'total_amount' => $totalAmount
            ]);

            DB::commit();

            return view('self-Payment.success', compact('property', 'totalAmount', 'updatedInvoiceCount'));
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Payment processing error (success callback)', [
                'property_id' => $id,
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ]);

            return redirect()->route('success.payment', $id)
                ->with('error', 'Payment verification failed: ' . $th->getMessage());
        }
    }

    public function fail($id)
    {
        try {
            $property = Property::with('units.invoices')->findOrFail($id);

            foreach ($property->units as $unit) {
                foreach ($unit->invoices as $invoice) {
                    $payment = Payment::where('invoice_id', $invoice->id)->first();
                    if ($payment && $payment->status !== 'Completed') {
                        $payment->status = 'Failed';
                        $payment->save();
                    }
                }
            }

            return view('self-Payment.fail', compact('property'));
        } catch (\Throwable $th) {
            Log::error('Payment fail callback error', [
                'property_id' => $id,
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ]);
            return back()->with('error', $th->getMessage());
        }
    }

    public function retryPayment($id)
    {
        try {
            $data = $this->common($id);
            $transData = $this->generateTransactionPayload($data['property'], $data['amount'], $data['year'], $data['quarter']);

            return redirect((new PaymentService())->createTransaction($transData));
        } catch (\Throwable $th) {
            Log::error('Retry payment error', [
                'property_id' => $id,
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ]);
            return back()->with('error', 'Retry failed: ' . $th->getMessage());
        }
    }

    private function common($id)
    {
        $timeService = new TimeService();
        $quarter = $timeService->currentQuarter();
        $year = $timeService->currentYear();

        $property = Property::with(['units.invoices' => function ($query) use ($quarter, $year) {
            $query->where('frequency', $quarter)
                ->whereYear('invoice_date', $year);
        }])->findOrFail($id);

        $amount = $property->units->sum(function ($unit) {
            return $unit->invoices->sum('amount');
        });

        return [
            'quarter' => $quarter,
            'year' => $year,
            'property' => $property,
            'amount' => $amount
        ];
    }

    private function createPaymentForInvoice($invoice, $phone, $propertyId, $year, $quarter, $unitId)
    {
        $payment = Payment::create([
            'invoice_id' => $invoice->id,
            'amount' => $invoice->amount,
            'payment_date' => now(),
            'status' => 'Pending',
            'reference' => 'tax',
        ]);

        PaymentDetail::create([
            'payment_id' => $payment->id,
            'bank_name' => 'Somxchange',
            'account_number' => $phone,
            'additional_info' => 'Self Payment',
        ]);

        Transaction::create([
            'transaction_id' => 'TXN-' . now()->format('YmdHis') . '-' . $invoice->id,
            'amount' => $invoice->amount,
            'credit' => 0,
            'debit' => $invoice->amount,
            'transaction_type' => 'invoice',
            'description' => "Year $year Quarter $quarter",
            'property_id' => $propertyId,
            'status' => 'Completed',
            'unit_id' => $unitId
        ]);
    }

    private function markInvoiceAsPaid($invoice, $timestamp, $propertyId, $year, $quarter, $unitId)
    {
        $invoice->update([
            'payment_status' => 'Paid',
            'paid_at' => $timestamp
        ]);

        $payment = Payment::where('invoice_id', $invoice->id)->first();
        if ($payment) {
            $payment->status = 'Completed';
            $payment->save();
        }

        Transaction::create([
            'transaction_id' => 'TXN-' . $timestamp->format('YmdHis') . '-' . $invoice->id,
            'amount' => $invoice->amount,
            'credit' => $invoice->amount,
            'debit' => 0,
            'transaction_type' => 'tax_payment',
            'description' => "Year $year Quarter $quarter",
            'property_id' => $propertyId,
            'status' => 'Completed',
            'unit_id' => $unitId
        ]);
    }

    private function generateTransactionPayload($property, $amount, $year, $quarter)
    {
        return [
            "phone" => $property->property_phone,
            "amount" => $amount,
            "currency" => config('app.currency', 'USD'),
            "successUrl" => config('app.url') . "/self-payment/success/{$property->id}",
            "cancelUrl" => config('app.url') . "/self-payment/fail/{$property->id}",
            "order_info" => [
                "item_name" => "$quarter-$year Property Tax",
                "order_no" => $property->house_code,
            ]
        ];
    }
}
