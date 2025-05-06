<?php
namespace App\Services;
use Carbon\Carbon;

class TimeService
{
    public function currentQuarter(): string
    {
        return 'Q' . Carbon::now()->quarter;
    }

    public function currentYear(): int
    {
        return Carbon::now()->year;
    }

    public function currentTimeInfo(): array
    {
        return [
            'quarter' => $this->currentQuarter(),
            'year' => Carbon::now()->year,
            'month' => Carbon::now()->monthName,
            'day' => Carbon::now()->dayName,
            'timezone' => config('app.timezone'),
            'timestamp' => Carbon::now()->timestamp
        ];
    }
}
