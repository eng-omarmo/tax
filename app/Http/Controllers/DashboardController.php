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

        return view('dashboard.index', compact(
            'quarterlyStats',
            'trendData',
            'topProperties',
            'unpaidUnits',
            'currentQuarter',
            'revenueAnalysis'  // Add this to the compact function
        ));
    }

    // public function index()
    // {
    //     $query = Property::query();

    //     $transactionQuery = Transaction::query();
    //     if (auth()->user()->role == 'Landlord') {
    //         $query->whereHas('landlord', function ($q) {
    //             $q->where('user_id', auth()->id());
    //         });

    //         $transactionQuery->whereHas('property', function ($q) {
    //             $q->whereHas('landlord', function ($q) {
    //                 $q->where('user_id', auth()->id());
    //             });
    //         });
    //     }

    //     $noProperties = $query->count();
    //     $IncreaseByThisWeek = $query->where('created_at', '>=', Carbon::now()->startOfWeek())->count();

    //     // total unpaid Tax
    //     $totalUnpaidTax = $transactionQuery
    //         ->where('transaction_type', 'debit')
    //         ->where('status', 'Completed')
    //         ->sum('debit');
    //     $unpaidTaxIncreaseByThisWeek = $transactionQuery->where('transaction_type', 'debit')->sum('debit');
    //     $totalPaidTax = $transactionQuery
    //         ->where('transaction_type', 'credit')
    //         ->where('status', 'Completed')
    //         ->sum('credit');

    //     $totalUnPaidTaxIncreaseByThisWeek = $transactionQuery->where('transaction_type', 'Tax')->where('status', 'Completed')->sum('credit');

    //     // rent calculation
    //     $totalUnpaidRent = $transactionQuery->where('transaction_type', 'Rent')->where('status', 'Completed')->sum('debit');
    //     $unpaidRentIncreaseByThisWeek = $transactionQuery->where('transaction_type', 'Rent')->where('status', 'Increase')->sum('debit');
    //     $totalPaidRent = $transactionQuery->where('transaction_type', 'Rent')->where('status', 'Completed')->sum('credit');
    //     $totalPaidRentIncreaseByThisWeek = $transactionQuery->where('transaction_type', 'Rent')->where('status', 'Completed')->sum('credit');

    //     // tenant calculation
    //     $totalTenants = Unit::where('is_available', 1)->count();
    //     $totalTenantsIncreaseByThisWeek = Tenant::where('created_at', '>=', now()->startOfWeek())->count();

    //     $noIncome = $totalPaidTax;
    //     $profit = $totalUnpaidTax - $noIncome;

    //     // last 7 days transaction
    //     $transactions = $transactionQuery->where('created_at', '>=', now()->subDays(7))->get();

    //     // property calculation
    //     $noNewProperties = Property::where('created_at', '>=', now()->subDays(7))->count();
    //     $noActiveProperties = Property::where('status', 'Active')->count();

    //     // transaction calculation
    //     $noTransaction = $transactionQuery->count();

    //     return view('dashboard/index2', [
    //         'noProperties' => $noProperties,
    //         'IncreaseByThisWeek' => $IncreaseByThisWeek,
    //         'totalUnpaidTax' => $totalUnpaidTax,
    //         'totalPaidTax' => $totalPaidTax,
    //         'unpaidTaxIncreaseByThisWeek' => $unpaidTaxIncreaseByThisWeek,
    //         'totalPaidTaxIncreaseByThisWeek' => $totalUnPaidTaxIncreaseByThisWeek,
    //         'totalUnpaidRent' => $totalUnpaidRent,
    //         'totalPaidRent' => $totalPaidRent,
    //         'unpaidRentIncreaseByThisWeek' => $unpaidRentIncreaseByThisWeek,
    //         'totalPaidRentIncreaseByThisWeek' => $totalPaidRentIncreaseByThisWeek,
    //         'totalTenants' => $totalTenants,
    //         'totalTenantsIncreaseByThisWeek' => $totalTenantsIncreaseByThisWeek,
    //         'noTransaction' => $noTransaction,
    //         'noIncome' => $noIncome,
    //         'profit' => $profit,
    //         'transactions' => $transactions,
    //         'noNewProperties' => $noNewProperties,
    //         'noActiveProperties' => $noActiveProperties
    //     ]);
    // }

    public function index3()
    {
        return view('dashboard/index3');
    }

    public function index4()
    {
        return view('dashboard/index4');
    }

    public function index5()
    {
        return view('dashboard/index5');
    }

    public function index6()
    {
        return view('dashboard/index6');
    }

    public function index7()
    {
        return view('dashboard/index7');
    }

    public function index8()
    {
        return view('dashboard/index8');
    }

    public function index9()
    {
        return view('dashboard/index9');
    }

    public function index10()
    {
        return view('dashboard/index10');
    }
}
