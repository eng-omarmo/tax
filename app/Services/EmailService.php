<?php

namespace App\Services;

use App\Mail\PropertyInvoiceSummaryMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailService
{
    /**
     * Send a simple user notification email.
     */


    /**
     * Send a property invoice summary email.
     */
    public function sendPropertyInvoiceSummary(string $to, $invoices): bool
    {
        try {
            Mail::to($to)->send(new PropertyInvoiceSummaryMail($invoices));
            return true;
        } catch (\Exception $e) {
            Log::error('Invoice summary email failed: ' . $e->getMessage());
            return false;
        }
    }


}
