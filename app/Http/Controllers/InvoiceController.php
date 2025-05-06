<?php

namespace App\Http\Controllers;

use App\Models\Tax;
use App\Models\Unit;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\TaxRate;
use App\Models\Accounts;
use App\Models\Property;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Services\TimeService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use function PHPUnit\Framework\isNull;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class InvoiceController extends Controller
{
    public function invoiceAdd()
    {
        return view('invoice/invoiceAdd');
    }

    public function invoiceEdit()
    {
        return view('invoice/invoiceEdit');
    }

    public function invoiceList()
    {
        $quarter = $this->currentQuater();
        $taxRate = 0.05;

        $baseQuery = Unit::where(['is_available' => 1, 'is_owner' => 'no']);
        $data['potentialIncome'] = $baseQuery->sum('unit_price') * $taxRate;

        $data['flatIncome'] = (clone $baseQuery)->where('unit_type', 'Flat')->sum('unit_price') * $taxRate;
        $data['sectionIncome'] = (clone $baseQuery)->where('unit_type', 'Section')->sum('unit_price') * $taxRate;
        $data['officeIncome'] = (clone $baseQuery)->where('unit_type', 'Office')->sum('unit_price') * $taxRate;
        $data['shopIncome'] = (clone $baseQuery)->where('unit_type', 'Shop')->sum('unit_price') * $taxRate;
        $data['otherIncome'] = (clone $baseQuery)->where('unit_type', 'Other')->sum('unit_price') * $taxRate;

        // Get both properties and invoices
        $data['properties'] = Unit::where(['is_available' => 1, 'is_owner' => 'no'])
            ->with(['property.landlord.user', 'property.district', 'invoices'])
            ->get()
            ->pluck('property')
            ->unique();

        $data['invoices'] = Invoice::with(['unit.property'])
            ->where('frequency', $quarter)
            ->latest()
            ->paginate(10);

        $data['quarter'] = $quarter;

        return view('Invoice.index', ['data' => $data]);
    }

    public function generateInvoice()
    {
        try {
            $quarter = $this->currentQuater();
            $taxRate = TaxRate::where(['tax_type' => $quarter, 'status' => "active"])->value('rate') / 100;
            if (empty($taxRate)) {
                return redirect()->route('index')->with('error', 'No tax Rate is for this ' . $quarter);
            }

            // $existingInvoice = Invoice::where('frequency', $quarter)
            //     ->whereYear('invoice_date', now()->year)
            //     ->get();

            // if ($existingInvoice) {
            //     return redirect()->back()->with('error', 'Invoices for this quarter have already been generated today.');
            // }
            $query = Unit::where(['is_available' => 1, 'is_owner' => 'no'])->get();

            if (!$query) {
                return redirect()->back()->with('error', 'there no occupied units to credit invoice in the system');
            }
            foreach ($query as $unit) {
                $calculatedInvoice = $unit->unit_price * $taxRate;
                $invoice =    Invoice::create([
                    'unit_id' => $unit->id,
                    'invoice_number' => 'INV' . strtoupper(uniqid()),
                    'amount' => $calculatedInvoice,
                    'invoice_date' => now(),
                    'due_date' => now()->addDays(30),
                    'frequency' => $quarter,
                    'payment_status' => 'Pending',

                ]);
                $this->createTransactiondebit($invoice);
            }
            return redirect()->back()->with('success', 'invoiced is generated successfully for ' . $quarter);
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
            Log::info($th->getMessage());
        }
    }



    public function invoicePreview()
    {
        return view('invoice/invoicePreview');
    }

    public function create()
    {

        return view('Invoice.create');
    }

    public function pay($id)
    {
        try {
            $paymentMethods = PaymentMethod::all();
            $invoice = Invoice::with('unit')->where('invoice_number', $id)->first();

            //put data in an array
            session()->put('data', [
                'tax_code' => $invoice->tax_code,
                'owner' => $invoice->unit->property->landlord->user->name,
                'property' => $invoice->unit->punit_number,
                'propertyInfo' => $invoice->unit->unit_type . ' ' . $invoice->unit->property->district->name,
                'phone' => $invoice->unit->property->phone_number,
                'email' => $invoice->unit->property->landlord->email,
                'address' => $invoice->unit->property->landlord->address,
                'balance' => 00,
                'amount' => $invoice->tax_amount,
                'amountPaid' => 00,
                'due_date' => $invoice->due_date,
                'issue_date' => now(),
            ]);

            return view('Invoice.create', [
                'invoice' => $invoice,
                'balance' => 00,
                'paymentMethods' => $paymentMethods,
            ]);
        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'Tax not found');
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred.');
        }
    }



    private function currentQuater()
    {
        $timeService = new TimeService();
        $quarter = $timeService->currentQuarter();
        return $quarter;
    }



    public function transaction(Request $request)
    {

        try {
            $request->validate([
                'invoice_number' => 'required|exists:invoices,invoice_number',
                'reference_number' => 'nullable|string|max:255',
                'payment_method_id' => 'required|string|max:255',
                'sender_account' => 'nullable|string|max:255',
            ]);

            DB::beginTransaction();
            $invoice = Invoice::with('unit.property')->where('invoice_number', $request->invoice_number)->first();


            if ($invoice->payment_status == 'Paid') {
                return back()->with('error', 'Invoice already paid.');
            }

            $payment = Payment::create([
                'invoice_id' => $invoice->id,
                'amount' => $invoice->amount,
                'payment_date' => Carbon::now()->format('Y-m-d H:i:s'),
                'reference' => $request->reference_number,
                'status' => 'completed',
            ]);

            $account = Accounts::where('payment_method_id', $request->payment_method_id)->first();
            $account->balance += $invoice->amount;
            $account->save();

            $this->createpaymentDetail($payment, $request);
            $this->createTransactionFee($invoice);
            $invoice->payment_status = 'Paid';
            $invoice->save();
            DB::commit();
            return redirect()->route('receipt.tax', ['id' => $payment->id]);
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }
    private function getPaymentMethod($paymentMethodId)
    {
        return PaymentMethod::findOrFail($paymentMethodId)->name;
    }



    private function createpaymentDetail($payment, $request)
    {
        return Payment::createPaymentDetail([
            'payment_id' => $payment->id,
            'bank_name' =>   $this->getPaymentMethod($request->payment_method_id),
            'account_number' => $request->sender_account,
            'additional_info' => $request->additional_info ?? 'no additional provided'
        ]);
    }
    private function createTransactionFee($invoice)
    {
        try {
            return Transaction::create([
                'transaction_id' => $invoice->invoice_number . '' . uniqid(),
                'property_id' => $invoice->unit->property->id,
                'unit_id' => $invoice->unit->id,
                'transaction_type' => 'credit',
                'amount' => $invoice->amount,
                'description' => 'Paid Tax Fee',
                'credit' => $invoice->amount,
                'debit' => 0,
                'status' => 'completed',
            ]);
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
        }
    }

    private function createTransactiondebit($invoice)
    {
        try {
            return Transaction::create([
                'transaction_id' => $invoice->invoice_number . '' . uniqid(),
                'property_id' => $invoice->unit->property->id,
                'unit_id' => $invoice->unit->id,
                'transaction_type' => 'debit',
                'amount' => $invoice->amount,
                'description' => 'Paid Tax Fee',
                'credit' => 0,
                'debit' => $invoice->amount,
                'status' => 'completed',
            ]);
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
        }
    }
    //details
    public function propertyDetails($id)
    {
        try {
            $property = Property::with(['landlord.user', 'district', 'units.invoices'])
                ->findOrFail($id);

            $totalBilled = $property->units->flatMap->invoices->sum('amount');
            $totalPaid = $property->units->flatMap->invoices
                ->where('payment_status', 'Paid')
                ->sum('amount');

            $currentQuarter = $this->currentQuater();

            return view('Invoice.property.details', compact(
                'property',
                'totalBilled',
                'totalPaid',
                'currentQuarter'
            ));
        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'Property not found.');
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while fetching property details.');
        }
    }
}
