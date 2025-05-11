<?php

namespace App\Jobs;

use App\Models\Invoice;
use App\Services\TimeService;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use App\Mail\PropertyInvoiceSummaryMail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class NotifyPropertyOwnerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        $timeService = new TimeService();
        $quarter = $timeService->currentQuarter();
        $year = $timeService->currentYear();
        $invoices = Invoice::with('unit.property')
            ->where('frequency', $quarter)
            ->whereYear('invoice_date', $year)
            ->get();

        $grouped = $invoices->groupBy(function ($invoice) {
            return $invoice->unit->property->id;
        });

        foreach ($grouped as $propertyId => $propertyInvoices) {
            $property = $propertyInvoices->first()->unit->property;
            if (empty($property->landlord->email)) {
                Log::warning("Property {$property->id} has no email address.");
                continue;
            }

            try {
                Mail::to($property->landlord->email)->send(
                    new PropertyInvoiceSummaryMail($propertyInvoices)
                );
                Log::info("✅ Sent invoice summary to property owner: {$property->landlord->email}");
            } catch (\Exception $e) {
                Log::error("❌ Failed to send invoice summary to property {$property->id}: " . $e->getMessage());
            }
        }
    }
}
