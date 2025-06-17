<?php

namespace App\Http\Controllers\api;

use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;

class OtpController extends Controller
{
    use  ApiResponseTrait;
    protected OtpService $otpService;
    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    public function send(Request $request)
    {
        $validator = validator($request->all(), [
            'recipient' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->badRequestResponse(null, $validator->errors()->first());
        }
        $user = User::where(['phone' => $request->recipient])->first();

        if (!$user) {
            return $this->okResponse(null, 'User not found');
        }
        $response = $this->otpService->generate($user);
        if (!$response) {
            return $this->okResponse(null, 'Otp not sent');
        }
        return $this->okResponse(null, 'Otp sent successfully');
    }

    public function verify(Request $request)
    {
        $validator = validator($request->all(), [
            'otp' => 'required',
            'recipient' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->badRequestResponse(null, $validator->errors()->first());
        }
        $user = User::where(['phone' => $request->recipient])->first();
        if (!$user) {
            return $this->okResponse(null, 'User not found');
        }
        $response = $this->otpService->verifyOtp($user, $request->otp);
        if (!$response) {
            return $this->okResponse(null, 'Otp not verified');
        }
        return $this->okResponse(null, 'Otp verified successfully');
    }
}
