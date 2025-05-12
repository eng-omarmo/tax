<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Jobs\NotifyPropertyOwnerJob;
use Illuminate\Support\Facades\Bus;
use App\Models\Invoice;
use App\Services\TimeService;

class NotifyPropertyOwner extends Command
{
    protected $signature = 'invoices:notify-property-owner {--chunk=100 : Number of records to process per chunk}';
    protected $description = 'Notify property owners about their invoices';

    public function handle()
    {
        $this->info("Starting property owner notification process...");

        $timeService = new TimeService();
        $quarter = $timeService->currentQuarter();
        $year = $timeService->currentYear();
        $chunkSize = $this->option('chunk');

        // Get count of invoices to process
        $count = Invoice::where('frequency', $quarter)
            ->whereYear('invoice_date', $year)
            ->count();

        $this->info("Found {$count} invoices to process in chunks of {$chunkSize}");

        if ($count === 0) {
            $this->info("No invoices to process. Exiting.");
            return;
        }

        // Process in chunks to avoid memory issues
        Invoice::with('unit.property.landlord')
            ->where('frequency', $quarter)
            ->whereYear('invoice_date', $year)
            ->chunkById($chunkSize, function ($invoices) {
                // Group by property and dispatch jobs
                $grouped = $invoices->groupBy(function ($invoice) {
                    return $invoice->unit->property->id;
                });

                $jobs = [];
                foreach ($grouped as $propertyId => $propertyInvoices) {
                    $jobs[] = new NotifyPropertyOwnerJob($propertyInvoices);
                }

                // Dispatch batch of jobs
                Bus::batch($jobs)
                    ->allowFailures()
                    ->onQueue('emails')
                    ->dispatch();

                $this->info("Dispatched " . count($jobs) . " notification jobs");
            });

        $this->info("✅ All invoice notification jobs dispatched successfully.");
    }
}
