<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Services\TaxCalculationService;

class PropertyInvoiceSummaryMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invoices;
    public $invoice;
    public $taxCalculator;
    public $totalTax;

    /**
     * Create a new message instance.
     *
     * @param  \Illuminate\Support\Collection  $invoices
     * @return void
     */
    public function __construct($invoices)
    {
        $this->invoices = $invoices;
        $this->invoice = $invoices->first();
        $this->taxCalculator = new TaxCalculationService();

        // Pre-calculate total tax
        $this->totalTax = $this->calculateTotalTax();
    }

    /**
     * Calculate the total tax for all invoices
     *
     * @return float
     */
    protected function calculateTotalTax()
    {
        $total = 0;
        foreach ($this->invoices as $invoice) {
            $total += $this->taxCalculator->calculatePropertyTax($invoice->unit->unit_price);
        }
        return $total;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Property Tax Invoice Summary')
                    ->view('email.property.invoice_summary')
                    ->with([
                        'taxCalculator' => $this->taxCalculator,
                        'totalTax' => $this->totalTax
                    ]);
    }
}
