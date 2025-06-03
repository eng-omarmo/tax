<?php

namespace App\Services;

use App\Models\Sms;
use Illuminate\Support\Facades\Log;

class SMSService
{
    /**
     * Get SMS settings from the database or use default config
     *
     * @param string $gateway The gateway name to retrieve settings for
     * @return array|null The settings array or null if not found
     */
    private static function get_settings(string $gateway): ?array
    {
        // Try to get settings from database
        $settings = Sms::where('gateway', $gateway)->first();

        // If settings exist in database, return as array
        if ($settings) {
            return $settings->toArray();
        }

        // If no settings in database, use default config from Sms model
        if ($gateway === 'hormuud_sms' && isset(Sms::$defaultConfig)) {
            return Sms::$defaultConfig;
        }

        return null;
    }

    public static function hormuud_sms(string $receiver, string $otp, ?string $message = null): string
    {
        $config = self::get_settings('hormuud_sms');

        if (!isset($config) || $config['status'] !== 1) {
            return 'error: invalid configuration';
        }

        try {
            if ($message === null) {
                // If no custom message, use template from config or default
                $template = $config['otp_template'] ?? 'Your OTP is';
                $message = $template . " " . $otp;
            }
            $receiver = str_replace("+252", "", $receiver);

            $accessToken = self::getHormuudToken($config);
            if (str_starts_with($accessToken, 'error:')) {
                return $accessToken;
            }

            return self::sendHormuudSMS($receiver, $message, $accessToken);
        } catch (\Exception $e) {
            return 'error: ' . $e->getMessage();
        }
    }

    private static function getHormuudToken(array $config): string
    {
        $tokenPostFields = [
            'username' => $config['username'],
            'password' => $config['password'] ?? '',
            'grant_type' => $config['grant_type'] ?? 'password',
        ];

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://smsapi.hormuud.com/token',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($tokenPostFields),
            CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);

        if ($error) {
            // Consider logging the actual error
            Log::error('SMS API error: ' . $error);
            return 'error: token request failed';
        }

        $tokenData = json_decode($response, true);
        return $tokenData['access_token'] ?? 'error: invalid token response';
    }

    private static function sendHormuudSMS(string $receiver, string $message, string $accessToken): string
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://smsapi.hormuud.com/api/SendSMS',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode([
                'mobile' => $receiver,
                'message' => $message,
            ]),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $accessToken,
            ],
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);

        if ($error) {
            return 'error: sms request failed';
        }

        $result = json_decode($response, true);
        return (isset($result['ResponseMessage']) && $result['ResponseMessage'] === 'SUCCESS!.')
            ? 'success'
            : 'error: ' . ($result['message'] ?? 'unknown error');
    }
}
