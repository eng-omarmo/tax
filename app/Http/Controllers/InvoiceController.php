<?php

namespace App\Http\Controllers;

use Log;
use App\Models\Tax;
use App\Models\Unit;
use App\Models\Invoice;
use App\Models\TaxRate;
use App\Models\Transaction;
use Illuminate\Http\Request;
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

        $data['potentialIncome'] = Unit::where('is_available', 1)->sum('unit_price');

        $data['flatIncome'] = Unit::where('is_available', 1)->where('unit_type', 'Flat')->sum('unit_price');
        $data['sectionIncome'] = Unit::where('is_available', 1)->where('unit_type', 'Section')->sum('unit_price');
        $data['officeIncome'] = Unit::where('is_available', 1)->where('unit_type', 'Office')->sum('unit_price');
        $data['shopIncome'] = Unit::where('is_available', 1)->where('unit_type', 'Shop')->sum('unit_price');
        $data['otherIncome'] = Unit::where('is_available', 1)->where('unit_type', 'Other')->sum('unit_price');
        $data['invoices'] = Invoice::latest()->paginate(10);


        return view(
            'invoice.index',
            [
                'data' => $data
            ]
        );
    }
    public function quarter1(Request $request)
    {
        try {
            $request->validate([
                'q1' => 'required',
            ]);
            $activeUnits = Unit::where('is_available', 1)->get();

            $taxRate = TaxRate::first();
            if (!$taxRate) {
                return redirect()->back()->with('error', 'Tax rate not set');
            }
            $rate = $taxRate->rate / 100;
            foreach ($activeUnits as $unit) {
                $calculatedInvoice = $unit->unit_price * $rate;
                $invoice =  Invoice::create([
                    'unit_id' => $unit->id,
                    'invoice_number' => 'INV' . strtoupper(uniqid()),
                    'amount' => $calculatedInvoice,
                    'invoice_date' => now(),
                    'due_date' => now()->addDays(30),
                    'frequency' => 'quarter1',
                    'payment_status' => 'Pending',
                ]);
                $this->createTransaction($invoice);
            }
            return redirect()->back()->with('success', 'invoiced is generated successfully for quater 1');
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

    public function search(Request $request)
    {

        $request->validate([
            'tax_code' => 'required|string',
        ]);

        try {
            $tax = Tax::with(['property.transactions', 'property.landlord.user'])
                ->where('tax_code', $request->tax_code)
                ->firstOrFail();

            $balance = $tax->property->transactions->where('transaction_type', 'Tax')->sum(function ($transaction) {
                return $transaction->debit - $transaction->credit;
            });

            $amountPAid = $tax->property->transactions->where('transaction_type', 'Tax')->sum('credit');

            //put data in an array

            session()->put('data', [
                'tax_code' => $tax->tax_code,
                'owner' => $tax->property->landlord->user->name,
                'property' => $tax->property->house_code,
                'propertyInfo' => $tax->property->type . ' ' . $tax->property->district->name,
                'phone' => $tax->property->landlord->phone_number,
                'email' => $tax->property->landlord->email,
                'address' => $tax->property->landlord->address,
                'balance' => $balance,
                'amount' => $tax->tax_amount,
                'amountPaid' => $amountPAid,
                'due_date' => $tax->due_date,
                'issue_date' => now(),
            ]);

            return view('Invoice.create', [
                'tax' => $tax,
                'balance' => $balance,
            ]);
        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'Tax not found');
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while searching for the tax record.');
        }
    }

    public function generateTaxInvoice(Request $request)
    {
        $data = session()->get('data');
        return view('Invoice.preview', compact('data'));
    }


    private function createTransaction($invoice)
    {
        try {
            return Transaction::create([
                'transaction_id' => 'Tran' . rand(1000, 9999) . rand(1000, 9999),
                'property_id' => $invoice->unit->property_id,
                'unit_id' => $invoice->unit_id,
                'transaction_type' => 'Tax',
                'amount' => $invoice->amount,
                'description' => 'Tax Invoice for ' . $invoice->frequency,
                'credit' => 0,
                'debit' => $invoice->amount,
                'status' => 'Completed',
            ]);
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
        }
    }
}
