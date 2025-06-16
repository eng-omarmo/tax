<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{
    use ApiResponseTrait;

    /**
     * Login user and create token
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string',
                'device_name' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return $this->okResponse($validator->errors()->first(), 422);
            }

            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return $this->okResponse(null, 'The provided credentials are incorrect.');
            }

            if ($user->status !== 'active') {
                return $this->okResponse(null, 'Your account is not active. Please contact administrator.');
            }

            if ($user->tokens()->exists()) {
                $user->tokens()->delete();
            }
            $deviceName = $request->device_name ?? $request->ip();
            $token = $user->createToken($deviceName)->plainTextToken;

            return $this->successResponse(array_merge(
                $user->toArray(),
                ['token' => $token]
            ));
        } catch (\Exception $e) {
            return $this->unprocessableResponse(null, 'An error occurred during login: ' . $e->getMessage());
        }
    }

    /**
     * Logout user (Revoke the token)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        try {
            if ($request->user()) {
                $request->user()->tokens()->delete();
                return $this->successResponse(null, 200, 'Successfully logged out');
            }
            return $this->unauthorizedResponse(null, 'user is not authenticated');
        } catch (\Exception $e) {
            return $this->unprocessableResponse('An error occurred during logout: ' . $e->getMessage());
        }
    }

    /**
     * Get the authenticated User
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function user(Request $request)
    {
        try {

            if (!$request->user()) {
                return $this->unauthorizedResponse(null, 'user is not authenticated');
            }

            return $this->okResponse($request->user(), 'User retrieved successfully');
        } catch (\Exception $e) {
            return $this->unprocessableResponse('An error occurred: ' . $e->getMessage());
        }
    }
}
