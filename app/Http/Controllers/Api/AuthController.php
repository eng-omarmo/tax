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

            // Validate the request
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string',
                'device_name' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->first(), 422);
            }

            // Find the user by email
            $user = User::where('email', $request->email)->first();

            // Check if user exists and password is correct
            if (!$user || !Hash::check($request->password, $user->password)) {
                return $this->errorResponse('The provided credentials are incorrect.', 401);
            }

            // Check if user is active
            if ($user->status !== 'active') {
                return $this->errorResponse('Your account is not active. Please contact administrator.', 403);
            }

            // Create token
            $deviceName = $request->device_name ?? $request->ip();
            $token = $user->createToken($deviceName)->plainTextToken;

            // Return user data and token
            return $this->successResponse([
                $user,
                'token' => $token,
                'token_type' => 'Bearer',
            ], 200, 'Login successful');
        } catch (\Exception $e) {
            return $this->errorResponse('An error occurred during login: ' . $e->getMessage(), 500);
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

            // Revoke all tokens...
            $request->user()->tokens()->delete();
            return $this->successResponse(null, 200, 'Successfully logged out');
        } catch (\Exception $e) {
            return $this->errorResponse('An error occurred during logout: ' . $e->getMessage(), 500);
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
            return $this->successResponse([
                'user' => $request->user(),
            ], 200, 'User retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('An error occurred: ' . $e->getMessage(), 500);
        }
    }
}
