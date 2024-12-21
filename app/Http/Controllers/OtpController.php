<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OtpController extends Controller
{


    public function verifyOtp(Request $request)
    {
        try {
            $request->validate([
                'otp' => 'required'
            ]);

            $otp = $request->input('otp');
            $user = User::where('otp', $otp)->first();

            if (!$user || $user->otp != $otp || $user->otp_expires_at < now()) {
                return response()->json(['error' => 'Invalid OTP or expired'], 401);
            }

            $user->otp = null;
            return response()->json(['success' => 'OTP verified successfully']);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }
}
