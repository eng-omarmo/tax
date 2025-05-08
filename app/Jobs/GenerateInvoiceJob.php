<?php

namespace App\Jobs;

use App\Models\Unit;
use App\Models\Invoice;
use App\Services\TimeService;
use App\Services\TransactionService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Str;

class GenerateInvoiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 30;

    protected Unit $unit;

    public function __construct(Unit $unit)
    {
        $this->unit = $unit;
    }

    public function handle(): void
    {
        try {
            $now = Carbon::now();
            $timeService = new TimeService();
            $quarter = $timeService->currentQuarter();
            $year = $timeService->currentYear();

            // Avoid duplication
            if (Invoice::where('unit_id', $this->unit->id)
                ->where('frequency', $quarter)
                ->whereYear('invoice_date', $year)
                ->exists()) {
                Log::info("Invoice already exists for Unit #{$this->unit->id} | Q{$quarter}-{$year}");
                return;
            }

            $propertyCode = $this->unit->property?->house_code ?? 'UNKNOWN';
            $invoiceNumber = 'INV-' . $propertyCode . '-' . strtoupper(Str::random(6));
            $dueDate = $now->copy()->startOfMonth()->addDays(14);

            $invoice = Invoice::create([
                'unit_id' => $this->unit->id,
                'invoice_number' => $invoiceNumber,
                'amount' => $this->unit->unit_price,
                'invoice_date' => $now->startOfMonth(),
                'due_date' => $dueDate,
                'frequency' => $quarter,
                'payment_status' => 'Pending',
                'quarter' => $quarter,
                'year' => $year,
            ]);

            Log::info("✅ Invoice created for Unit #{$this->unit->id} | Invoice #: {$invoiceNumber}");

            (new TransactionService())->recordInvoice($this->unit, $quarter);

        } catch (\Throwable $e) {
            Log::error("❌ Invoice job failed for Unit #{$this->unit->id}: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            $this->fail($e);
        }
    }
}
