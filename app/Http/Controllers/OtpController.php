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

            $otp = $request->input('otp');
            $otp = Otp::where('otp', $otp)->first();
            if (!$otp || $otp->otp != $otp || $otp->otp_expires_at < now()) {
                return redirect()->route('signin')->with('error', 'Invalid OTP.');
            }
            $otp->delete();
            $user = User::find($otp->user_id);
            Auth::login($user);
            return redirect()->route('dashboard.index2');
        } catch (\Throwable $th) {
            return redirect()->route('signin')->with('error', $th->getMessage());
        }
    }
}
