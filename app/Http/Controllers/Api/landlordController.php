<?php

namespace App\Http\Controllers\Api;

use App\Models\Landlord;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class LandlordController extends Controller
{
    use ApiResponseTrait;

    /**
     * Store a newly created landlord
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => [
                'required',
                'string',
                'regex:/^([0-9\s\-\+\(\)]*)$/',
                'min:10',
                'max:15',
                Rule::unique('landlords', 'phone_number'),
                Rule::unique('users', 'phone'),
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('landlords', 'email'),
                Rule::unique('users', 'email'),
            ],
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return $this->badRequestResponse(null, $validator->errors()->first());
        }

        if (Landlord::where('email', $request->email)
            ->orWhere('phone_number', $request->phone)
            ->exists()
        ) {
            return $this->conflictResponse(null, 'Landlord already exists.');
        }
        $image = $request->file('image');
        $imageName = time() . '.' . $image->getClientOriginalExtension();
        $path = $image->storeAs('uploads', $imageName, 'public');
        try {
            $landlord = Landlord::create([
                'name' => $request->name,
                'address' => $request->address,
                'phone_number' => $request->phone,
                'email' => $request->email,
                'user_id' => $request->user()->id,
                'profile_image' => $path,
            ]);

            return $this->createdResponse($landlord, 'Landlord created successfully.');
        } catch (\Exception $e) {
            return $this->unprocessableResponse($e->getMessage());
        }
    }

    /**
     * Display the landlord profile
     */
    public function show($id): JsonResponse
    {
        try {
            $landlord = Landlord::find($id);
            if (!$landlord) {
                return $this->notFoundResponse(null, 'Landlord  not found.');
            }
            return $this->okResponse($landlord, 'Landlord retrieved successfully.');
        } catch (\Exception $e) {
            return $this->unprocessableResponse($e->getMessage());
        }
    }

    /**
     * Update the landlord profile
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $landlord = Landlord::find($id);
            if (!$landlord) {
                return $this->notFoundResponse(null, 'Landlord not found.');
            }

            $updateRules = [
                'name' => 'required|string|max:255',
                'address' => 'required|string|max:255',
                'phone' => [
                    'required',
                    'string',
                    'regex:/^([0-9\s\-\+\(\)]*)$/',
                    'min:10',
                    'max:15',
                    Rule::unique('landlords', 'phone_number')->ignore($landlord->id),
                    Rule::unique('users', 'phone'),
                ],
                'email' => [
                    'required',
                    'email',
                    'max:255',
                    Rule::unique('landlords', 'email')->ignore($landlord->id),
                    Rule::unique('users', 'email'),
                ],
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ];

            $validator = Validator::make($request->all(), $updateRules);
            if ($validator->fails()) {
                return $this->badRequestResponse(null, $validator->errors()->first());
            }

            // Handle new image upload if provided
            $imagePath = $landlord->profile_image;

            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath);
                }

                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('uploads', $imageName, 'public');
            }

            $landlord->update([
                'name' => $request->name,
                'address' => $request->address,
                'phone_number' => $request->phone,
                'email' => $request->email,
                'profile_image' => $imagePath,
            ]);

            return $this->okResponse($landlord, 'Landlord updated successfully.');
        } catch (\Exception $e) {
            return $this->unprocessableResponse($e->getMessage());
        }
    }


    /**
     * Remove the landlord profile
     */
    public function destroy(Request $request): JsonResponse
    {
        try {
            $landlord = Landlord::where('user_id', $request->user()->id)->first();
            if (!$landlord) {
                return $this->notFoundResponse('Landlord profile not found.');
            }
            $landlord->delete();
            return $this->okResponse(null, 'Landlord deleted.');
        } catch (\Exception $e) {
            return $this->unprocessableResponse($e->getMessage());
        }
    }
}
