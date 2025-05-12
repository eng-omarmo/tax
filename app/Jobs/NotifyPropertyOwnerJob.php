<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use App\Mail\PropertyInvoiceSummaryMail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Bus\Batchable;
use Throwable;

class NotifyPropertyOwnerJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $propertyInvoices;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $backoff = [30, 60, 120];

    /**
     * Create a new job instance.
     *
     * @param  \Illuminate\Support\Collection  $propertyInvoices
     * @return void
     */
    public function __construct($propertyInvoices)
    {
        $this->propertyInvoices = $propertyInvoices;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->propertyInvoices->isEmpty()) {
            Log::warning("No invoices to process in job.");
            return;
        }

        $property = $this->propertyInvoices->first()->unit->property;

        if (empty($property->landlord->email)) {
            Log::warning("Property {$property->id} ({$property->property_name}) has no landlord email address.");
            return;
        }

        try {
            Mail::to($property->landlord->email)->send(
                new PropertyInvoiceSummaryMail($this->propertyInvoices)
            );
            Log::info("✅ Sent invoice summary to property owner: {$property->landlord->email} for property {$property->property_name}");
        } catch (\Exception $e) {
            Log::error("❌ Failed to send invoice summary to property {$property->id}: " . $e->getMessage());
            throw $e; // Rethrow to trigger job retry
        }
    }

    /**
     * Handle a job failure.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public function failed(Throwable $exception)
    {
        $property = $this->propertyInvoices->first()->unit->property;
        Log::error("❌ Job failed permanently for property {$property->id} ({$property->property_name}): " . $exception->getMessage());
    }
}
