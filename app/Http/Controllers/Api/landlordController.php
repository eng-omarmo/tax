<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Landlord;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class landlordController extends Controller
{
    use ApiResponseTrait;


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:15|unique:users,phone|unique:landlords,phone_number',
            'email' => 'required|email|unique:users,email|max:255',
        ]);

        if ($validator->fails()) {
            return $this->badRequestResponse($validator->errors()->first());
        }

        $check = Landlord::where('email', $request->email)->orWhere('phone_number', $request->phone)->first();
        if ($check) {
            return $this->okResponse('User already exists.');
        }

        try {
            $landlord = Landlord::create([
                'name' => $request->name,
                'address' => $request->address,
                'phone_number' => $request->phone,
                'email' => $request->email,
                'user_id' => $request->user()->id,
            ]);
            return $this->successResponse($landlord, 'Landlord created successfully.');
        } catch (\Exception $e) {
            $this->unprocessableResponse($e->getMessage());
        }
    }
}
