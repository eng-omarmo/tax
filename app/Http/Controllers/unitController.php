<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Property;
use Illuminate\Http\Request;

class unitController extends Controller
{
    //

    public function index()
    {
        $units = Unit::with('property')->paginate(5);
        return view('unit.index', compact('units'));
    }
    public function create()
    {
        return view('unit.create');
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'property_id' => 'required|exists:properties,id',
                'unit_name' => 'required',

                'unit_price' => 'required',
            ]);
            $unitNumber = 'U' . rand(1000, 9999) . rand(1000, 9999);

            unit::create([
                'property_id' => $request->property_id,
                'unit_number' => $unitNumber,
                'unit_name' => $request->unit_name,
                'unit_type' => $request->unit_type,
                'unit_price' => $request->unit_price,
                'is_available' => 1
            ]);
            return redirect()->route('unit.index')->with('success', 'Unit created successfully.');
        } catch (\Throwable $th) {
            return back()->with('error', 'Failed to create unit.' . $th->getMessage());
        }
    }



    public function search(Request $request)
    {
        $request->validate([
            'search_property' => 'required|string',
        ]);

        $search = trim($request->input('search_property'));

        $property = Property::where('property_phone', 'like', '%' . $search . '%')->first();
        if (!$property) {
            return back()->with('error', 'Property not found.');
        }

        return view('unit.create', ['property' => $property]);
    }
}
