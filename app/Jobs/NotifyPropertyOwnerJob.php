<?php

namespace App\Jobs;

use Throwable;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use App\Mail\PropertyInvoiceSummaryMail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Bus\Batchable;
use Illuminate\Support\Collection;

class NotifyPropertyOwnerJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Collection $propertyInvoices;

    public $tries = 3;
    public $backoff = [30, 60, 120];

    public function __construct(Collection $propertyInvoices)
    {
        $this->propertyInvoices = $propertyInvoices;
    }

    public function handle(): void
    {
        Log::info("📬 Processing ". $this->propertyInvoices->count(). " invoices for property ID ". ($this->propertyInvoices->first()->unit?->property?->id ?? 'N/A'));
        if ($this->propertyInvoices->isEmpty()) {
            Log::warning("⚠️ No invoices to process in job.");
            return;
        }

        $firstInvoice = $this->propertyInvoices->first();
        $unit = $firstInvoice->unit ?? null;
        $property = $unit ? $unit->property : null;
        $landlord = $property ? $property->landlord : null;

        if (!$landlord || empty($landlord->email)) {
            Log::warning("⚠️ Missing landlord or email for property ID " . ($property ? $property->id : 'N/A'));
            return;
        }

        try {
            Mail::to($landlord->email)->send(
                new PropertyInvoiceSummaryMail($this->propertyInvoices)
            );

            Log::info("✅ Sent invoice summary to {$landlord->email} for property '{$property->property_name}' (ID: {$property->id})");
        } catch (Throwable $e) {
            Log::error("❌ Failed to send invoice summary to property ID " . ($property ? $property->id : 'N/A') . ": {$e->getMessage()}");
            throw $e;
        }
    }

    public function failed(Throwable $exception): void
    {
        $firstInvoice = $this->propertyInvoices->first();
        $unit = $firstInvoice->unit ?? null;
        $property = $unit ? $unit->property : null;

        Log::error("❌ Job failed for property ID " . ($property ? $property->id : 'N/A') . ": {$exception->getMessage()}");
    }
}
