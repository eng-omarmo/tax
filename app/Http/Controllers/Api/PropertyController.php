<?php

namespace App\Http\Controllers\api;

use App\Models\Property;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class PropertyController extends Controller
{
    use  ApiResponseTrait;


    public function store(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'property_name' => 'required|string|max:255',
                'property_phone' => 'required|string|max:20',
                'house_type' => 'required|string|max:50',
                'branch_id' => 'required|integer',
                'zone' => 'required|string|max:100',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'lanlord_id' => 'required|integer|exists:landlords,id',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
                'document' => 'nullable|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first()
                ], 422);
            }

            // Check for duplicate
            $exists = Property::where('property_name', $request->property_name)
                ->where('property_phone', $request->property_phone)
                ->exists();

            if ($exists) {
                return $this->conflictResponse(null, 'Property exists');
            }

            // Uploads
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imageName = time() . '.' . $request->file('image')->getClientOriginalExtension();
                $imagePath = $request->file('image')->storeAs('uploads', $imageName, 'public');
            }

            $documentPath = null;
            if ($request->hasFile('document')) {
                $documentName = time() . '.' . $request->file('document')->getClientOriginalExtension();
                $documentPath = $request->file('document')->storeAs('uploads', $documentName, 'public');
            }

            // Generate house code
            $code = 'HOUSE-' . strtoupper(Str::random(3)) . '-' . rand(100, 999);

            $monitoringStatus = $request->boolean('skip_monitoring') ? 'Approved' : 'Pending';
            $status = $monitoringStatus === 'Approved' ? 'Active' : 'InActive';

            $property = Property::create([
                'property_name' => $request->property_name,
                'property_phone' => $request->property_phone,
                'house_code' => $code,
                'branch_id' => $request->branch_id,
                'zone' => $request->zone,
                'house_type' => $request->house_type,
                'house_rent' => $request->house_rent,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'monitoring_status' => $monitoringStatus,
                'status' => $status,
                'district_id' => $request->user()->district->id,
                'landlord_id' => $request->lanlord_id,
                'image' => $imagePath,
                'document' => $documentPath,
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Property created successfully.',
                'data' => $property
            ], 201);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong.',
                'error' => $th->getMessage()
            ], 500);
        }
    }
}
