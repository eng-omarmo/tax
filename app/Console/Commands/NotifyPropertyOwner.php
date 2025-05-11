<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Jobs\NotifyPropertyOwnerJob;

class NotifyPropertyOwner extends Command
{
    protected $signature = 'invoices:notify-property-owner';
    protected $description = 'Notify property owner ';

    public function handle()
    {
        $this->info("Starting Notify Property job dispatch...");
        NotifyPropertyOwnerJob::dispatch();
        $this->info("✅ All invoice jobs dispatched successfully.");
    }
}
