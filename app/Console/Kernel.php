<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {

        $schedule->command('invoices:generate-quarterly')
            ->monthlyOn(1, '00:00')
            ->when(fn() => in_array(now()->month, [1, 4, 7, 10]));
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
