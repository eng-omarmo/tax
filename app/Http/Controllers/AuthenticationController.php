<?php

namespace App\Http\Controllers;

use App\Models\Otp;
use App\Models\User;
use App\Services\OtpService;
use App\Services\SmsService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\MainSmsService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class AuthenticationController extends Controller
{
    public function forgotPassword()
    {
        return view('authentication/forgotPassword');
    }

    /**
     * Display the sign-in view.
     *
     * @return \Illuminate\Contracts\View\View
     */

    public function signIn()
    {
        return view('authentication.signin');
    }

    // public function signUp()
    // {
    //     return view('authentication.signUp');
    // }

    public function login(Request $request)
    {
        // Validate the input
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {

            RateLimiter::hit($this->throttleKey($request));

            return back()->with('error', 'The provided credentials are incorrect.');
        }
        RateLimiter::clear($this->throttleKey($request));

        $otpService = new OtpService();
        $otp  = $otpService->generate($user);
        if (!$otp) {
            return back()->with('error', 'Failed to generate OTP.');
        }
        session()->put('user', $user);
        $phone = $user->phone;

        $maskedPhone = substr_replace($phone, str_repeat('*', 4), 3, 4);
        return redirect()->route('otp.index')->with('success', 'OTP sent successfully to your ' . $maskedPhone . '. Please check.');
    }

    /**
     * Ensure the login attempts are not rate-limited.
     *
     * @throws ValidationException
     */
    protected function ensureIsNotRateLimited(Request $request)
    {
        if (RateLimiter::tooManyAttempts($this->throttleKey($request), 5)) {
            throw ValidationException::withMessages([
                'email' => 'Too many login attempts. Please try again in ' .
                    RateLimiter::availableIn($this->throttleKey($request)) . ' seconds.',
            ]);
        }
    }

    /**
     * Get the rate-limiter throttle key.
     */
    protected function throttleKey(Request $request): string
    {
        return strtolower($request->email) . '|' . $request->ip();
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('signin');
    }



}
