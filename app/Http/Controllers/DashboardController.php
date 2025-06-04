<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Unit;
use App\Models\Tenant;
use App\Models\Invoice;
use App\Models\Property;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Services\TimeService;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Style\Alignment;

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

            $quarterSummaries = [
                [
                    'label' => 'Q1 2025',
                    'billed' => 20000,
                    'collected' => 15000,
                    'outstanding' => 5000,
                ],
                [
                    'label' => 'Q2 2025',
                    'billed' => 18000,
                    'collected' => 16000,
                    'outstanding' => 2000,
                ],

                [
                    'label' => 'Q3 2025',
                    'billed' => 18000,
                    'collected' => 16000,
                    'outstanding' => 2000,
                ],
                [
                    'label' => 'Q4 2025',
                    'billed' => 18000,
                    'collected' => 16000,
                    'outstanding' => 2000,
                ],

            ];


        return view('dashboard.index', compact(
            'quarterlyStats',
            'trendData',
            'topProperties',
            'unpaidUnits',
            'currentQuarter',
            'revenueAnalysis' ,
            'availableQuarters',
            'quarterSummaries'

        ));
    }

    /**
     * Export quarterly income report as Excel
     */
    public function exportQuarterlyReport()
    {
        // Get current quarter and year
        $quarterService = new TimeService();
        $currentQuarter = $quarterService->currentQuarter();
        $currentYear = $quarterService->currentYear();

        // Create new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator('Tax Management System')
            ->setLastModifiedBy('Tax Management System')
            ->setTitle('Quarterly Income Report')
            ->setSubject('Quarterly Income Report')
            ->setDescription('Quarterly Income Report for ' . $currentQuarter . ' ' . $currentYear);

        // Add header row
        $sheet->setCellValue('A1', 'Quarterly Income Report - ' . $currentQuarter . ' ' . $currentYear);
        $sheet->mergeCells('A1:G1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Add report date
        $sheet->setCellValue('A2', 'Generated on: ' . now()->format('F d, Y'));
        $sheet->mergeCells('A2:G2');

        // Add summary section header
        $sheet->setCellValue('A4', 'Summary');
        $sheet->getStyle('A4')->getFont()->setBold(true);

        // Get quarterly stats
        $quarterlyStats = $this->getQuarterlyStats($currentQuarter, $currentYear);

        // Add summary data
        $sheet->setCellValue('A5', 'Total Billed:');
        $sheet->setCellValue('B5', '$' . number_format($quarterlyStats['totalBilled'], 2));

        $sheet->setCellValue('A6', 'Total Paid:');
        $sheet->setCellValue('B6', '$' . number_format($quarterlyStats['totalPaid'], 2));

        $sheet->setCellValue('A7', 'Total Outstanding:');
        $sheet->setCellValue('B7', '$' . number_format($quarterlyStats['totalOutstanding'], 2));

        $sheet->setCellValue('A8', 'Collection Rate:');
        $sheet->setCellValue('B8', $quarterlyStats['collectionRate'] . '%');

        $sheet->setCellValue('A9', 'Average Collection Days:');
        $sheet->setCellValue('B9', round($quarterlyStats['averageCollectionDays']) . ' days');

        $sheet->setCellValue('A10', 'Early Payment Rate:');
        $sheet->setCellValue('B10', $quarterlyStats['earlyPaymentRate'] . '%');

        // Add property details header
        $sheet->setCellValue('A12', 'Top Performing Properties');
        $sheet->getStyle('A12')->getFont()->setBold(true);

        // Add property details header row
        $sheet->setCellValue('A13', 'Property Name');
        $sheet->setCellValue('B13', 'Total Units');
        $sheet->setCellValue('C13', 'Paid Units');
        $sheet->setCellValue('D13', 'Collection Rate');
        $sheet->getStyle('A13:D13')->getFont()->setBold(true);

        // Get top properties
        $topProperties = $this->getTopProperties($currentQuarter, $currentYear);

        // Add property data
        $row = 14;
        foreach ($topProperties as $property) {
            $sheet->setCellValue('A' . $row, $property->property_name);
            $sheet->setCellValue('B' . $row, $property->total_invoiced_units);
            $sheet->setCellValue('C' . $row, $property->paid_units_count);
            $sheet->setCellValue('D' . $row, round($property->collection_rate, 2) . '%');
            $row++;
        }

        // Add unpaid units header
        $sheet->setCellValue('A' . ($row + 1), 'Properties with Unpaid Units');
        $sheet->getStyle('A' . ($row + 1))->getFont()->setBold(true);

        // Add unpaid units header row
        $sheet->setCellValue('A' . ($row + 2), 'Property Name');
        $sheet->setCellValue('B' . ($row + 2), 'Unpaid Units');
        $sheet->getStyle('A' . ($row + 2) . ':B' . ($row + 2))->getFont()->setBold(true);

        // Get unpaid units
        $unpaidUnits = $this->getUnpaidUnits($currentQuarter, $currentYear);

        // Add unpaid units data
        $row = $row + 3;
        foreach ($unpaidUnits as $property) {
            $sheet->setCellValue('A' . $row, $property->property_name);
            $sheet->setCellValue('B' . $row, $property->units_count);
            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Create writer and output file
        $writer = new Xlsx($spreadsheet);
        $filename = 'Quarterly_Income_Report_' . $currentQuarter . '_' . $currentYear . '.xlsx';

        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Get quarterly stats for report
     */
    private function getQuarterlyStats($currentQuarter, $currentYear)
    {
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
        ];

        // Add advanced metrics
        $metrics = [
            // Average days to collect
            'averageCollectionDays' => Invoice::whereYear('invoice_date', $currentYear)
                ->where('frequency', $currentQuarter)
                ->where('payment_status', 'paid')
                ->whereNotNull('paid_at')
                ->selectRaw('AVG(DATEDIFF(paid_at, invoice_date)) as avg_days')
                ->value('avg_days') ?? 0,

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
        ];

        // Merge metrics into stats
        $quarterlyStats = array_merge($quarterlyStats, $metrics);

        // Calculate collection rate
        $quarterlyStats['collectionRate'] = $quarterlyStats['totalBilled'] > 0
            ? round(($quarterlyStats['totalPaid'] / $quarterlyStats['totalBilled']) * 100, 2)
            : 0;

        return $quarterlyStats;
    }

    /**
     * Get top performing properties
     */
    private function getTopProperties($currentQuarter, $currentYear)
    {
        return Property::withCount(['units' => function ($query) {
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
    }

    /**
     * Get properties with unpaid units
     */
    private function getUnpaidUnits($currentQuarter, $currentYear)
    {
        return Property::withCount(['units' => function ($query) use ($currentYear, $currentQuarter) {
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
    }
}
