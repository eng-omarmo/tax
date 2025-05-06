<?php

namespace App\Http\Controllers;

use App\Models\Otp;
use App\Models\User;

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
            $otpDetails= Otp::where('otp', $request->otp)->first();
            if (!$otpDetails) {
                return redirect()->route('signin')->with('error', 'Invalid OTP.');
            }

            if($otpDetails->expires_at < now()){
                return redirect()->route('signin')->with('error', 'OTP has expired.');
            }
            if($otpDetails->otp != $request->otp){
                return redirect()->route('signin')->with('error', 'Invalid OTP.');
            }


            $otpDetails->delete();
            $user = User::find($otpDetails->user_id);
            if(!$user){
                return redirect()->route('signin')->with('error', 'User not found.');
            }
            Auth::login($user);
            return redirect()->route('index')->with('success', 'OTP verified successfully.');
        } catch (\Throwable $th) {
            return redirect()->route('signin')->with('error', $th->getMessage());
        }
    }
}
