<?php

namespace App\Console\Commands;

use App\Jobs\GenerateInvoiceJob;
use App\Models\Unit;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
class GenerateQuarterlyInvoices extends Command
{
    protected $signature = 'invoices:generate-quarterly';
    protected $description = 'Dispatch jobs to generate quarterly invoices for rented units';

    public function handle()
    {
        $this->info("Starting invoice job dispatch...");

        $query = Unit::query()
            ->with('property')
            ->where('is_owner', 'no')
            ->where('is_available', 1)
            ->whereHas('property', function ($query) {
                $query->where('status', 'Active')
                      ->where('monitoring_status', 'Approved');
            });
            Log::info('SQL: ' . $query->toSql());
            Log::info('Bindings: ', $query->getBindings());
        $total = $query->count();
        $this->info("Found {$total} eligible units. Dispatching jobs...");

        $query->chunk(100, function ($units) {
            foreach ($units as $unit) {
                GenerateInvoiceJob::dispatch($unit);
            }
        });

        $this->info("✅ All invoice jobs dispatched successfully.");
    }
}
