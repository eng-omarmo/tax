<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Tax;
use App\Models\TaxRate;
use Illuminate\Http\Request;

class taxController extends Controller
{
    //
    public function index()
    {
        // Initialize the query with property and transaction relationships
        $query = Tax::with(['property.transactions']);

        // Apply search filter if provided
        if (request()->filled('search')) {
            $search = request('search');
            $query->whereHas('property', function ($q) use ($search) {
                $q->where('property_name', 'like', "%{$search}%")
                    ->orWhere('property_phone', 'like', "%{$search}%");
            });
        }

        // Apply status filter if provided
        if (request()->filled('status')) {
            $status = request('status');
            if ($status === 'Overdue') {
                $query->where('due_date', '<', now()->toDateString());
            } else {
                $query->where('status', $status);
            }
        }

        // Restrict data based on user role
        if (auth()->user()->role === 'Landlord') {
            $query->whereHas('property.landlord', function ($q) {
                $q->where('user_id', auth()->id());
            });
        }

        // Paginate results with a default value for per_page
        $perPage = request('per_page', 5);
        $taxes = $query->paginate($perPage);

        // Calculate balance for each tax entry
        foreach ($taxes as $tax) {
            $tax->balance = $tax->property->transactions->sum(function ($transaction) {
                return $transaction->debit - $transaction->credit;
            });
        }

        // Return view with data
        return view('tax.index', [
            'taxes' => $taxes,
        ]);
    }



    public function create()
    {
        return view('tax.create');
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'property_id' => 'required|exists:properties,id',
                'tax_amount' => 'required|numeric|min:0',
                'due_date' => 'required|date',
            ]);
            $check = Tax::where('property_id', $request->property_id)->where('due_date', $request->due_date,)->first();
            if ($check) {
                return back()->with('error', 'Tax already exists');
            }
            $tax = Tax::create([
                'property_id' => $request->property_id,
                'tax_amount' => $request->tax_amount,
                'due_date' => $request->due_date,
                'tax_code' => 'T' . rand(1000, 9999) . rand(1000, 9999),
                'status' => 'Pending',
            ]);

            $tax->createTransction($tax);

            return redirect()->route('tax.index');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }


    public function edit($id)
    {
        $tax = Tax::find($id);
        return view('tax.edit', ['tax' => $tax]);
    }

    public function update(Request $request, Tax $tax)
    {
        $tax->update([
            'property_id' => $request->property_id,
            'due_date' => $request->due_date
        ]);
        return redirect()->route('tax.index');
    }

    public function destroy($tax)
    {
        $tax = Tax::find($tax);
        $tax->transactions()->delete();
        $tax->delete();

        return redirect()->route('tax.index');
    }

    public function search(Request $request)
    {
        $property =  Property::where('property_phone', $request->search_property)->first();
        if (!$property) {
            return back()->with('error', 'Property not found.');
        }

        return view('tax.create', compact('property'));
    }

    
}
