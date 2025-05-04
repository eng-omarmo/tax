<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class unitController extends Controller
{
    //

    public function index()
    {
        $query = Unit::with('property');

        if (request()->has('search') && request()->search) {
            $searchTerm = '%' . request()->search . '%';

            $query->where(function ($q) use ($searchTerm) {
                $q->where('unit_name', 'like', $searchTerm)
                    ->orWhere('unit_number', 'like', $searchTerm)
                    ->orWhere('unit_price', 'like', $searchTerm);
            })
                ->orWhereHas('property', function ($q) use ($searchTerm) {
                    $q->where(function ($q) use ($searchTerm) {
                        $q->where('property_name', 'like', $searchTerm)
                            ->orWhere('property_phone', 'like', $searchTerm);
                    });
                });
        }

        if (request()->has('status') && request()->status) {
            $query->where('is_available', request()->status);
        }

        $units = $query->paginate(10);

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
                'is_owner' => 'required',
                'is_available' => 'required|boolean'
            ]);
            $unitNumber = 'U' . rand(1000, 9999) . rand(1000, 9999);


            unit::create([
                'property_id' => $request->property_id,
                'unit_number' => $unitNumber,
                'unit_name' => $request->unit_name,
                'unit_type' => $request->unit_type,
                'unit_price' => $request->unit_price,
                'is_owner' => $request->is_owner,
                'is_available' => $request->is_available
            ]);
            return redirect()->route('unit.index')->with('success', 'Unit created successfully.');
        } catch (\Throwable $th) {
            Log::error($th);
            return back()->with('error', 'Failed to create unit.' . $th->getMessage());
        }
    }

    public function search(Request $request)
    {
        $request->validate([
            'search_property' => 'required|string',
        ]);

        $search = trim($request->input('search_property'));

        $property = Property::where('house_code', 'like', '%' . $search . '%')->first();
        if (!$property) {
            return back()->with('error', 'Property not found.');
        }

        return view('unit.create', ['property' => $property]);
    }

    public function edit($id)
    {
        $unit = Unit::find($id);
        return view('unit.edit', compact('unit'));
    }


    public function update(Request $request, $id)
    {


        try {
            $request->validate([
                'unit_name' => 'required',
                'unit_price' => 'required',
                'unit_type' => 'required',
                'is_available' => 'required',
            ]);
            $unit = Unit::find($id);
            $unit->update([
                'unit_name' => $request->unit_name,
                'unit_type' => $request->unit_type,
                'unit_price' => $request->unit_price,
                'is_available' => $request->is_available
            ]);
            return redirect()->route('unit.index')->with('success', 'Unit updated successfully.');
        } catch (\Throwable $th) {
            return back()->with('error', 'Failed to update unit.' . $th->getMessage());
        }
    }

    public function viewRent($id)
    {
        $unit = Unit::with([
            'property',
            'currentRent'
        ])->findOrFail($id);

        $isAvailableBadge = function ($isAvailable) {
            if ($isAvailable) {
                return ['success', 'Available', 'checkbox'];
            }
            return ['danger', 'Occupied', 'close'];
        };

        return view('property.monitor.unit.details', compact('unit', 'isAvailableBadge'));
    }
}
