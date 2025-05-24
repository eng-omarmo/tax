<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {

        $schedule->command('invoices:generate-quarterly')
            ->now();

        $schedule->command('invoices:notify-property-owner')
            ->now();
    }

    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }

    protected $commands = [
        \App\Console\Commands\GenerateQuarterlyInvoices::class,
        \App\Console\Commands\NotifyPropertyOwner::class,
    ];
}
