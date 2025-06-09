<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Unit;
use App\Models\Tenant;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\District;
use App\Models\Property;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Services\TimeService;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

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
            $quarter = str_replace('Q', ' ', $currentQuarter) - $i;
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
                    $q
                        ->whereYear('invoice_date', $currentYear)
                        ->whereRaw('frequency = ?', [$currentQuarter]);
                });
            }], 'unit_price')
            ->withCount(['units as paid_units_count' => function ($query) use ($currentYear, $currentQuarter) {
                $query->whereHas('invoices', function ($q) use ($currentYear, $currentQuarter) {
                    $q
                        ->whereYear('invoice_date', $currentYear)
                        ->whereRaw('frequency = ?', [$currentQuarter])
                        ->where('payment_status', 'paid');
                });
            }])
            ->withCount(['units as total_invoiced_units' => function ($query) use ($currentYear, $currentQuarter) {
                $query->whereHas('invoices', function ($q) use ($currentYear, $currentQuarter) {
                    $q
                        ->whereYear('invoice_date', $currentYear)
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
                $q
                    ->whereYear('invoice_date', $currentYear)
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
                'value' => $quarterlyStats['totalBilled'] / 3,  // Monthly average
                'description' => 'Average monthly collection'
            ]
        ];
        $availableQuarters = Invoice::distinct('frequency')
            ->pluck('frequency')
            ->sort()
            ->values();

        // quater sumaries
        $year = now()->year;

        $rawData = Invoice::selectRaw("
            LOWER(frequency) as frequency,
            SUM(amount) as billed,
            SUM(CASE WHEN payment_status = 'Paid' THEN amount ELSE 0 END) as collected,
            SUM(CASE WHEN payment_status != 'Paid' THEN amount ELSE 0 END) as outstanding
        ")
            ->whereYear('invoice_date', $year)
            ->groupBy('frequency')
            ->get()
            ->keyBy('frequency');

        $quarters = ['q1', 'q2', 'q3', 'q4'];
        $quarterSummaries = [];

        foreach ($quarters as $q) {
            if (isset($rawData[$q])) {
                $quarterSummaries[] = [
                    'label' => strtoupper($q) . ' ' . $year,
                    'billed' => $rawData[$q]->billed,
                    'collected' => $rawData[$q]->collected,
                    'outstanding' => $rawData[$q]->outstanding,
                ];
            } else {
                $quarterSummaries[] = [
                    'label' => strtoupper($q) . ' ' . $year,
                    'billed' => 0,
                    'collected' => 0,
                    'outstanding' => 0,
                ];
            }
        }
        // Top Performing Districts

        $topDistricts = Invoice::select(
            'districts.name',
            DB::raw('SUM(invoices.amount) as total_billed'),
            DB::raw('SUM(CASE WHEN invoices.payment_status = "paid" THEN invoices.amount ELSE 0 END) as total_collected')
        )
            ->join('units', 'invoices.unit_id', '=', 'units.id')
            ->join('properties', 'units.property_id', '=', 'properties.id')
            ->join('districts', 'properties.district_id', '=', 'districts.id')
            ->groupBy('districts.name')
            ->orderByDesc('total_collected')
            ->get()
            ->map(function ($district) {
                $district->collection_rate = $district->total_billed > 0
                    ? ($district->total_collected / $district->total_billed) * 100
                    : 0;
                return $district;
            });

        $payments = Payment::with('invoice')->latest('payment_date')->take(10)->get();

        return view('dashboard.index', compact(
            'quarterlyStats',
            'trendData',
            'topProperties',
            'unpaidUnits',
            'currentQuarter',
            'revenueAnalysis',
            'availableQuarters',
            'topDistricts',
            'quarterSummaries',
            'payments'
        ));
    }

    /**
     * Export quarterly income report as Excel
     */
    public function exportQuarterlyReport(Request $request)
    {
        // Get quarter and year (allow selection via request)
        $quarterService = new TimeService();
        $currentQuarter = $request->input('quarter', $quarterService->currentQuarter());
        $currentYear = $request->input('year', $quarterService->currentYear());

        // Create new Spreadsheet object
        $spreadsheet = new Spreadsheet();

        // Set document properties
        $spreadsheet
            ->getProperties()
            ->setCreator('Tax Management System')
            ->setLastModifiedBy('Tax Management System')
            ->setTitle('Comprehensive Quarterly Income Report')
            ->setSubject('Quarterly Income Report')
            ->setDescription('Comprehensive Quarterly Income Report for ' . $currentQuarter . ' ' . $currentYear);

        // Create multiple worksheets for different sections
        $summarySheet = $spreadsheet->getActiveSheet();
        $summarySheet->setTitle('Executive Summary');
        $districtSheet = $spreadsheet->createSheet();
        $districtSheet->setTitle('District Analysis');
        $propertySheet = $spreadsheet->createSheet();
        $propertySheet->setTitle('Property Performance');
        $trendSheet = $spreadsheet->createSheet();
        $trendSheet->setTitle('Trend Analysis');

        // EXECUTIVE SUMMARY SHEET
        $this->buildExecutiveSummarySheet($summarySheet, $currentQuarter, $currentYear);

        // DISTRICT ANALYSIS SHEET
        $this->buildDistrictAnalysisSheet($districtSheet, $currentQuarter, $currentYear);

        // PROPERTY PERFORMANCE SHEET
        $this->buildPropertyPerformanceSheet($propertySheet, $currentQuarter, $currentYear);

        // TREND ANALYSIS SHEET
        $this->buildTrendAnalysisSheet($trendSheet, $currentQuarter, $currentYear);

        // Create writer and output file
        $writer = new Xlsx($spreadsheet);
        $filename = 'Comprehensive_Quarterly_Income_Report_' . $currentQuarter . '_' . $currentYear . '.xlsx';

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
                    $q
                        ->whereYear('invoice_date', $currentYear)
                        ->whereRaw('frequency = ?', [$currentQuarter]);
                });
            }], 'unit_price')
            ->withCount(['units as paid_units_count' => function ($query) use ($currentYear, $currentQuarter) {
                $query->whereHas('invoices', function ($q) use ($currentYear, $currentQuarter) {
                    $q
                        ->whereYear('invoice_date', $currentYear)
                        ->whereRaw('frequency = ?', [$currentQuarter])
                        ->where('payment_status', 'paid');
                });
            }])
            ->withCount(['units as total_invoiced_units' => function ($query) use ($currentYear, $currentQuarter) {
                $query->whereHas('invoices', function ($q) use ($currentYear, $currentQuarter) {
                    $q
                        ->whereYear('invoice_date', $currentYear)
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
                $q
                    ->whereYear('invoice_date', $currentYear)
                    ->whereRaw('frequency = ?', [$currentQuarter])
                    ->where('payment_status', 'unpaid');
            });
        }])
            ->having('units_count', '>', 0)
            ->orderByDesc('units_count')
            ->take(5)
            ->get();
    }


    private function buildExecutiveSummarySheet($sheet, $currentQuarter, $currentYear)
    {
        // Get quarterly stats
        $quarterlyStats = $this->getQuarterlyStats($currentQuarter, $currentYear);

        // Add header with logo and title
        $sheet->setCellValue('A1', 'QUARTERLY INCOME REPORT');
        $sheet->setCellValue('A2', $currentQuarter . ' ' . $currentYear);
        $sheet->mergeCells('A1:G1');
        $sheet->mergeCells('A2:G2');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Add report date and quarter information
        $sheet->setCellValue('A4', 'Report Generated:');
        $sheet->setCellValue('B4', now()->format('F d, Y'));
        $sheet->setCellValue('A5', 'Quarter Period:');
        $sheet->setCellValue('B5', $this->getQuarterDateRange($currentQuarter, $currentYear));

        // KEY PERFORMANCE INDICATORS SECTION
        $sheet->setCellValue('A7', 'KEY PERFORMANCE INDICATORS');
        $sheet->mergeCells('A7:G7');
        $sheet->getStyle('A7')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A7')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('DDEBF7');

        // Create KPI table with visual indicators
        $sheet->setCellValue('A8', 'Metric');
        $sheet->setCellValue('B8', 'Value');
        $sheet->setCellValue('C8', 'Previous Quarter');
        $sheet->setCellValue('D8', 'Change');
        $sheet->setCellValue('E8', 'Status');
        $sheet->getStyle('A8:E8')->getFont()->setBold(true);
        $sheet->getStyle('A8:E8')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('F2F2F2');

        // Get previous quarter stats for comparison
        $prevQuarterStats = $this->getPreviousQuarterStats($currentQuarter, $currentYear);

        // Row 9: Total Billed
        $sheet->setCellValue('A9', 'Total Billed:');
        $sheet->setCellValue('B9', '$' . number_format($quarterlyStats['totalBilled'], 2));
        $sheet->setCellValue('C9', '$' . number_format($prevQuarterStats['totalBilled'], 2));
        $billedChange = $prevQuarterStats['totalBilled'] > 0 ?
            (($quarterlyStats['totalBilled'] - $prevQuarterStats['totalBilled']) / $prevQuarterStats['totalBilled']) * 100 : 0;
        $sheet->setCellValue('D9', number_format($billedChange, 1) . '%');
        $sheet->setCellValue('E9', $billedChange >= 0 ? 'Positive' : 'Negative');
        $sheet->getStyle('E9')->getFont()->getColor()->setARGB($billedChange >= 0 ? '00AA00' : 'FF0000');

        // Row 10: Total Paid
        $sheet->setCellValue('A10', 'Total Paid:');
        $sheet->setCellValue('B10', '$' . number_format($quarterlyStats['totalPaid'], 2));
        $sheet->setCellValue('C10', '$' . number_format($prevQuarterStats['totalPaid'], 2));
        $paidChange = $prevQuarterStats['totalPaid'] > 0 ?
            (($quarterlyStats['totalPaid'] - $prevQuarterStats['totalPaid']) / $prevQuarterStats['totalPaid']) * 100 : 0;
        $sheet->setCellValue('D10', number_format($paidChange, 1) . '%');
        $sheet->setCellValue('E10', $paidChange >= 0 ? 'Positive' : 'Negative');
        $sheet->getStyle('E10')->getFont()->getColor()->setARGB($paidChange >= 0 ? '00AA00' : 'FF0000');

        // Row 11: Total Outstanding
        $sheet->setCellValue('A11', 'Total Outstanding:');
        $sheet->setCellValue('B11', '$' . number_format($quarterlyStats['totalOutstanding'], 2));
        $sheet->setCellValue('C11', '$' . number_format($prevQuarterStats['totalOutstanding'], 2));
        $outstandingChange = $prevQuarterStats['totalOutstanding'] > 0 ?
            (($quarterlyStats['totalOutstanding'] - $prevQuarterStats['totalOutstanding']) / $prevQuarterStats['totalOutstanding']) * 100 : 0;
        $sheet->setCellValue('D11', number_format($outstandingChange, 1) . '%');
        $sheet->setCellValue('E11', $outstandingChange <= 0 ? 'Positive' : 'Negative'); // Reversed logic - lower outstanding is better
        $sheet->getStyle('E11')->getFont()->getColor()->setARGB($outstandingChange <= 0 ? '00AA00' : 'FF0000');

        // Row 12: Collection Rate
        $sheet->setCellValue('A12', 'Collection Rate:');
        $sheet->setCellValue('B12', $quarterlyStats['collectionRate'] . '%');
        $sheet->setCellValue('C12', $prevQuarterStats['collectionRate'] . '%');
        $rateChange = $quarterlyStats['collectionRate'] - $prevQuarterStats['collectionRate'];
        $sheet->setCellValue('D12', number_format($rateChange, 1) . '%');
        $sheet->setCellValue('E12', $rateChange >= 0 ? 'Improved' : 'Declined');
        $sheet->getStyle('E12')->getFont()->getColor()->setARGB($rateChange >= 0 ? '00AA00' : 'FF0000');

        // Row 13: Average Collection Days
        $sheet->setCellValue('A13', 'Avg Collection Days:');
        $sheet->setCellValue('B13', round($quarterlyStats['averageCollectionDays']) . ' days');
        $sheet->setCellValue('C13', round($prevQuarterStats['averageCollectionDays']) . ' days');
        $daysChange = $prevQuarterStats['averageCollectionDays'] > 0 ?
            $quarterlyStats['averageCollectionDays'] - $prevQuarterStats['averageCollectionDays'] : 0;
        $sheet->setCellValue('D13', number_format($daysChange, 1) . ' days');
        $sheet->setCellValue('E13', $daysChange <= 0 ? 'Improved' : 'Declined'); // Reversed logic - lower days is better
        $sheet->getStyle('E13')->getFont()->getColor()->setARGB($daysChange <= 0 ? '00AA00' : 'FF0000');

        // Row 14: Early Payment Rate
        $sheet->setCellValue('A14', 'Early Payment Rate:');
        $sheet->setCellValue('B14', $quarterlyStats['earlyPaymentRate'] . '%');
        $sheet->setCellValue('C14', $prevQuarterStats['earlyPaymentRate'] . '%');
        $earlyChange = $quarterlyStats['earlyPaymentRate'] - $prevQuarterStats['earlyPaymentRate'];
        $sheet->setCellValue('D14', number_format($earlyChange, 1) . '%');
        $sheet->setCellValue('E14', $earlyChange >= 0 ? 'Improved' : 'Declined');
        $sheet->getStyle('E14')->getFont()->getColor()->setARGB($earlyChange >= 0 ? '00AA00' : 'FF0000');

        // EXECUTIVE INSIGHTS SECTION
        $sheet->setCellValue('A16', 'EXECUTIVE INSIGHTS');
        $sheet->mergeCells('A16:G16');
        $sheet->getStyle('A16')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A16')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('DDEBF7');

        // Generate insights based on data
        $insights = $this->generateExecutiveInsights($quarterlyStats, $prevQuarterStats);
        $row = 17;
        foreach ($insights as $insight) {
            $sheet->setCellValue('A' . $row, '• ' . $insight);
            $sheet->mergeCells('A' . $row . ':G' . $row);
            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    private function buildDistrictAnalysisSheet($sheet, $currentQuarter, $currentYear)
    {
        // Get district performance data
        $districtData = $this->getDistrictPerformanceData($currentQuarter, $currentYear);

        // Add header
        $sheet->setCellValue('A1', 'DISTRICT PERFORMANCE ANALYSIS');
        $sheet->setCellValue('A2', $currentQuarter . ' ' . $currentYear);
        $sheet->mergeCells('A1:H1');
        $sheet->mergeCells('A2:H2');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Add district comparison table header
        $sheet->setCellValue('A4', 'District');
        $sheet->setCellValue('B4', 'Total Revenue');
        $sheet->setCellValue('C4', 'Total Collected');
        $sheet->setCellValue('D4', 'Outstanding');
        $sheet->setCellValue('E4', 'Collection Rate');
        $sheet->setCellValue('F4', 'Growth');
        $sheet->setCellValue('G4', 'Properties');
        $sheet->setCellValue('H4', 'Units');
        $sheet->getStyle('A4:H4')->getFont()->setBold(true);
        $sheet->getStyle('A4:H4')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('F2F2F2');

        // Add district data
        $row = 5;
        foreach ($districtData as $district) {
            $sheet->setCellValue('A' . $row, $district['district_name']);
            $sheet->setCellValue('B' . $row, '$' . number_format($district['totalRevenue'], 2));
            $sheet->setCellValue('C' . $row, '$' . number_format($district['totalPaid'], 2));
            $sheet->setCellValue('D' . $row, '$' . number_format($district['totalOutstanding'], 2));
            $sheet->setCellValue('E' . $row, number_format($district['collectionRate'], 1) . '%');
            $sheet->setCellValue('F' . $row, number_format($district['revenueGrowth'], 1) . '%');
            $sheet->setCellValue('G' . $row, $district['propertyCount']);
            $sheet->setCellValue('H' . $row, $district['unitCount']);

            // Apply conditional formatting for collection rate
            if ($district['collectionRate'] < 50) {
                $sheet->getStyle('E' . $row)->getFont()->getColor()->setARGB('FF0000'); // Red
            } elseif ($district['collectionRate'] < 75) {
                $sheet->getStyle('E' . $row)->getFont()->getColor()->setARGB('FFA500'); // Orange
            } else {
                $sheet->getStyle('E' . $row)->getFont()->getColor()->setARGB('008000'); // Green
            }

            // Apply conditional formatting for growth
            if ($district['revenueGrowth'] < 0) {
                $sheet->getStyle('F' . $row)->getFont()->getColor()->setARGB('FF0000'); // Red
            } else {
                $sheet->getStyle('F' . $row)->getFont()->getColor()->setARGB('008000'); // Green
            }

            $row++;
        }

        // Add district recommendations section
        $sheet->setCellValue('A' . ($row + 2), 'DISTRICT IMPROVEMENT RECOMMENDATIONS');
        $sheet->mergeCells('A' . ($row + 2) . ':H' . ($row + 2));
        $sheet->getStyle('A' . ($row + 2))->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A' . ($row + 2))->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('DDEBF7');

        // Add recommendation headers
        $sheet->setCellValue('A' . ($row + 3), 'District');
        $sheet->setCellValue('B' . ($row + 3), 'Key Recommendations');
        $sheet->getStyle('A' . ($row + 3) . ':B' . ($row + 3))->getFont()->setBold(true);
        $sheet->getStyle('A' . ($row + 3) . ':B' . ($row + 3))->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('F2F2F2');

        // Add district recommendations
        $recRow = $row + 4;
        foreach ($districtData as $district) {
            if (!empty($district['recommendations'])) {
                $sheet->setCellValue('A' . $recRow, $district['district_name']);
                $sheet->setCellValue('B' . $recRow, implode("\n", $district['recommendations']));
                $sheet->getStyle('B' . $recRow)->getAlignment()->setWrapText(true);
                $recRow++;
            }
        }

        // Auto-size columns
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }
    /**
     * Get property performance data for the quarterly report
     */
    private function getPropertyPerformanceData($currentQuarter, $currentYear)
    {
        // Get quarter date range
        $quarterNumber = (int) substr($currentQuarter, 1, 1);
        $startMonth = ($quarterNumber - 1) * 3 + 1;
        $endMonth = $quarterNumber * 3;
        $startDate = Carbon::createFromDate($currentYear, $startMonth, 1)->startOfDay();
        $endDate = Carbon::createFromDate($currentYear, $endMonth, 1)->endOfMonth()->endOfDay();

        $properties = Property::all();
        $propertyData = [];
        $totalSystemRevenue = 0;
        $totalSystemPaid = 0;
        $totalSystemOutstanding = 0;

        // Performance metrics
        $bestCollectionRate = ['property' => null, 'rate' => 0];
        $worstCollectionRate = ['property' => null, 'rate' => 100];
        $highestRevenue = ['property' => null, 'amount' => 0];

        // Monthly trends data
        $monthlyTrends = [];
        for ($i = 0; $i < 3; $i++) {
            $month = $startMonth + $i;
            if ($month <= 12) {
                $monthName = Carbon::createFromDate($currentYear, $month, 1)->format('F');
                $monthlyTrends[$monthName] = [];
            }
        }

        foreach ($properties as $property) {
            // Get units in this property
            $units = Unit::where('property_id', $property->id)->pluck('id');

            // Skip if no units in this property
            if ($units->isEmpty()) {
                continue;
            }

            // Get invoices for these units in the selected quarter
            $invoices = Invoice::whereIn('unit_id', $units)
                ->whereBetween('invoice_date', [$startDate, $endDate])
                ->get();

            // Calculate property metrics
            $totalRevenue = $invoices->sum('amount');
            $totalPaid = $invoices->where('payment_status', 'Paid')->sum('amount');
            $totalOutstanding = $invoices->where('payment_status', '!=', 'Paid')->sum('amount');
            $collectionRate = $totalRevenue > 0 ? ($totalPaid / $totalRevenue) * 100 : 0;
            $unitCount = count($units);
            $invoiceCount = count($invoices);
            $paidInvoiceCount = $invoices->where('payment_status', 'Paid')->count();

            // Calculate growth compared to previous quarter
            $prevQuarterNumber = $quarterNumber - 1;
            $prevYear = $currentYear;
            if ($prevQuarterNumber < 1) {
                $prevQuarterNumber = 4;
                $prevYear--;
            }
            $prevStartMonth = ($prevQuarterNumber - 1) * 3 + 1;
            $prevEndMonth = $prevQuarterNumber * 3;
            $prevStartDate = Carbon::createFromDate($prevYear, $prevStartMonth, 1)->startOfDay();
            $prevEndDate = Carbon::createFromDate($prevYear, $prevEndMonth, 1)->endOfMonth()->endOfDay();

            $prevInvoices = Invoice::whereIn('unit_id', $units)
                ->whereBetween('invoice_date', [$prevStartDate, $prevEndDate])
                ->get();

            $prevTotalPaid = $prevInvoices->where('payment_status', 'Paid')->sum('amount');
            $revenueGrowth = $prevTotalPaid > 0 ? (($totalPaid - $prevTotalPaid) / $prevTotalPaid) * 100 : 0;

            // Get monthly breakdown for this property
            foreach ($monthlyTrends as $month => $data) {
                $monthNumber = Carbon::parse("1 $month $currentYear")->month;
                $monthStart = Carbon::createFromDate($currentYear, $monthNumber, 1)->startOfDay();
                $monthEnd = Carbon::createFromDate($currentYear, $monthNumber, 1)->endOfMonth()->endOfDay();

                $monthInvoices = Invoice::whereIn('unit_id', $units)
                    ->whereBetween('invoice_date', [$monthStart, $monthEnd])
                    ->get();

                $monthlyTrends[$month][$property->property_name] = $monthInvoices->where('payment_status', 'Paid')->sum('amount');
            }

            // Get district information
            $district = $property->district ? $property->district->name : 'Unassigned';

            // Store property data
            $propertyData[] = [
                'property_id' => $property->id,
                'property_name' => $property->property_name,
                'district' => $district,
                'totalRevenue' => $totalRevenue,
                'totalPaid' => $totalPaid,
                'totalOutstanding' => $totalOutstanding,
                'collectionRate' => $collectionRate,
                'unitCount' => $unitCount,
                'invoiceCount' => $invoiceCount,
                'paidInvoiceCount' => $paidInvoiceCount,
                'revenueGrowth' => $revenueGrowth,
                'propertyCount' => 1, // Always 1 for individual property
                'monthlyTrends' => isset($monthlyTrends) ? array_map(function ($month) use ($property) {
                    return $month[$property->property_name] ?? 0;
                }, $monthlyTrends) : []
            ];

            // Update system totals
            $totalSystemRevenue += $totalRevenue;
            $totalSystemPaid += $totalPaid;
            $totalSystemOutstanding += $totalOutstanding;

            // Update performance metrics
            if ($collectionRate > $bestCollectionRate['rate'] && $totalRevenue > 0) {
                $bestCollectionRate['property'] = $property->property_name;
                $bestCollectionRate['rate'] = $collectionRate;
            }

            if ($collectionRate < $worstCollectionRate['rate'] && $totalRevenue > 0) {
                $worstCollectionRate['property'] = $property->property_name;
                $worstCollectionRate['rate'] = $collectionRate;
            }

            if ($totalPaid > $highestRevenue['amount']) {
                $highestRevenue['property'] = $property->property_name;
                $highestRevenue['amount'] = $totalPaid;
            }
        }

        // Sort properties by collection rate (descending)
        usort($propertyData, function ($a, $b) {
            return $b['collectionRate'] <=> $a['collectionRate'];
        });

        // Calculate system-wide collection rate
        $systemCollectionRate = $totalSystemRevenue > 0 ? ($totalSystemPaid / $totalSystemRevenue) * 100 : 0;

        // Generate recommendations for each property
        foreach ($propertyData as &$property) {
            $property['recommendations'] = $this->generatePropertyRecommendations($property, $systemCollectionRate);
        }

        return $propertyData;
    }

    /**
     * Generate recommendations for improving property performance
     */
    private function generatePropertyRecommendations($property, $systemCollectionRate)
    {
        $recommendations = [];

        // Collection rate recommendations
        if ($property['collectionRate'] < $systemCollectionRate - 10) {
            $recommendations[] = "Collection rate is significantly below system average. Consider implementing stricter collection policies or payment incentives.";
        } elseif ($property['collectionRate'] < $systemCollectionRate - 5) {
            $recommendations[] = "Collection rate is below system average. Review collection process and tenant communication strategies.";
        }

        // Revenue growth recommendations
        if ($property['revenueGrowth'] < 0) {
            $recommendations[] = "Revenue has declined by " . abs(round($property['revenueGrowth'], 1)) . "% compared to last quarter. Investigate causes and develop recovery plan.";
        } elseif ($property['revenueGrowth'] < 2) {
            $recommendations[] = "Revenue growth is stagnant. Consider reviewing unit pricing or occupancy strategies.";
        }

        // Unit utilization recommendations
        if ($property['paidInvoiceCount'] < $property['invoiceCount'] * 0.7) {
            $recommendations[] = "Only " . round(($property['paidInvoiceCount'] / $property['invoiceCount']) * 100, 1) . "% of invoices are paid. Focus on improving tenant payment compliance.";
        }

        // Outstanding amount recommendations
        if ($property['totalOutstanding'] > $property['totalPaid'] * 0.3) {
            $recommendations[] = "High outstanding amount relative to collected revenue. Implement targeted collection efforts for overdue payments.";
        }

        // Positive recommendation if performing well
        if (empty($recommendations) && $property['collectionRate'] > $systemCollectionRate) {
            $recommendations[] = "Property is performing well with above-average collection rate. Continue current management practices.";
        } elseif (empty($recommendations)) {
            $recommendations[] = "Property is performing adequately. Monitor for any changes in collection patterns.";
        }

        return $recommendations;
    }
    private function buildPropertyPerformanceSheet($sheet, $currentQuarter, $currentYear)
    {
        // Get property performance data
        $propertyData = $this->getPropertyPerformanceData($currentQuarter, $currentYear);

        // Add header
        $sheet->setCellValue('A1', 'PROPERTY PERFORMANCE ANALYSIS');
        $sheet->setCellValue('A2', $currentQuarter . ' ' . $currentYear);
        $sheet->mergeCells('A1:H1');
        $sheet->mergeCells('A2:H2');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Add property comparison table header
        $sheet->setCellValue('A4', 'Property');
        $sheet->setCellValue('B4', 'Total Revenue');
        $sheet->setCellValue('C4', 'Total Collected');
        $sheet->setCellValue('D4', 'Outstanding');
        $sheet->setCellValue('E4', 'Collection Rate');
        $sheet->setCellValue('F4', 'Growth');
        $sheet->setCellValue('G4', 'Properties');
        $sheet->setCellValue('H4', 'Units');
        $sheet->getStyle('A4:H4')->getFont()->setBold(true);
        $sheet->getStyle('A4:H4')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('F2F2F2');

        // Add property data
        $row = 5;
        foreach ($propertyData as $property) {
            $sheet->setCellValue('A' . $row, $property['property_name']);
            $sheet->setCellValue('B' . $row, '$' . number_format($property['totalRevenue'], 2));
            $sheet->setCellValue('C' . $row, '$' . number_format($property['totalPaid'], 2));
            $sheet->setCellValue('D' . $row, '$' . number_format($property['totalOutstanding'], 2));
            $sheet->setCellValue('E' . $row, number_format($property['collectionRate'], 1) . '%');
            $sheet->setCellValue('F' . $row, number_format($property['revenueGrowth'], 1) . '%');
            $sheet->setCellValue('G' . $row, $property['propertyCount']);
            $sheet->setCellValue('H' . $row, $property['unitCount']);

            // Apply conditional formatting for collection rate
            if ($property['collectionRate'] < 50) {
                $sheet->getStyle('E' . $row)->getFont()->getColor()->setARGB('FF0000'); // Red
            } elseif ($property['collectionRate'] < 75) {
                $sheet->getStyle('E' . $row)->getFont()->getColor()->setARGB('FFA500'); // Orange
            } else {
                $sheet->getStyle('E' . $row)->getFont()->getColor()->setARGB('008000'); // Green
            }

            // Apply conditional formatting for growth
            if ($property['revenueGrowth'] < 0) {
                $sheet->getStyle('F' . $row)->getFont()->getColor()->setARGB('FF0000'); // Red
            } else {
                $sheet->getStyle('F' . $row)->getFont()->getColor()->setARGB('008000'); // Green
            }

            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }



    private function buildTrendAnalysisSheet($sheet, $currentQuarter, $currentYear)
    {
        // Get quarterly trend data for the past 4 quarters
        $trendData = $this->getQuarterlyTrendData($currentQuarter, $currentYear, 4);

        // Add header
        $sheet->setCellValue('A1', 'QUARTERLY TREND ANALYSIS');
        $sheet->setCellValue('A2', 'Last 4 Quarters');
        $sheet->mergeCells('A1:F1');
        $sheet->mergeCells('A2:F2');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // REVENUE TREND SECTION
        $sheet->setCellValue('A4', 'REVENUE TRENDS');
        $sheet->mergeCells('A4:F4');
        $sheet->getStyle('A4')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A4')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('DDEBF7');

        // Add trend table header
        $sheet->setCellValue('A5', 'Quarter');
        $sheet->setCellValue('B5', 'Total Billed');
        $sheet->setCellValue('C5', 'Total Collected');
        $sheet->setCellValue('D5', 'Outstanding');
        $sheet->setCellValue('E5', 'Collection Rate');
        $sheet->setCellValue('F5', 'QoQ Growth');
        $sheet->getStyle('A5:F5')->getFont()->setBold(true);
        $sheet->getStyle('A5:F5')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('F2F2F2');

        // Add trend data
        $row = 6;
        $previousCollected = null;
        foreach ($trendData as $quarter => $data) {
            $sheet->setCellValue('A' . $row, $quarter);
            $sheet->setCellValue('B' . $row, '$' . number_format($data['totalBilled'], 2));
            $sheet->setCellValue('C' . $row, '$' . number_format($data['totalPaid'], 2));
            $sheet->setCellValue('D' . $row, '$' . number_format($data['totalOutstanding'], 2));
            $sheet->setCellValue('E' . $row, number_format($data['collectionRate'], 1) . '%');

            // Calculate quarter-over-quarter growth
            if ($previousCollected !== null && $previousCollected > 0) {
                $growth = (($data['totalPaid'] - $previousCollected) / $previousCollected) * 100;
                $sheet->setCellValue('F' . $row, number_format($growth, 1) . '%');

                // Apply conditional formatting for growth
                if ($growth < 0) {
                    $sheet->getStyle('F' . $row)->getFont()->getColor()->setARGB('FF0000'); // Red
                } else {
                    $sheet->getStyle('F' . $row)->getFont()->getColor()->setARGB('008000'); // Green
                }
            } else {
                $sheet->setCellValue('F' . $row, 'N/A');
            }

            $previousCollected = $data['totalPaid'];
            $row++;
        }

        // DISTRICT TREND SECTION
        $sheet->setCellValue('A' . ($row + 2), 'DISTRICT COLLECTION RATE TRENDS');
        $sheet->mergeCells('A' . ($row + 2) . ':F' . ($row + 2));
        $sheet->getStyle('A' . ($row + 2))->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A' . ($row + 2))->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('DDEBF7');

        // Get district trend data
        $districtTrends = $this->getDistrictTrendData($currentQuarter, $currentYear, 4);

        // Add district trend header
        $districtRow = $row + 3;
        $sheet->setCellValue('A' . $districtRow, 'District');
        $quarters = array_keys($trendData);
        for ($i = 0; $i < count($quarters); $i++) {
            $sheet->setCellValue(chr(66 + $i) . $districtRow, $quarters[$i]);
        }
        $sheet->setCellValue(chr(66 + count($quarters)) . $districtRow, 'Trend');
        $sheet->getStyle('A' . $districtRow . ':' . chr(66 + count($quarters)) . $districtRow)->getFont()->setBold(true);
        $sheet->getStyle('A' . $districtRow . ':' . chr(66 + count($quarters)) . $districtRow)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('F2F2F2');

        // Add district trend data
        $districtRow++;
        foreach ($districtTrends as $district => $rates) {
            $sheet->setCellValue('A' . $districtRow, $district);

            // Add collection rates for each quarter
            for ($i = 0; $i < count($quarters); $i++) {
                $quarter = $quarters[$i];
                $rate = isset($rates[$quarter]) ? $rates[$quarter] : 'N/A';

                if ($rate !== 'N/A') {
                    $sheet->setCellValue(chr(66 + $i) . $districtRow, number_format($rate, 1) . '%');

                    // Apply conditional formatting
                    if ($rate < 50) {
                        $sheet->getStyle(chr(66 + $i) . $districtRow)->getFont()->getColor()->setARGB('FF0000'); // Red
                    } elseif ($rate < 75) {
                        $sheet->getStyle(chr(66 + $i) . $districtRow)->getFont()->getColor()->setARGB('FFA500'); // Orange
                    } else {
                        $sheet->getStyle(chr(66 + $i) . $districtRow)->getFont()->getColor()->setARGB('008000'); // Green
                    }
                } else {
                    $sheet->setCellValue(chr(66 + $i) . $districtRow, 'N/A');
                }
            }

            // Calculate trend (improving, stable, declining)
            $trend = $this->calculateDistrictTrend($rates, $quarters);
            $sheet->setCellValue(chr(66 + count($quarters)) . $districtRow, $trend);

            // Apply conditional formatting for trend
            if ($trend === 'Improving') {
                $sheet->getStyle(chr(66 + count($quarters)) . $districtRow)->getFont()->getColor()->setARGB('008000'); // Green
            } elseif ($trend === 'Declining') {
                $sheet->getStyle(chr(66 + count($quarters)) . $districtRow)->getFont()->getColor()->setARGB('FF0000'); // Red
            }

            $districtRow++;
        }

        // Auto-size columns
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    /**
     * Get previous quarter stats
     */
    private function getPreviousQuarterStats($currentQuarter, $currentYear)
    {
        $quarterNumber = (int) substr($currentQuarter, 1, 1);
        $prevQuarterNumber = $quarterNumber - 1;
        $prevYear = $currentYear;

        if ($prevQuarterNumber < 1) {
            $prevQuarterNumber = 4;
            $prevYear--;
        }

        $prevQuarter = 'Q' . $prevQuarterNumber;
        return $this->getQuarterlyStats($prevQuarter, $prevYear);
    }

    /**
     * Generate executive insights based on data comparison
     */
    private function generateExecutiveInsights($currentStats, $prevStats)
    {
        $insights = [];

        // Collection rate insights
        if ($currentStats['collectionRate'] > $prevStats['collectionRate']) {
            $insights[] = "Collection rate has improved by " .
                number_format($currentStats['collectionRate'] - $prevStats['collectionRate'], 1) .
                "% compared to previous quarter, indicating improved collection efficiency.";
        } else if ($currentStats['collectionRate'] < $prevStats['collectionRate']) {
            $insights[] = "Collection rate has decreased by " .
                number_format($prevStats['collectionRate'] - $currentStats['collectionRate'], 1) .
                "% compared to previous quarter. Review collection strategies.";
        }

        // Revenue insights
        if ($currentStats['totalBilled'] > $prevStats['totalBilled']) {
            $insights[] = "Total billed amount has increased by $" .
                number_format($currentStats['totalBilled'] - $prevStats['totalBilled'], 2) .
                " compared to previous quarter, showing growth in billable revenue.";
        }

        // Outstanding amount insights
        $outstandingChange = $currentStats['totalOutstanding'] - $prevStats['totalOutstanding'];
        if ($outstandingChange > 0) {
            $insights[] = "Outstanding amount has increased by $" . number_format($outstandingChange, 2) .
                ". Focus on reducing outstanding balances to improve cash flow.";
        } else if ($outstandingChange < 0) {
            $insights[] = "Outstanding amount has decreased by $" . number_format(abs($outstandingChange), 2) .
                ", indicating improved debt collection.";
        }

        // Collection days insights
        if ($currentStats['averageCollectionDays'] < $prevStats['averageCollectionDays']) {
            $insights[] = "Average collection time has improved by " .
                number_format($prevStats['averageCollectionDays'] - $currentStats['averageCollectionDays'], 1) .
                " days, indicating more efficient payment processing.";
        }

        // Early payment insights
        if ($currentStats['earlyPaymentRate'] > $prevStats['earlyPaymentRate']) {
            $insights[] = "Early payment rate has increased by " .
                number_format($currentStats['earlyPaymentRate'] - $prevStats['earlyPaymentRate'], 1) .
                "%, suggesting effective early payment incentives.";
        }

        // Add general recommendation if insights are few
        if (count($insights) < 3) {
            $insights[] = "Consider implementing targeted collection strategies in districts with below-average performance.";
            $insights[] = "Review property management practices in areas with high outstanding balances.";
        }

        return $insights;
    }

    /**
     * Get quarterly trend data for specified number of quarters
     */
    private function getQuarterlyTrendData($currentQuarter, $currentYear, $quarterCount)
    {
        $trendData = [];
        $quarterNumber = (int) substr($currentQuarter, 1, 1);

        for ($i = 0; $i < $quarterCount; $i++) {
            $q = $quarterNumber - $i;
            $y = $currentYear;

            while ($q < 1) {
                $q += 4;
                $y--;
            }

            $quarter = 'Q' . $q;
            $trendData[$quarter . ' ' . $y] = $this->getQuarterlyStats($quarter, $y);
        }

        // Reverse to show oldest first
        return array_reverse($trendData);
    }

    /**
     * Get district performance data
     */
    private function getDistrictPerformanceData($currentQuarter, $currentYear)
    {
        // This should call the same logic used in ReportController's incomeByDistrictReport method
        $reportController = new ReportController();
        $request = new Request(['quarter' => $currentQuarter, 'year' => $currentYear]);
        $data = $reportController->getDistrictData($request);

        return $data;
    }

    /**
     * Get district trend data for collection rates
     */
    private function getDistrictTrendData($currentQuarter, $currentYear, $quarterCount)
    {
        $districts = District::all()->pluck('name');
        $trendData = [];

        foreach ($districts as $district) {
            $trendData[$district] = [];
        }

        $quarterlyTrends = $this->getQuarterlyTrendData($currentQuarter, $currentYear, $quarterCount);

        foreach ($quarterlyTrends as $quarter => $stats) {
            // Extract quarter and year
            $parts = explode(' ', $quarter);
            $q = $parts[0];
            $y = $parts[1];

            // Get district data for this quarter
            $request = new Request(['quarter' => $q, 'year' => $y]);
            $reportController = new ReportController();
            $districtData = $reportController->getDistrictData($request);

            foreach ($districtData as $district) {
                $trendData[$district['district_name']][$quarter] = $district['collectionRate'];
            }
        }

        return $trendData;
    }

    /**
     * Calculate district trend (improving, stable, declining)
     */
    private function calculateDistrictTrend($rates, $quarters)
    {
        // Need at least 2 quarters to determine trend
        if (count(array_filter($rates, function ($rate) {
            return $rate !== 'N/A';
        })) < 2) {
            return 'Insufficient Data';
        }

        $trend = 0;
        $validQuarters = [];

        // Get valid quarters and rates
        foreach ($quarters as $quarter) {
            if (isset($rates[$quarter]) && $rates[$quarter] !== 'N/A') {
                $validQuarters[] = $quarter;
            }
        }

        // Compare each quarter with the next
        for ($i = 0; $i < count($validQuarters) - 1; $i++) {
            $current = $rates[$validQuarters[$i]];
            $next = $rates[$validQuarters[$i + 1]];

            if ($next > $current) {
                $trend++;
            } elseif ($next < $current) {
                $trend--;
            }
        }

        if ($trend > 0) {
            return 'Improving';
        } elseif ($trend < 0) {
            return 'Declining';
        } else {
            return 'Stable';
        }
    }

    /**
     * Get quarter date range as formatted string
     */
    private function getQuarterDateRange($quarter, $year)
    {
        $quarterNumber = (int) substr($quarter, 1, 1);
        $startMonth = ($quarterNumber - 1) * 3 + 1;
        $endMonth = $quarterNumber * 3;

        $startDate = Carbon::createFromDate($year, $startMonth, 1)->format('M d, Y');
        $endDate = Carbon::createFromDate($year, $endMonth, 1)->endOfMonth()->format('M d, Y');

        return $startDate . ' to ' . $endDate;
    }

    /**
     * Calculate property outstanding amount
     */
    private function calculatePropertyOutstandingAmount($propertyId, $quarter, $year)
    {
        $quarterNumber = (int) substr($quarter, 1, 1);
        $startMonth = ($quarterNumber - 1) * 3 + 1;
        $endMonth = $quarterNumber * 3;
        $startDate = Carbon::createFromDate($year, $startMonth, 1)->startOfDay();
        $endDate = Carbon::createFromDate($year, $endMonth, 1)->endOfMonth()->endOfDay();

        return Invoice::whereHas('unit', function ($query) use ($propertyId) {
            $query->where('property_id', $propertyId);
        })
            ->whereBetween('invoice_date', [$startDate, $endDate])
            ->where('payment_status', 'unpaid')
            ->sum('amount');
    }
}
