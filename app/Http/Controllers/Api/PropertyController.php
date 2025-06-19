<?php

namespace App\Http\Controllers\Api;

use Dotenv\Validator;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class PropertyController extends Controller
{


    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $validor = Validator($request->all(),[
                'property_name' => 'required',
                'property_phone' => 'required',
                'house_type' => 'required',
                'branch_id' => 'required',
                'district_id' => 'required',
                'zone' => 'required',
                'latitude' => 'required',
                'longitude' => 'required',
                'lanlord_id' => 'required',
                'image' => 'image|mimes:jpeg,png,jpg,webp|max:2048', // Validate file type & size
            ]);
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $document = $request->file('document');
            $documentName = time() . '.' . $document->getClientOriginalExtension();

            $path = $image->storeAs('uploads', $imageName, 'public');
            $documentPath = $document->storeAs('uploads', $documentName, 'public');

            $request->merge(['image' => $imageName]);
            $properties = Property::where('property_name', $request->property_name)
                ->where('property_phone', $request->property_phone)
                ->first();

            if ($properties) {
                return back()->with('error', 'Property name and phone already exists.');
            }
            $code = 'HOUSE-' . strtoupper(Str::random(3)) . '-' . rand(100, 999);

            $request->merge(['house_code' => $code]);

            // Change default monitoring status based on configuration or request
            $monitoringStatus = $request->has('skip_monitoring') && $request->skip_monitoring == 1 ? 'Approved' : 'Pending';
            $status = $monitoringStatus == 'Approved' ? 'Active' : 'InActive';

            $property = Property::create([
                'property_name' => $request->property_name,
                'property_phone' => $request->property_phone,
                'house_code' => $request->house_code,
                'branch_id' => $request->branch_id,
                'zone' => $request->zone,
                'house_type' => $request->house_type,
                'house_rent' => $request->house_rent,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'monitoring_status' => $monitoringStatus,
                'status' => $status,
                'district_id' => $request->district_id,
                'landlord_id' => $request->lanlord_id,
                'document'  => $documentPath,
                'image' => $path
            ]);

            DB::commit();


        } catch (\Throwable $th) {
            DB::rollBack();

        }
    }


}
