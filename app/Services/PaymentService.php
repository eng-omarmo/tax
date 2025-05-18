<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    protected $config;

    public function __construct()
    {
        $this->config = config('somx');
    }

    public function getAccessToken()
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($this->config['endpoints']['verify'], [
                'client_id' => $this->config['credentials']['client_id'],
                'client_secret' => $this->config['credentials']['client_secret']
            ]);

            if ($response->failed()) {
                Log::error('Payment API Auth Failed', ['response' => $response->json()]);
                throw new \Exception('Payment API authentication failed');
            }

            return $response->json()['access_token'];

        } catch (\Exception $e) {
            Log::error('Payment Service Error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function createTransaction(array $transactionData)
    {
        try {
            $token = $this->getAccessToken();

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ])->post($this->config['endpoints']['transaction'], $transactionData);

            if ($response->failed()) {
                Log::error('Transaction Creation Failed', ['response' => $response->json()]);
                throw new \Exception('Transaction creation failed');
            }

            return $response->json();

        } catch (\Exception $e) {
            Log::error('Transaction Error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}
