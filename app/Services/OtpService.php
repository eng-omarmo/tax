<?php

namespace App\Services;

use App\Models\Otp;
use App\Models\User;
use App\Services\SmsService;
use Illuminate\Support\Facades\Log;

class OtpService
{
    public function generate(User $user)
    {
        //  $otp = rand(100000, 999999);
        $otp = 123456;
        $expiresAt = now()->addMinutes(5);
        $model = Otp::where('user_id', $user->id)->first();

        if ($model) {
            $model->update([
                'otp' => $otp,
                'expires_at' => $expiresAt
            ]);
        } else {
            Otp::create([
                'user_id' => $user->id,
                'otp' => $otp,
                'expires_at' => $expiresAt
            ]);
        }
        //call sms service
        $smsService = new SmsService();
        $message = "Lambarkaaga xaqiijinta (OTP) waa: {{ $otp }}.
Fadlan geli nambarkan si aad u xaqiijiso aqoonsigaaga. Nambarkani wuu dhacayaa kaddib dhowr daqiiqo. Ha la wadaagin cid kale";
        $response = $smsService->send('hormuud', $user->phone, $message);
        Log::info($response);

        return true;
    }
    public function verifyOtp(User $user, $otp)
    {
        $otpRecord = Otp::where('user_id', $user->id)->where('otp', $otp)->first();

        if (!$otpRecord || $otpRecord->expires_at < now()) {
            return false;
        }
        return true;
    }
    public function  getOtpUser($otp)
    {
        $otpRecord = Otp::where('otp', $otp)->first();
        if (!$otpRecord) {
            return false;
        }

        return $otpRecord->user;
    }
}
