<?php

namespace App\Http\Controllers;

use App\Models\Otp;
use App\Models\User;

use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OtpController extends Controller
{



    public function index()
    {
        return view('otp.index');
    }

    public function verifyOtp(Request $request)
    {

        try {
            $request->validate([
                'otp' => 'required'
            ]);
            $otpService = new OtpService();
            $user = $otpService->getOtpUser($request->otp);
            if (!$user) {
                return redirect()->back()->with('error', 'Invalid OTP.');
            }
            $otpDetails = $otpService->verifyOtp($user, $request->otp);
            if (!$otpDetails) {
                return redirect()->back()->with('error', 'Invalid OTP.');
            }
            Auth::login($user);
            return redirect()->route('index')->with('success', 'OTP verified successfully.');
        } catch (\Throwable $th) {
            return redirect()->route('signin')->with('error', $th->getMessage());
        }
    }
}
