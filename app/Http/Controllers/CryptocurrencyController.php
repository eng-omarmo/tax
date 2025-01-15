<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\TaxRate;
use App\Models\Unit;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\CssSelector\Node\FunctionNode;

class CryptocurrencyController extends Controller
{
    public function marketplace()
    {
        return view('cryptocurrency/marketplace');
    }

    public function marketplaceDetails()
    {
        return view('cryptocurrency/marketplaceDetails');
    }

    public function portfolio()
    {
        return view('cryptocurrency/portfolio');
    }

    public function wallet()
    {
        $data['potentialIncome'] = Unit::where('is_available', 1)->sum('unit_price');

        $data['flatIncome'] = Unit::where('is_available', 1)->where('unit_type', 'Flat')->sum('unit_price');
        $data['sectionIncome'] = Unit::where('is_available', 1)->where('unit_type', 'Section')->sum('unit_price');
        $data['officeIncome'] = Unit::where('is_available', 1)->where('unit_type', 'Office')->sum('unit_price');
        $data['shopIncome'] = Unit::where('is_available', 1)->where('unit_type', 'Shop')->sum('unit_price');
        $data['otherIncome'] = Unit::where('is_available', 1)->where('unit_type', 'Other')->sum('unit_price');
        $data['invoices'] = Invoice::paginate(10);
        return view('cryptocurrency.wallet', compact('data'));
    }


    public function quarter1(Request $request)
    {
        try {
            $request->validate([
                'q1' => 'required',
            ]);
            $activeUnits = Unit::where('is_available', 1)->get();
            $taxRate = TaxRate::where('tax_type', 'quater1')->first();
            $calculatedInvoice = $activeUnits->price * $taxRate->rate / 100;
            foreach ($activeUnits as $unit) {
                Invoice::create([
                    'unit_id' => $unit->id,
                    'invoice_number' => 'INV' . strtoupper(uniqid()),
                    'amount' => $calculatedInvoice,
                    'invoice_date' => now(),
                    'due_date' => now()->addDays(30),
                    'frequency' => 'quarter1',
                    'payment_status' => 'pending',
                ]);
            }
            return redirect()->back()->with('success', 'invoiced is generated successfully for quater 1');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }


}
