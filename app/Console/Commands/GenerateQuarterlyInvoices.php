<?php

namespace App\Console\Commands;

use App\Models\Unit;
use Illuminate\Console\Command;
use App\Jobs\GenerateInvoiceJob;
use Illuminate\Support\Facades\Log;
use App\Jobs\NotifyPropertyOwnerJob;
use Illuminate\Support\Facades\Artisan;

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

        $total = $query->count();
        $this->info("Found {$total} eligible units. Dispatching jobs...");

        $query->chunk(100, function ($units) {
            foreach ($units as $unit) {
                GenerateInvoiceJob::dispatch($unit);
            }
        });
            // This runs one job then stops
            Artisan::call('queue:work', [
                '--once' => true,
                '--queue' => 'default', // optional
                '--delay' => 0,
                '--sleep' => 1,
                '--tries' => 3
            ]);



        $this->info("✅ All invoice jobs dispatched successfully.");
    }
}
