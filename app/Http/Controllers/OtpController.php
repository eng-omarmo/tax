<?php

namespace App\Http\Controllers;

use App\Models\Otp;
use App\Models\User;
use App\Models\LoginActivities;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Agent\Agent;

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
            $user = session()->get('user');
            $otpDetails = $otpService->verifyOtp($user, $request->otp);
            if (!$otpDetails) {
                return redirect()->back()->with('error', 'Invalid OTP.');
            }
            session()->forget('user');
            Auth::login($user);

            // Record login activity
            $agent = new Agent();
            LoginActivities::create([
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'device' => $this->getDeviceType($agent),
                'logged_in_at' => now(),
            ]);

            return redirect()->route('index')->with('success', 'OTP verified successfully.');
        } catch (\Throwable $th) {
            return redirect()->route('signin')->with('error', $th->getMessage());
        }
    }

    /**
     * Get the device type based on the agent.
     *
     * @param  \Jenssegers\Agent\Agent  $agent
     * @return string
     */
    private function getDeviceType(Agent $agent)
    {
        if ($agent->isDesktop()) {
            return 'Desktop';
        } elseif ($agent->isTablet()) {
            return 'Tablet';
        } elseif ($agent->isPhone()) {
            return 'Mobile';
        } else {
            return 'Unknown';
        }
    }
}
