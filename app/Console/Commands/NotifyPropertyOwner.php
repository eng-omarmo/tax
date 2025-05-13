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
    protected $signature = 'invoices:notify-property-owner
                            {--chunk=100 : Number of records to process per chunk}
                            {--dry-run : Do not dispatch jobs, just simulate the process}';

    protected $description = 'Notify property owners about their invoices';

    public function handle()
    {
        $this->info("🔔 Starting property owner notification process...");

        $timeService = new TimeService();
        $quarter = $timeService->currentQuarter();
        $year = $timeService->currentYear();
        $chunkSize = $this->option('chunk');
        $dryRun = $this->option('dry-run');

        $count = Invoice::where('frequency', $quarter)
            ->whereYear('invoice_date', $year)
            ->count();

        if ($count === 0) {
            $this->info("📭 No invoices to process. Exiting.");
            return;
        }

        $this->info("🔎 Found {$count} invoices to process in chunks of {$chunkSize}");

        Invoice::with('unit.property.landlord')
            ->where('frequency', $quarter)
            ->whereYear('invoice_date', $year)
            ->chunkById($chunkSize, function ($invoices) use ($dryRun) {
                // Filter out invoices with missing relationships
                $filtered = $invoices->filter(function ($invoice) {
                    if (!$invoice->unit || !$invoice->unit->property || !$invoice->unit->property->landlord) {
                        Log::warning("❌ Skipping invoice ID {$invoice->id} due to missing unit/property/landlord.");
                        return false;
                    }
                    return true;
                });

                // Group by landlord (you can change this to property if needed)
                $grouped = $filtered->groupBy(function ($invoice) {
                    return $invoice->unit->property->landlord->id;
                });

                $jobs = [];

                foreach ($grouped as $landlordId => $landlordInvoices) {
                    $jobs[] = new NotifyPropertyOwnerJob($landlordInvoices);
                }

                if ($dryRun) {
                    $this->info("🧪 Dry run enabled. Simulated dispatch of " . count($jobs) . " jobs.");
                    return;
                }

                Bus::batch($jobs)
                    ->allowFailures()
                    ->onQueue('emails')
                    ->dispatch();

                $this->info("✅ Dispatched " . count($jobs) . " notification jobs in this chunk.");
            });

        $this->info("🎉 All invoice notification jobs dispatched successfully.");
    }
}
