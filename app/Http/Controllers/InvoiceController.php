<?php

namespace App\Http\Controllers;

use Log;
use App\Models\Tax;
use App\Models\Unit;
use App\Models\Invoice;
use App\Models\TaxRate;
use App\Models\Transaction;
use App\Services\TimeService;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use function PHPUnit\Framework\isNull;

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

        $taxRate = TaxRate::where(['tax_type' => $quarter, 'status' => "active"])->value('rate') / 100;

        if (empty($taxRate)) {
            return redirect()->route('index')->with('error', 'No tax Rate is for this ' . $quarter);
        }


        $baseQuery = Unit::where(['is_available' => 1, 'is_owner' => 'no']);
        $data['potentialIncome'] = $baseQuery->sum('unit_price') * $taxRate;
        $baseQuery = Unit::where(['is_available' => 1, 'is_owner' => 'no']);
        $data['potentialIncome'] = $baseQuery->sum('unit_price') * $taxRate;



        $data['flatIncome'] = (clone $baseQuery)->where('unit_type', 'Flat')->sum('unit_price') * $taxRate;
        $data['sectionIncome'] = (clone $baseQuery)->where('unit_type', 'Section')->sum('unit_price') * $taxRate;
        $data['officeIncome'] = (clone $baseQuery)->where('unit_type', 'Office')->sum('unit_price') * $taxRate;
        $data['shopIncome'] = (clone $baseQuery)->where('unit_type', 'Shop')->sum('unit_price') * $taxRate;
        $data['otherIncome'] = (clone $baseQuery)->where('unit_type', 'Other')->sum('unit_price') * $taxRate;
        $data['invoices'] = Invoice::latest()->paginate(10);

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

            $existingInvoice = Invoice::where('frequency', $quarter)
                ->whereYear('invoice_date', now()->year)
                ->get();

            if ($existingInvoice) {
                return redirect()->back()->with('error', 'Invoices for this quarter have already been generated today.');
            }
            $query = Unit::where(['is_available' => 1, 'is_owner' => 'no'])->get();

            if (!$query) {
                return redirect()->back()->with('error', 'there no occupied units to credit invoice in the system');
            }
            foreach ($query as $unit) {
                $calculatedInvoice = $unit->unit_price * $taxRate;
                Invoice::create([
                    'unit_id' => $unit->id,
                    'invoice_number' => 'INV' . strtoupper(uniqid()),
                    'amount' => $calculatedInvoice,
                    'invoice_date' => now(),
                    'due_date' => now()->addDays(30),
                    'frequency' => $quarter,
                    'payment_status' => 'Pending',

                ]);
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
}
