<?php

namespace App\Services;

use App\Models\SmsConfig;

class SmsService
{
    public static function get_settings($provider)
    {
        $settings = SmsConfig::where('provider', $provider)->first();
        return $settings? $settings->value : null;
    }

public static function hormuud_sms($receiver, $otp, $message = null): string
{
    $config = self::get_settings('hormuud');
    $response = 'error';
    if (isset($config) && $config['status'] == 'Active') {
        if($message ==  null){
            $message = $config['otp_template'] . " " . $otp  ?? 'Your OTP is #OTP#';
        }

        $receiver = str_replace("+252", "", $receiver);
        $header =[
            'Content-Type: application/x-www-form-urlencoded'
            ];

        // Step 1: Get token
        $tokenPostFields = [
            'username'    => $config['username'],
            'password'    => $config['password'] ?? '',
            'grant_type'  => $config['grant_type'] ?? 'password',
        ];

        $tokenCurl = curl_init();
        curl_setopt_array($tokenCurl, [
            CURLOPT_URL => 'https://smsapi.hormuud.com/token',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($tokenPostFields),
            CURLOPT_HTTPHEADER => $header,
        ]);

        $tokenResponse = curl_exec($tokenCurl);
        $tokenError = curl_error($tokenCurl);
        curl_close($tokenCurl);

        if ($tokenError) {
            return 'error: token request failed';
        }

        $tokenData = json_decode($tokenResponse, true);
        $accessToken = $tokenData['access_token'] ?? null;

        if (!$accessToken) {
            return 'error: invalid token';
        }

        // Step 2: Send SMS
        $smsPayload = json_encode([
            'mobile' => $receiver,
            'message' => $message,
        ]);

        $smsHeaders = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $accessToken,
        ];

        $smsCurl = curl_init();
        curl_setopt_array($smsCurl, [
            CURLOPT_URL => 'https://smsapi.hormuud.com/api/SendSMS',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $smsPayload,
            CURLOPT_HTTPHEADER => $smsHeaders,
        ]);

        $smsResponse = curl_exec($smsCurl);
        $smsError = curl_error($smsCurl);
        curl_close($smsCurl);

        if ($smsError) {
            return 'error: sms request failed';
        }

        $smsResult = json_decode($smsResponse, true);
        if (isset($smsResult['ResponseMessage']) && $smsResult['ResponseMessage'] === 'SUCCESS!.') {
            $response = 'success';
        } else {
            $response = 'error: ' . ($smsResult['message'] ?? 'unknown error');
        }
    }

    return $response;
}

}
