<?php

namespace App\Services;

use App\Models\Sms;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class SMSService
{
    private const HORMUUD_TOKEN_URL = 'https://smsapi.hormuud.com/token';
    private const HORMUUD_SEND_SMS_URL = 'https://smsapi.hormuud.com/api/SendSMS';
    private const TOKEN_CACHE_KEY = 'hormuud_sms_token';
    private const TOKEN_CACHE_TTL = 3500; // Just under 1 hour (typical token expiry)

    /**
     * Send SMS via specified gateway
     *
     * @param string $gateway
     * @param string $receiver
     * @param string $message
     * @return array ['success' => bool, 'message' => string, 'data' => mixed]
     */
    public static function send(string $gateway, string $receiver, string $message): array
    {
        try {
            $method = strtolower($gateway) . '_sms';

            if (!method_exists(__CLASS__, $method)) {
                return self::formatResponse(false, "Unsupported gateway: {$gateway}");
            }

            return call_user_func([__CLASS__, $method], $receiver, $message);
        } catch (\Exception $e) {
            Log::error("SMS sending failed: " . $e->getMessage(), [
                'gateway' => $gateway,
                'receiver' => $receiver,
                'exception' => $e
            ]);
            return self::formatResponse(false, $e->getMessage());
        }
    }

    /**
     * Hormuud SMS Gateway Implementation
     */
    protected static function hormuud_sms(string $receiver, string $message): array
    {
        $config = self::getGatewayConfig('hormuud_sms');

        if (!$config || $config['status'] !== 1) {
            return self::formatResponse(false, 'Gateway is not configured or disabled');
        }

        $receiver = self::normalizePhoneNumber($receiver, '+252');

        $tokenResponse = self::getHormuudToken($config);
        if (!$tokenResponse['success']) {
            return $tokenResponse;
        }

        return self::sendHormuudSMS($receiver, $message, $tokenResponse['data']);
    }

    /**
     * Get gateway configuration
     */
    private static function getGatewayConfig(string $gateway): ?array
    {
        return Sms::where('gateway', $gateway)->first()?->toArray()
            ?? (($gateway === 'hormuud_sms' && isset(Sms::$defaultConfig))
                ? Sms::$defaultConfig
                : null);
    }

    /**
     * Get authentication token
     */
    private static function getHormuudToken(array $config): array
    {
        // Check cache first
        if (Cache::has(self::TOKEN_CACHE_KEY)) {
            return self::formatResponse(true, 'Token retrieved from cache', Cache::get(self::TOKEN_CACHE_KEY));
        }

        $response = self::makeHttpRequest(
            self::HORMUUD_TOKEN_URL,
            'POST',
            [
                'username' => $config['username'],
                'password' => $config['password'] ?? '',
                'grant_type' => $config['grant_type'] ?? 'password',
            ],
            ['Content-Type: application/x-www-form-urlencoded'],
            true
        );

        if (!$response['success']) {
            return $response;
        }

        $token = $response['data']['access_token'] ?? null;
        if (!$token) {
            return self::formatResponse(false, 'Invalid token response');
        }

        Cache::put(self::TOKEN_CACHE_KEY, $token, self::TOKEN_CACHE_TTL);
        return self::formatResponse(true, 'Token retrieved successfully', $token);
    }

    /**
     * Send SMS via Hormuud API
     */
    private static function sendHormuudSMS(string $receiver, string $message, string $accessToken): array
    {
        $response = self::makeHttpRequest(
            self::HORMUUD_SEND_SMS_URL,
            'POST',
            [
                'mobile' => $receiver,
                'message' => $message,
            ],
            [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $accessToken,
            ]
        );

        if (!$response['success']) {
            return $response;
        }

        // Enhanced response parsing
        $responseData = $response['data'] ?? [];

        if (isset($responseData['ResponseMessage']) && $responseData['ResponseMessage'] === 'SUCCESS!.') {
            return self::formatResponse(true, 'SMS sent successfully');
        }

        // More detailed error messages
        $errorMessage = 'Unknown error';

        if (isset($responseData['message'])) {
            $errorMessage = $responseData['message'];
        } elseif (isset($responseData['ResponseMessage'])) {
            $errorMessage = $responseData['ResponseMessage'];
        } elseif (isset($responseData['errors'])) {
            $errorMessage = is_array($responseData['errors'])
                ? implode(', ', $responseData['errors'])
                : $responseData['errors'];
        }

        // Log full response for debugging
        Log::error('Hormuud SMS API Error Response', [
            'receiver' => $receiver,
            'response' => $responseData,
            'full_response' => $response
        ]);

        return self::formatResponse(false, $errorMessage, $responseData);
    }
    /**
     * Normalize phone number format
     */
    private static function normalizePhoneNumber(string $phone, string $countryCode): string
    {
        return str_replace($countryCode, '', $phone);
    }

    /**
     * Make HTTP request with common error handling
     */
    private static function makeHttpRequest(
        string $url,
        string $method,
        array $data,
        array $headers = [],
        bool $isFormUrlEncoded = false
    ): array {
        try {
            $curl = curl_init();
            $options = [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => strtoupper($method),
                CURLOPT_HTTPHEADER => $headers,
            ];

            if ($method === 'POST') {
                $options[CURLOPT_POSTFIELDS] = $isFormUrlEncoded
                    ? http_build_query($data)
                    : json_encode($data);
            }

            curl_setopt_array($curl, $options);

            $response = curl_exec($curl);
            $error = curl_error($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            if ($error) {
                throw new \Exception("CURL error: {$error}");
            }

            $decodedResponse = json_decode($response, true) ?? $response;

            if ($httpCode >= 400) {
                throw new \Exception("API request failed with HTTP {$httpCode}");
            }

            return self::formatResponse(true, 'Request successful', $decodedResponse);
        } catch (\Exception $e) {
            Log::error("HTTP request failed: " . $e->getMessage(), [
                'url' => $url,
                'method' => $method,
                'exception' => $e
            ]);
            return self::formatResponse(false, $e->getMessage());
        }
    }

    /**
     * Standardized response format
     */
    private static function formatResponse(bool $success, string $message, $data = null): array
    {
        return [
            'success' => $success,
            'message' => $message,
            'data' => $data
        ];
    }

    /**
     * Send a custom SMS message via Hormuud
     *
     * @param string $receiver Recipient phone number
     * @param string $message Custom message content
     * @return array Response with success status and message
     */
    public function sendCustomSms(string $receiver, string $message): array
    {
        try {
            $config = self::getGatewayConfig('hormuud_sms');

            if (!$config || $config['status'] !== 1) {
                return self::formatResponse(false, 'Gateway is not configured or disabled');
            }

            $receiver = self::normalizePhoneNumber($receiver, '+252');

            $tokenResponse = self::getHormuudToken($config);
            if (!$tokenResponse['success']) {
                return $tokenResponse;
            }

            return self::sendHormuudSMS($receiver, $message, $tokenResponse['data']);
        } catch (\Exception $e) {
            Log::error("Custom SMS sending failed: " . $e->getMessage(), [
                'receiver' => $receiver,
                'exception' => $e
            ]);
            return self::formatResponse(false, $e->getMessage());
        }
    }
}
