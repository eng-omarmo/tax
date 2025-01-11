<?php

namespace App\Http\Controllers;

use App\Models\Tax;
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
        return view('invoice/invoiceList');
    }

    public function invoicePreview()
    {
        return view('invoice/invoicePreview');
    }

    public function create()
    {
        return view('invoice.create');
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
                'phone' => $tax->property->landlord->user->phone,
                'email' => $tax->property->landlord->user->email,
                'address' => $tax->property->landlord->user->address,
                'balance' => $balance,
                'amount' => $tax->tax_amount,
                'amountPaid' => $amountPAid,
                'due_date' => $tax->due_date,
                'issue_date' => now(),
            ]);

            return view('invoice.create', [
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
        return view('invoice.preview', compact('data'));
    }
}
