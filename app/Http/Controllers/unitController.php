<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

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

    public function create(Request $request, $id)
    {

        $data['property'] = Property::where('id', $id)->first();
        if (!$data['property']) {
            return back()->with('error', 'Property not found.');
        }
        return view('unit.create', $data);
    }


    public function store(Request $request)
    {
        // Remove the dd() statement
        // dd($request->all());
        try {
            // Validate common fields
            $validator = Validator::make($request->all(), [
                'property_id' => 'required|exists:properties,id',
                'is_owner' => 'required|in:yes,no',
                'is_available' => 'required|boolean'
            ]);

            if ($validator->fails()) {
                return back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('current_step', $request->input('current_step', 1));
            }

            // Validate each unit's data
            if (!$request->has('units') || !is_array($request->units)) {
                return back()
                    ->withErrors(['units' => 'No units provided'])
                    ->withInput()
                    ->with('current_step', $request->input('current_step', 1));
            }

            foreach ($request->units as $index => $unitData) {
                $unitValidator = Validator::make($unitData, [
                    'unit_name' => 'required|string|max:255',
                    'unit_type' => 'required|in:Flat,Section,Office,Shop,Other',
                    'unit_price' => 'required|numeric|min:0',
                ], [
                    'unit_name.required' => 'Unit name is required for unit #' . ($index + 1),
                    'unit_type.required' => 'Unit type is required for unit #' . ($index + 1),
                    'unit_price.required' => 'Monthly rent is required for unit #' . ($index + 1),
                ]);

                if ($unitValidator->fails()) {
                    return back()
                        ->withErrors($unitValidator)
                        ->withInput()
                        ->with('current_step', $request->input('current_step', 1));
                }
            }

            // Create each unit
            foreach ($request->units as $unitData) {
                // Generate a unique unit number
                $unitNumber = 'U' . rand(1000, 9999) . rand(1000, 9999);

                // Determine availability and owner status from the unit's status
                $isAvailable = ($unitData['status'] === 'available') ? 0 : 1; // 0 = available, 1 = occupied
                $isOwner = ($unitData['status'] === 'owner') ? 'yes' : 'no';

                // Create the unit
                Unit::create([
                    'property_id' => $request->property_id,
                    'unit_number' => $unitNumber,
                    'unit_name' => $unitData['unit_name'],
                    'unit_type' => $unitData['unit_type'],
                    'unit_price' => $unitData['unit_price'],
                    'is_owner' => $isOwner,
                    'is_available' => $isAvailable
                ]);
            }

            return redirect()->route('unit.index')
                ->with('success', count($request->units) . ' units created successfully.');
        } catch (\Throwable $th) {
            Log::error($th);
            return back()
                ->with('error', 'Failed to create units: ' . $th->getMessage())
                ->withInput()
                ->with('current_step', $request->input('current_step', 1));
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

        // $unit = Unit::fin
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
                'is_available' => $request->is_available,
                'is_owner' => $request->is_owner == 1 ? 'yes' : 'no'
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
