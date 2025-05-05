<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Unit;
use App\Models\Invoice;
use Illuminate\Support\Str;
use App\Services\TimeService;
use Illuminate\Console\Command;

class GenerateQuarterlyInvoices extends Command
{
    protected $signature = 'invoices:generate-quarterly';
    protected $description = 'Generate quarterly invoices for all rented units';

    public function handle()
    {
        $now = Carbon::now();
        $quarter = ceil($now->month / 3);
        $year = $now->year;

        $this->info("Generating invoices for Q{$quarter} {$year}...");

        // Loop through all occupied (not owner) units
        $units = Unit::where('is_owner', 1)
            ->where('is_available', 0)
            ->with('property')
            ->get();

        $generatedCount = 0;
        $skippedCount = 0;
        $timeService = new TimeService();
        $quarter = $timeService->currentQuarter();
        $this->info("This is the current Quarter: {$quarter}");
        foreach ($units as $unit) {
            try {
                // Check if an invoice for this quarter already exists
                $invoiceExists = Invoice::where('unit_id', $unit->id)
                    ->where('frequency', $quarter)
                    ->whereYear('invoice_date', now()->year)
                    ->exists();

                if ($invoiceExists) {
                    $this->info("Invoice already exists for unit ID {$unit->id} for Q{$quarter} {$year}.");
                    $skippedCount++;
                    continue;
                }
                $propertyCode = $unit->property ? $unit->property->house_code : '';
                $invoiceNumber = 'INV-' . $propertyCode . '-' . strtoupper(uniqid());

                $dueDate = $now->copy()->startOfMonth()->addDays(14);

                Invoice::create([
                    'unit_id' => $unit->id,
                    'invoice_number' => $invoiceNumber,
                    'amount' => $unit->unit_price,
                    'invoice_date' => $now->startOfMonth(),
                    'due_date' => $dueDate,
                    'frequency' => $quarter,
                    'payment_status' => 'Pending',
                    'quarter' => $quarter,
                    'year' => $year
                ]);
                $generatedCount++;
                $this->info("Invoice generated for unit ID {$unit->id} with number {$invoiceNumber}");
            } catch (\Exception $e) {
                $this->error("Failed to generate invoice for unit ID {$unit->id}: " . $e->getMessage());
            }
        }

        $this->info("Generation completed: {$generatedCount} invoices generated, {$skippedCount} skipped.");
    }
}
