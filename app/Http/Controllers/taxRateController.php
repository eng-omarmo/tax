<?php

namespace App\Http\Controllers;

use App\Models\TaxRate;
use Illuminate\Http\Request;

class taxRateController extends Controller
{
    //

    public function index()
    {

        $taxRates = TaxRate::paginate(5);
        return view('tax.rate.index', ['taxRates' => $taxRates]);
    }

    public function create()
    {
        return view('tax.rate.create');
    }


    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'rate' => 'required',
                'date' => 'required|date',
                'status' => 'required|string',
            ]);
            $checkifexists = TaxRate::where('tax_type', $request->name)->first();
            if ($checkifexists) {
                return back()->with('error', 'Tax rate already exists.')->withInput($request->all());
            }
            TaxRate::create([
                'tax_type' => $request->name,
                'rate' => $request->rate,
                'effective_date' => $request->date,
                'status' => $request->status,
            ]);
            return redirect()->route('tax.rate.index')->with('success', 'Tax rate created successfully.');
        } catch (\Throwable $th) {
            return back()->with('error', 'Failed to create tax rate.' . $th->getMessage());
        }
    }


    public function edit($id)
    {
        $taxRate = TaxRate::find($id);
        return view('tax.rate.edit', ['taxRate' => $taxRate]);
    }

    public function update(Request $request, $id)
    {

        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'rate' => 'required',
                'date' => 'required|date',
                'status' => 'required|string',
            ]);

            $taxRate = TaxRate::find($id);
            $taxRate->tax_type = $request->name;
            $taxRate->rate = $request->rate;
            $taxRate->effective_date = $request->date;
            $taxRate->status = $request->status;
            $taxRate->save();

            return redirect()->route('tax.rate.index')->with('success', 'Tax rate updated successfully.');
        } catch (\Throwable $th) {
            return back()->with('error', 'Failed to update tax rate.' . $th->getMessage());
        }
    }
    public function destroy($id)
    {
        try {
            $taxRate = TaxRate::find($id);
            $taxRate->delete();
            return redirect()->route('tax.rate.index')->with('success', 'Tax rate deleted successfully.');
        } catch (\Throwable $th) {
            return back()->with('error', 'Failed to delete tax rate.' . $th->getMessage());
        }
    }
}
