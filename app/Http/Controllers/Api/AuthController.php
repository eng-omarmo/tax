<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Jenssegers\Agent\Agent;

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
                'device_id' => 'nullable|string',
                'fcm_token' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return $this->okResponse(null, $validator->errors()->first());
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
            $this->trackLoginActivity($user, $request);
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
            $user = $request->user();
            $user->currentAccessToken()->delete();
            return $this->okResponse(null, 'Logged out successfully');
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
            return $this->okResponse($request->user(), 'User retrieved successfully');
        } catch (\Exception $e) {
            return $this->unprocessableResponse('An error occurred: ' . $e->getMessage());
        }
    }




    public function trackLoginActivity(User $user, Request $request)
    {
        $agent = new Agent();
        $device = $agent->device();
        $platform = $agent->platform();
        $browser = $agent->browser();
        $user->loginActivities()->create([
            'user_id' => $user->id,
            'ip_address' => $request->ip(),
            'device' => $device,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'device_id' => $request->device_id,
            'logged_in_at' => now(),
            'fcm_token' => $request->fcm_token,
            'device' =>  $platform . '' . $agent->platform(),

        ]);
    }
}
