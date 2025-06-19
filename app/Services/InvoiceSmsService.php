<?php

namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;

class InvoiceSmsService
{
    /**
     * Send a property invoice summary SMS.
     *
     * @param string $phone The recipient's phone number
     * @param Collection $invoices Collection of invoices
     * @return bool Success status
     */
    public function sendPropertyInvoiceSummary(string $phone, Collection $invoices): bool
    {
        try {
            $message = $this->generateInvoiceSummaryMessage($invoices);

            if (!$message) {
                Log::warning("Invoice SMS message could not be generated.");
                return false;
            }

            Log::info("Sending property invoice SMS to {$phone}", [
                'length' => strlen($message),
                'message' => $message
            ]);

            $smsService = new SMSService();
            $result = $smsService->sendCustomSms($phone, $message);

            return $result['success'] ?? false;
        } catch (\Exception $e) {
            Log::error('Invoice summary SMS failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return false;
        }
    }

    /**
     * Generate the invoice summary SMS message.
     *
     * @param Collection $invoices
     * @return string|null
     */
    private function generateInvoiceSummaryMessage(Collection $invoices): ?string
    {
        if ($invoices->isEmpty()) {
            return null;
        }

        $property = $invoices->first()?->unit?->property;
        if (!$property) {
            return null;
        }

        $now = now();
        $quarterStart = $now->copy()->startOfQuarter()->format('M d');
        $quarterEnd = $now->copy()->endOfQuarter()->format('M d, Y');

        $totalTax = $invoices->sum('amount');


        $id = Crypt::encrypt($property->id);
        // Create a more concise message
        $message = "Invoice: {$property->property_name}\n";
        $message .= "xilga: {$quarterStart}-{$quarterEnd}\n";
        $message .= "Albaaabada: " . $invoices->count() . "\n";
        $message .= "Total Due: $" . number_format($totalTax, 2) . "\n";
        $message .= "Pay: https://tax.somxchange.dev/self-payment/{$id}";

        return Str::limit($message, 160);
    }
}
