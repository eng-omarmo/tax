<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PropertyInvoiceSummaryMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invoices;
    public $invoice; // Add this for backward compatibility

    public function __construct($invoices)
    {
        $this->invoices = $invoices;
        // Set the first invoice as the default invoice for backward compatibility
        $this->invoice = $invoices->first();
    }

    public function build()
    {
        return $this->view('email.property.invoice_summary')
                    ->subject('Property Invoice Summary');
    }
}
