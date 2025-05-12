<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Services\TaxCalculationService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class PropertyInvoiceSummaryMail extends Mailable
{
    use Queueable, SerializesModels;

    public Collection $invoices;
    public mixed $invoice;
    public TaxCalculationService $taxCalculator;
    public float $totalTax;

    /**
     * Create a new message instance.
     *
     * @param  \Illuminate\Support\Collection  $invoices
     */
    public function __construct(Collection $invoices)
    {
        $this->invoices = $invoices;

        // Safely get the first invoice with proper null checking
        $this->invoice = $invoices->first(function ($invoice) {
            return $invoice && $invoice->unit !== null;
        }) ?? $invoices->first();

        try {
            $this->taxCalculator = app(TaxCalculationService::class);
            $this->totalTax = $this->calculateTotalTax();
        } catch (\Throwable $e) {
            Log::error("Failed to initialize tax calculator: " . $e->getMessage());
            throw $e;
        }
    }

    protected function calculateTotalTax(): float
    {
        $total = 0.0;

        foreach ($this->invoices as $invoice) {
            try {
                if (!$invoice || !$invoice->unit) {
                    Log::warning('Skipped invoice due to missing unit.', [
                        'invoice_id' => $invoice->id ?? 'unknown'
                    ]);
                    continue;
                }

                $unitPrice = (float)($invoice->unit->unit_price ?? 0);
                $total += $this->taxCalculator->calculatePropertyTax($unitPrice);
            } catch (\Throwable $e) {
                Log::error("Error calculating tax for invoice: " . $e->getMessage(), [
                    'invoice_id' => $invoice->id ?? 'unknown'
                ]);
            }
        }

        return $total;
    }

    public function build(): static
    {
        try {
            Log::info('Building email...');
            // $this->invoices  display
            Log::info('Invoices: '. json_encode($this->invoices->toArray()));
            Log::info('Invoice: '. json_encode($this->invoice));
            Log::info('Tax Calculator: '. json_encode($this->taxCalculator));
            Log::info('Total Tax: '. $this->totalTax);

            return $this->subject('Property Tax Invoice Summary')
                        ->view('email.property.invoice_summary')
                        ->with([
                            'invoices' => $this->invoices,
                            'invoice' => $this->invoice,
                            'taxCalculator' => $this->taxCalculator,
                            'totalTax' => $this->totalTax,
                        ]);
        } catch (\Throwable $e) {
            Log::error("Failed to build email: " . $e->getMessage());
            throw $e;
        }
    }
}
