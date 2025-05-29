<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Unit;
use App\Models\Tenant;
use App\Models\Invoice;
use App\Models\Property;
use App\Models\Transaction;
use App\Services\TimeService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Get current quarter and year
        $quarterService = new TimeService();
        $currentQuarter = $quarterService->currentQuarter();
        $currentYear = $quarterService->currentYear();

        // Compute base stats
        $quarterlyStats = [
            'totalBilled' => Invoice::whereYear('invoice_date', $currentYear)
                ->whereRaw('frequency = ?', [$currentQuarter])
                ->sum('amount'),

            'totalPaid' => Invoice::whereYear('invoice_date', $currentYear)
                ->whereRaw('frequency = ?', [$currentQuarter])
                ->where('payment_status', 'paid')
                ->sum('amount'),

            'totalOutstanding' => Invoice::whereYear('invoice_date', $currentYear)
                ->whereRaw('frequency = ?', [$currentQuarter])
                ->where('payment_status', 'unpaid')
                ->sum('amount'),

            'unitsTaxed' => Unit::where('is_available', false)->count(),

            'paidInvoices' => Invoice::whereYear('invoice_date', $currentYear)
                ->whereRaw('frequency = ?', [$currentQuarter])
                ->where('payment_status', 'paid')
                ->count(),
        ];

        // Add advanced metrics
        $metrics = [
            // Average days to collect
            'averageCollectionDays' => Invoice::whereYear('invoice_date', $currentYear)
                ->where('frequency', $currentQuarter)
                ->where('payment_status', 'paid')
                ->whereNotNull('paid_at')
                ->selectRaw('AVG(DATEDIFF(paid_at, invoice_date)) as avg_days')
                ->value('avg_days'),

            // Early payment rate
            'earlyPaymentRate' => (function () use ($currentYear, $currentQuarter) {
                $totalPaid = Invoice::whereYear('invoice_date', $currentYear)
                    ->where('frequency', $currentQuarter)
                    ->where('payment_status', 'paid')
                    ->count();

                if ($totalPaid === 0) {
                    return 0;
                }

                $earlyPaid = Invoice::whereYear('invoice_date', $currentYear)
                    ->where('frequency', $currentQuarter)
                    ->where('payment_status', 'paid')
                    ->whereRaw('DATEDIFF(paid_at, due_date) < 0')
                    ->count();

                return round(($earlyPaid / $totalPaid) * 100, 2);
            })(),

            // Overdue tax rate
            'overdueTaxRate' => (function () use ($currentYear, $currentQuarter) {
                $total = Invoice::whereYear('invoice_date', $currentYear)
                    ->where('frequency', $currentQuarter)
                    ->count();

                if ($total === 0) {
                    return 0;
                }

                $overdue = Invoice::whereYear('invoice_date', $currentYear)
                    ->where('frequency', $currentQuarter)
                    ->where('payment_status', 'unpaid')
                    ->where('due_date', '<', now())
                    ->count();

                return round(($overdue / $total) * 100, 2);
            })(),
        ];

        // Merge metrics into stats
        $quarterlyStats = array_merge($quarterlyStats, $metrics);

        // Calculate collection rate
        $quarterlyStats['collectionRate'] = $quarterlyStats['totalBilled'] > 0
            ? round(($quarterlyStats['totalPaid'] / $quarterlyStats['totalBilled']) * 100, 2)
            : 0;

        // Trend data for 4 past quarters
        $trendData = [];
        for ($i = 0; $i < 4; $i++) {
            $quarter = str_replace('Q' , ' ' , $currentQuarter ) - $i;
            $year = $currentYear;
            if ($quarter <= 0) {
                $quarter += 4;
                $year--;
            }

            $totalBilled = Invoice::whereYear('invoice_date', $year)
                ->where('frequency', 'Q' . $quarter)
                ->sum('amount');

            $totalCollected = Invoice::whereYear('invoice_date', $year)
                ->where('frequency', 'Q' . $quarter)
                ->where('payment_status', 'paid')
                ->sum('amount');

            $trendData[] = [
                'quarter' => 'Q' . $quarter,
                'collected' => $totalCollected,
                'outstanding' => Invoice::whereYear('invoice_date', $year)
                    ->where('frequency', 'Q' . $quarter)
                    ->where('payment_status', 'unpaid')
                    ->sum('amount'),
                'collection_rate' => $totalBilled > 0 ? round(($totalCollected / $totalBilled) * 100, 2) : 0,
                'average_days_to_pay' => Invoice::whereYear('invoice_date', $year)
                    ->where('frequency', 'Q' . $quarter)
                    ->where('payment_status', 'paid')
                    ->whereNotNull('paid_at')
                    ->selectRaw('AVG(DATEDIFF(paid_at, invoice_date)) as avg_days')
                    ->value('avg_days') ?? 0
            ];
        }

        // Top performing properties
        $topProperties = Property::withCount(['units' => function ($query) {
            $query->where('is_available', 0);
        }])
            ->withSum(['units' => function ($query) use ($currentYear, $currentQuarter) {
                $query->whereHas('invoices', function ($q) use ($currentYear, $currentQuarter) {
                    $q->whereYear('invoice_date', $currentYear)
                        ->whereRaw('frequency = ?', [$currentQuarter]);
                });
            }], 'unit_price')
            ->withCount(['units as paid_units_count' => function ($query) use ($currentYear, $currentQuarter) {
                $query->whereHas('invoices', function ($q) use ($currentYear, $currentQuarter) {
                    $q->whereYear('invoice_date', $currentYear)
                        ->whereRaw('frequency = ?', [$currentQuarter])
                        ->where('payment_status', 'paid');
                });
            }])
            ->withCount(['units as total_invoiced_units' => function ($query) use ($currentYear, $currentQuarter) {
                $query->whereHas('invoices', function ($q) use ($currentYear, $currentQuarter) {
                    $q->whereYear('invoice_date', $currentYear)
                        ->whereRaw('frequency = ?', [$currentQuarter]);
                });
            }])
            ->get()
            ->map(function ($property) {
                $property->collection_rate = $property->total_invoiced_units > 0
                    ? ($property->paid_units_count / $property->total_invoiced_units) * 100
                    : 0;
                return $property;
            })
            ->sortByDesc('collection_rate')
            ->take(5);

        // Unpaid units by property
        $unpaidUnits = Property::withCount(['units' => function ($query) use ($currentYear, $currentQuarter) {
            $query->whereHas('invoices', function ($q) use ($currentYear, $currentQuarter) {
                $q->whereYear('invoice_date', $currentYear)
                    ->whereRaw('frequency = ?', [$currentQuarter])
                    ->where('payment_status', 'unpaid');
            });
        }])
            ->having('units_count', '>', 0)
            ->orderByDesc('units_count')
            ->take(5)
            ->get();

        // Add revenue analysis data
        $revenueAnalysis = [
            [
                'label' => 'Current Quarter Revenue',
                'value' => $quarterlyStats['totalPaid'],
                'description' => 'Total tax collected this quarter'
            ],
            [
                'label' => 'Outstanding Revenue',
                'value' => $quarterlyStats['totalOutstanding'],
                'description' => 'Pending tax payments'
            ],
            [
                'label' => 'Early Payment Revenue',
                'value' => $quarterlyStats['totalPaid'] * ($quarterlyStats['earlyPaymentRate'] / 100),
                'description' => 'Revenue from early payments'
            ],
            [
                'label' => 'Average Collection',
                'value' => $quarterlyStats['totalBilled'] / 3, // Monthly average
                'description' => 'Average monthly collection'
            ]
        ];
        $availableQuarters = Invoice::distinct('frequency')
            ->pluck('frequency')
            ->sort()
            ->values();

        return view('dashboard.index', compact(
            'quarterlyStats',
            'trendData',
            'topProperties',
            'unpaidUnits',
            'currentQuarter',
            'revenueAnalysis' ,
            'availableQuarters'

        ));
    }

}
