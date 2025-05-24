<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Property;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\PaymentDetail;
use App\Services\TimeService;
use App\Services\PaymentService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SelfPaymentController extends Controller
{
    //

    public function selfPayment($id)
    {

        try {


            $data =  $this->common($id);
            // Check if any unit is missing an invoice for the current period
            $unitsWithoutInvoices = $data['property']->units->filter(function ($unit) {
                return $unit->invoices->isEmpty();
            });

            if ($unitsWithoutInvoices->isNotEmpty()) {
                $unitNumber = $unitsWithoutInvoices->first()->unit_number;
                Log::info('Units without invoices: ' . $unitNumber);
            }

            DB::beginTransaction();
            foreach ($data['property']->units as $unit) {
                foreach ($unit->invoices as $invoice) {
                    $paymentExists = Payment::where('invoice_id', $invoice->id)->first();
                    if (!$paymentExists) {
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
                            'account_number' => $data['property']->property_phone,
                            'additional_info' => 'Self Payment',
                        ]);
                        Transaction::create(
                            [
                                'transaction_id' => 'TXN-' . now()->format('YmdHis') . '-' . $invoice->id,
                                'amount' => $invoice->amount,
                                'credit' => 0,
                                'debit' => $invoice->amount,
                                'transaction_type' => 'invoice',
                                'description' => 'Year  ' . $data['year'] . '   Quarter '  . $data['quarter'],
                                'property_id' => $data['property']->id,
                                'status' => 'Completed',
                                'unit_id' => $unit->id
                            ]
                        );
                    }
                }
            }
            $transData = [
                "phone" => $data['property']->property_phone,
                "amount" => $data['amount'],
                "currency" => config('app.currency', 'USD'),
                "successUrl" => config('app.url') . "/self-payment/success/{$data['property']->id}",
                "cancelUrl" => config('app.url') . "/self-payment/fail/{$data['property']->id}",
                "order_info" => [
                    "item_name" => "{$data['quarter']}-{$data['year']} Property Tax",
                    "order_no" => $data['property']->house_code,
                ]
            ];
            DB::commit();
            //  return 'success';
            $payment = new PaymentService();
            $url = $payment->createTransaction($transData);
            return redirect($url);
        } catch (\Throwable $th) {
            Log::error('Payment processing error: ' . $th->getMessage(), [
                'property_id' => $id,
                'trace' => $th->getTraceAsString()
            ]);
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    //success url
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

            $units  = $property->units;
            foreach ($units as $unit) {
                foreach ($unit->invoices as $invoice) {

                    if ($invoice->payment_status != 'Paid') {
                        $invoice->update([
                            'payment_status' => 'Paid',
                            'paid_at' => $now
                        ]);

                        $payment = Payment::where('invoice_id', $invoice->id)->first();
                        $payment->status = 'Completed';

                        $payment->save();

                        Transaction::create([
                            'transaction_id' => 'TXN-' . $now->format('YmdHis') . '-' . $invoice->id,
                            'amount' => $invoice->amount,
                            'credit' => $invoice->amount,
                            'debit' => 0,
                            'transaction_type' => 'tax_payment',
                            'description' => 'Year  ' . $year . '   Quarter '  . $quarter,
                            'property_id' => $property->id,
                            'status' => 'Completed',
                            'unit_id' => $unit->id
                        ]);

                        $updatedInvoiceCount++;
                        $totalAmount += $invoice->amount;
                    }
                }
            }

            Log::info('Payment processed successfully', [
                'property_id' => $id,
                'Payment' => $payment->status,
                'invoices_updated' => $updatedInvoiceCount,
                'total_amount' => $totalAmount
            ]);

            DB::commit();
            return view('self-Payment.success', compact('property', 'totalAmount', 'updatedInvoiceCount'));
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Payment processing error: ' . $th->getMessage(), [
                'property_id' => $id,
                'trace' => $th->getTraceAsString()
            ]);

            return redirect()->route('success.payment', $id)
                ->with('error', 'Payment verification failed: ' . $th->getMessage());
        }
    }

    //fail url
    public function fail($id)
    {
        try {
            $property = Property::with('unit', 'unit.invoices')->findOrFail($id);
            $units  = $property->units;
            foreach ($units as $unit) {
                foreach ($unit->invoices as $invoice) {
                    $payment = Payment::where('invoice_id', $invoice->id)->first();
                    $payment->status = 'Completed';
                }
            }
            return view('self-Payment.fail', compact('property'));
        } catch (\Throwable $th) {
            Log::error('Payment processing error: ' . $th->getMessage(), [
                'property_id' => $id,
                'trace' => $th->getTraceAsString()
            ]);
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }
    public function retryPayment($id)
    {
        try {
            $data = $this->common($id);
            $data = [
                "phone" => $data['property']->property_phone,
                "amount" => $data['amount'],
                "currency" => config('app.currency', 'USD'),
                "successUrl" => config('app.url') . "/self-payment/success/{$data['property']->id}",
                "cancelUrl" => config('app.url') . "/self-payment/fail/{$data['property']->id}",
                "order_info" => [
                    "item_name" => "{$data['quarter']}-{$data['year']} Property Tax",
                    "order_no" => $data['property']->house_code,
                ]
            ];
            $payment = new PaymentService();
            $url = $payment->createTransaction($data);
            return redirect($url);
        } catch (\Throwable $th) {
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
        }])->where('id', $id)->first();
        $amount = $property->units->sum(function ($unit) {
            return $unit->invoices->sum('amount');
        });
        $data = [
            'quater' => $quarter,
            'year' => $year,
            'property' => $property,
            'amount' => $amount
        ];
        return  $data;
    }
}
