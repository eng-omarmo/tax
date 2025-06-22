<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Unit;
use App\Models\Property;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Validator;

class UnitController extends Controller
{
    use ApiResponseTrait;

    /**
     * Store a newly created unit in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'property_id' => 'required|exists:properties,id',
                'unit_name' => 'required|string|max:255',
                'unit_number' => 'nullable|string|max:255',
                'unit_type' => 'required|in:Flat,Section,Office,Shop,Other',
                'unit_price' => 'nullable|numeric',
                'is_owner' => 'required|in:yes,no',
                'is_available' => 'boolean',
            ]);

            if ($validator->fails()) {
                return $this->unprocessableResponse($validator->errors(), 'Validation failed');
            }

            // Check if property exists
            $property = Property::find($request->property_id);
            if (!$property) {
                return $this->notFoundResponse(null, 'Property not found');
            }

            // Check if unit with same name already exists in the property
            $existingUnit = Unit::where('property_id', $request->property_id)
                ->where('unit_name', $request->unit_name)
                ->first();

            if ($existingUnit) {
                return $this->conflictResponse(null, 'Unit with this name already exists in this property');
            }

            // Create the unit
            $unit = Unit::create([
                'property_id' => $request->property_id,
                'unit_name' => $request->unit_name,
                'unit_number' => $request->unit_number,
                'unit_type' => $request->unit_type,
                'unit_price' => $request->unit_price,
                'is_owner' => $request->is_owner,
                'is_available' => $request->has('is_available') ? $request->is_available : true,
            ]);

            return $this->createdResponse($unit, 'Unit created successfully');
        } catch (\Exception $e) {
            return $this->unprocessableResponse(null,'Failed to create unit: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified unit.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $unit = Unit::with(['property', 'currentRent'])->find($id);

            if (!$unit) {
                return $this->notFoundResponse(null, 'Unit not found');
            }

            return $this->okResponse($unit, 'Unit retrieved successfully');
        } catch (\Exception $e) {
            return $this->unprocessableResponse(null,'Failed to retrieve unit: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified unit in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            // Find the unit
            $unit = Unit::find($id);

            if (!$unit) {
                return $this->notFoundResponse(null, 'Unit not found');
            }

            // Validate the request
            $validator = Validator::make($request->all(), [
                'property_id' => 'exists:properties,id',
                'unit_name' => 'string|max:255',
                'unit_number' => 'nullable|string|max:255',
                'unit_type' => 'in:Flat,Section,Office,Shop,Other',
                'unit_price' => 'nullable|numeric',
                'is_owner' => 'in:yes,no',
                'is_available' => 'boolean',
            ]);

            if ($validator->fails()) {
                return $this->unprocessableResponse($validator->errors(), 'Validation failed');
            }

            // Check for duplicate unit name if name is being changed
            if ($request->has('unit_name') && $request->unit_name !== $unit->unit_name) {
                $existingUnit = Unit::where('property_id', $request->property_id ?? $unit->property_id)
                    ->where('unit_name', $request->unit_name)
                    ->where('id', '!=', $id)
                    ->first();

                if ($existingUnit) {
                    return $this->conflictResponse(null, 'Unit with this name already exists in this property');
                }
            }

            // Update the unit
            $unit->update($request->all());

            return $this->okResponse($unit, 'Unit updated successfully');
        } catch (\Exception $e) {
            return $this->unprocessableResponse(null,'Failed to update unit: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified unit from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
}
