<?php

namespace App\Jobs;

use Throwable;

use App\Models\Notification;
use App\Services\TimeService;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Services\InvoiceSmsService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use App\Mail\PropertyInvoiceSummaryMail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

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
        if ($this->propertyInvoices->isEmpty()) {
            Log::warning("⚠️ No invoices to process in job.");
            return;
        }

        // Assume all invoices belong to the same property
        $property = $this->propertyInvoices->first()?->unit?->property;

        if (!$property) {
            Log::warning("⚠️ Could not determine the property from the invoice.");
            return;
        }

        $landlord = $property->landlord;
        $email = $landlord?->email;

        if (!$landlord || empty($email)) {
            Log::warning("⚠️ Missing landlord or email.");
            return;
        }

        $timeService = app(TimeService::class);
        $year = $timeService->currentYear();
        $quarter = $this->propertyInvoices->first()?->frequency;

        if (empty($quarter)) {
            $quarter = $timeService->currentQuarter();
        }

        if (empty($quarter)) {
            Log::error("❌ Still missing quarter value after fallback. Aborting notification creation.");
            return;
        }
        Log::withContext([
            'property_id' => $property->id,
            'landlord_email' => $email,
            'invoice_count' => $this->propertyInvoices->count(),
        ]);

        DB::transaction(function () use ($property, $email, $year, $quarter) {
            $notificationExists = Notification::where('property_id', $property->id)
                ->where('quarter', $quarter)
                ->where('year', $year)
                ->where('is_notified', 1)
                ->exists();

            if ($notificationExists) {
                Log::warning("⚠️ Notification already exists for property.");
                return;
            }

            // Send the email
            Mail::to($email)->send(
                new PropertyInvoiceSummaryMail($this->propertyInvoices)
            );


            $landlord = $property->landlord;
            if (!$landlord || !$landlord->phone_number) {
                Log::warning("No landlord or phone number for property ID {$property->id}");
                return;
            }

            $invoiceSms = new InvoiceSmsService();
            $invoiceSms->sendPropertyInvoiceSummary($landlord->phone_number, $this->propertyInvoices);


            // Create the notification
            Notification::create([
                'property_id' => $property->id,
                'is_notified' => 1,
                'quarter' => $quarter,
                'year' => $year,
            ]);

            Log::info("✅ Sent invoice summary email to landlord for property '{$property->property_name}' (ID: {$property->id})");
        });
    }


    public function failed(Throwable $exception): void
    {
        $propertyId = $this->propertyInvoices->first()?->unit?->property?->id ?? 'N/A';

        Log::error("❌ Job failed for property ID {$propertyId}: {$exception->getMessage()}");
    }
}
