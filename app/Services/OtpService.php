<?php

namespace App\Services;

use App\Models\Otp;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class OtpService
{
    public function generate(User $user)
    {
      //  $otp = rand(100000, 999999);
        $otp = 123456;
        $expiresAt = now()->addMinutes(5);

        Otp::updateOrCreate([
            'user_id' => $user->id,
            'otp' => $otp,
            'expires_at' => $expiresAt
        ]);
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
