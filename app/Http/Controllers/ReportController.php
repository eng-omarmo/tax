<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Unit;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\District;
use App\Models\Landlord;
use App\Models\Property;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Services\TimeService;

class ReportController extends Controller
{
    /**
     * Display today's report with registered properties, unpaid units, paid units, and landlords
     */
    public function todayReport()
    {
        $today = Carbon::today();

        // Get today's registered properties
        $properties = Property::whereDate('created_at', $today)->get();

        // Get today's unpaid units
        $unpaidUnits = Invoice::with('unit')->where('payment_status', 'Pending')->whereDate('created_at', $today)->get();

        // Get today's paid units
        $paidUnits = Invoice::with('unit')
            ->where('payment_status', 'Paid')
            ->whereDate('updated_at', $today)
            ->get();

        // Get today's registered landlords
        $landlords = Landlord::whereDate('created_at', $today)->get();

        $payments = Payment::with('invoice')
            ->where('status', 'Completed')
            ->whereDate('updated_at', $today)->get();

        return view('reports.today_report', compact('properties', 'unpaidUnits', 'paidUnits', 'landlords', 'payments'));
    }

    /**
     * Display income report with detailed financial information
     */
    public function incomeReport(Request $request)
    {
        // Get time period (default to current quarter)
        $timeService = new TimeService();
        $currentQuarter = $request->input('quarter', $timeService->currentQuarter());
        $currentYear = $request->input('year', $timeService->currentYear());

        // Get all payments for the selected period
        $startDate = $this->getQuarterStartDate($currentQuarter, $currentYear);
        $endDate = $this->getQuarterEndDate($currentQuarter, $currentYear);

        // Get payments data
        $payments = Payment::with(['invoice.unit.property', 'paymentDetail'])
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->where('status', 'completed')
            ->get();

        // Calculate summary statistics
        $totalRevenue = $payments->sum('amount');
        $propertyRevenue = $payments->groupBy('invoice.unit.property_id')
            ->map(function ($propertyPayments) {
                return [
                    'property' => $propertyPayments->first()->invoice->unit->property ?? null,
                    'total' => $propertyPayments->sum('amount'),
                    'count' => $propertyPayments->count(),
                ];
            })
            ->sortByDesc('total')
            ->values();

        // Get invoices data for the period
        $invoices = Invoice::with('unit.property')
            ->whereBetween('invoice_date', [$startDate, $endDate])
            ->get();

        $totalBilled = $invoices->sum('amount');
        $totalPaid = $invoices->where('payment_status', 'Paid')->sum('amount');
        $totalOutstanding = $invoices->where('payment_status', '!=', 'Paid')->sum('amount');
        $collectionRate = $totalBilled > 0 ? ($totalPaid / $totalBilled) * 100 : 0;

        // Get monthly breakdown
        $monthlyRevenue = $payments->groupBy(function ($payment) {
            return Carbon::parse($payment->payment_date)->format('F');
        })
            ->map(function ($monthPayments) {
                return $monthPayments->sum('amount');
            });

        // Get payment methods breakdown
        $paymentMethods = $payments->groupBy('payment_method')
            ->map(function ($methodPayments) {
                return [
                    'total' => $methodPayments->sum('amount'),
                    'count' => $methodPayments->count(),
                ];
            });

        return view('reports.income_report', compact(
            'payments',
            'totalRevenue',
            'propertyRevenue',
            'totalBilled',
            'totalPaid',
            'totalOutstanding',
            'collectionRate',
            'monthlyRevenue',
            'paymentMethods',
            'currentQuarter',
            'currentYear'
        ));
    }

    /**
     * Helper method to get quarter start date
     */
    private function getQuarterStartDate($quarter, $year)
    {
        $quarterNumber = (int) substr($quarter, 1, 1);
        $month = ($quarterNumber - 1) * 3 + 1;
        return Carbon::createFromDate($year, $month, 1)->startOfDay();
    }

    /**
     * Helper method to get quarter end date
     */
    private function getQuarterEndDate($quarter, $year)
    {
        $quarterNumber = (int) substr($quarter, 1, 1);
        $month = $quarterNumber * 3;
        return Carbon::createFromDate($year, $month, 1)->endOfMonth()->endOfDay();
    }

    //income by distrct report
    //income by distrct report
    public function incomeByDistrictReport(Request $request)
    {
        // Get time period (default to current quarter)
        $timeService = new TimeService();
        $currentQuarter = $request->input('quarter', $timeService->currentQuarter());
        $currentYear = $request->input('year', $timeService->currentYear());

        // Get quarter date range
        $quarterNumber = (int) substr($currentQuarter, 1, 1);
        $startMonth = ($quarterNumber - 1) * 3 + 1;
        $endMonth = $quarterNumber * 3;
        $startDate = Carbon::createFromDate($currentYear, $startMonth, 1)->startOfDay();
        $endDate = Carbon::createFromDate($currentYear, $endMonth, 1)->endOfMonth()->endOfDay();

        $districts = District::all();
        $districtData = [];
        $totalSystemRevenue = 0;
        $totalSystemPaid = 0;
        $totalSystemOutstanding = 0;

        // Performance metrics
        $bestCollectionRate = ['district' => null, 'rate' => 0];
        $worstCollectionRate = ['district' => null, 'rate' => 100];
        $highestRevenue = ['district' => null, 'amount' => 0];

        // Monthly trends data
        $monthlyTrends = [];
        for ($i = 0; $i < 3; $i++) {
            $month = $startMonth + $i;
            if ($month <= 12) {
                $monthName = Carbon::createFromDate($currentYear, $month, 1)->format('F');
                $monthlyTrends[$monthName] = [];
            }
        }

        foreach ($districts as $district) {
            // Get properties in this district
            $properties = Property::where('district_id', $district->id)->pluck('id');

            // Skip if no properties in this district
            if ($properties->isEmpty()) {
                continue;
            }

            // Get units in these properties
            $units = Unit::whereIn('property_id', $properties)->pluck('id');

            // Get invoices for these units in the selected quarter
            $invoices = Invoice::whereIn('unit_id', $units)
                ->whereBetween('invoice_date', [$startDate, $endDate])
                ->get();

            // Calculate district metrics
            $totalRevenue = $invoices->sum('amount');
            $totalPaid = $invoices->where('payment_status', 'Paid')->sum('amount');
            $totalOutstanding = $invoices->where('payment_status', '!=', 'Paid')->sum('amount');
            $collectionRate = $totalRevenue > 0 ? ($totalPaid / $totalRevenue) * 100 : 0;
            $propertyCount = count($properties);
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

            // Get monthly breakdown for this district
            foreach ($monthlyTrends as $month => $data) {
                $monthNumber = Carbon::parse("1 $month $currentYear")->month;
                $monthStart = Carbon::createFromDate($currentYear, $monthNumber, 1)->startOfDay();
                $monthEnd = Carbon::createFromDate($currentYear, $monthNumber, 1)->endOfMonth()->endOfDay();

                $monthInvoices = Invoice::whereIn('unit_id', $units)
                    ->whereBetween('invoice_date', [$monthStart, $monthEnd])
                    ->get();

                $monthlyTrends[$month][$district->name] = $monthInvoices->where('payment_status', 'Paid')->sum('amount');
            }

            // Store district data
            $districtData[] = [
                'district_id' => $district->id,
                'district_name' => $district->name,
                'totalRevenue' => $totalRevenue,
                'totalPaid' => $totalPaid,
                'totalOutstanding' => $totalOutstanding,
                'collectionRate' => $collectionRate,
                'propertyCount' => $propertyCount,
                'unitCount' => $unitCount,
                'invoiceCount' => $invoiceCount,
                'paidInvoiceCount' => $paidInvoiceCount,
                'revenueGrowth' => $revenueGrowth
            ];

            // Update system totals
            $totalSystemRevenue += $totalRevenue;
            $totalSystemPaid += $totalPaid;
            $totalSystemOutstanding += $totalOutstanding;

            // Update performance metrics
            if ($collectionRate > $bestCollectionRate['rate'] && $totalRevenue > 0) {
                $bestCollectionRate['district'] = $district->name;
                $bestCollectionRate['rate'] = $collectionRate;
            }

            if ($collectionRate < $worstCollectionRate['rate'] && $totalRevenue > 0) {
                $worstCollectionRate['district'] = $district->name;
                $worstCollectionRate['rate'] = $collectionRate;
            }

            if ($totalPaid > $highestRevenue['amount']) {
                $highestRevenue['district'] = $district->name;
                $highestRevenue['amount'] = $totalPaid;
            }
        }

        // Sort districts by collection rate (descending)
        usort($districtData, function ($a, $b) {
            return $b['collectionRate'] <=> $a['collectionRate'];
        });

        // Calculate system-wide collection rate
        $systemCollectionRate = $totalSystemRevenue > 0 ? ($totalSystemPaid / $totalSystemRevenue) * 100 : 0;

        // Generate recommendations for each district
        foreach ($districtData as &$district) {
            $district['recommendations'] = $this->generateDistrictRecommendations($district, $systemCollectionRate);
        }

        return view(
            'reports.income_by_district_report',
            [
                'incomeByDistrict' => $districtData,
                'currentYear' => $currentYear,
                'currentQuarter' => $currentQuarter,
                'systemStats' => [
                    'totalRevenue' => $totalSystemRevenue,
                    'totalPaid' => $totalSystemPaid,
                    'totalOutstanding' => $totalSystemOutstanding,
                    'collectionRate' => $systemCollectionRate
                ],
                'performanceMetrics' => [
                    'bestCollectionRate' => $bestCollectionRate,
                    'worstCollectionRate' => $worstCollectionRate,
                    'highestRevenue' => $highestRevenue
                ],
                'monthlyTrends' => $monthlyTrends
            ]
        );
    }

    /**
     * Generate recommendations for improving district performance
     */
    private function generateDistrictRecommendations($district, $systemAvgRate)
    {
        $recommendations = [];

        // Collection rate recommendations
        if ($district['collectionRate'] < $systemAvgRate) {
            $recommendations[] = "Hirgeli ol’olayaal aruurin hormarsan soo  lagu hagaajinayo heerka daqliqa canshuurta degmadan.";
            $recommendations[] = "Dib u eeg habraacyada maareynta guryaha ee degmadan.";
        }

        // Revenue growth recommendations
        if ($district['revenueGrowth'] < 0) {
            $recommendations[] = " Baadh hoos u dhaca dakhliga marka la barbar dhigo rubucii hore.";
            $recommendations[] = "Ka samey dib u qiimeynta guryaha ku yaalo degmadan si daqli loo kordhiyo.";
        }

        // Property density recommendations
        if ($district['propertyCount'] > 0 && $district['unitCount'] / $district['propertyCount'] < 5) {
            $recommendations[] = "Dib u eeegis ku samee albaabada ku koobanyahay guryahaas.";
        }

        // Outstanding amount recommendations
        if ($district['totalOutstanding'] > $district['totalPaid']) {
            $recommendations[] = "Mudnaanta sii ururinta lacagaha weli lagu leeyahay degmadan";
            $recommendations[] = "Hirgeli qorshayaal lacag aruurin si loo soo xareeyo daqliga canshuurta kumaqan degmadan.";
        }

        // If performing well
        if ($district['collectionRate'] > $systemAvgRate && $district['revenueGrowth'] > 0) {
            $recommendations[] = "Sii wad heerka waxqabadka hadda jira, kana fiirso in hab-raaca degmadan loo adeegsado tusaale ahaan degmooyin kale";
        }

        return $recommendations;
    }

    /**
     * Get district data for reports and exports
     * This extracts the district data logic from incomeByDistrictReport
     * so it can be reused by the export functionality
     */
    public function getDistrictData(Request $request)
    {
        // Get time period (default to current quarter)
        $timeService = new TimeService();
        $currentQuarter = $request->input('quarter', $timeService->currentQuarter());
        $currentYear = $request->input('year', $timeService->currentYear());

        // Get quarter date range
        $quarterNumber = (int) substr($currentQuarter, 1, 1);
        $startMonth = ($quarterNumber - 1) * 3 + 1;
        $endMonth = $quarterNumber * 3;
        $startDate = Carbon::createFromDate($currentYear, $startMonth, 1)->startOfDay();
        $endDate = Carbon::createFromDate($currentYear, $endMonth, 1)->endOfMonth()->endOfDay();

        $districts = District::all();
        $districtData = [];
        $totalSystemRevenue = 0;
        $totalSystemPaid = 0;
        $totalSystemOutstanding = 0;

        // Performance metrics
        $bestCollectionRate = ['district' => null, 'rate' => 0];
        $worstCollectionRate = ['district' => null, 'rate' => 100];
        $highestRevenue = ['district' => null, 'amount' => 0];

        // Monthly trends data
        $monthlyTrends = [];
        for ($i = 0; $i < 3; $i++) {
            $month = $startMonth + $i;
            if ($month <= 12) {
                $monthName = Carbon::createFromDate($currentYear, $month, 1)->format('F');
                $monthlyTrends[$monthName] = [];
            }
        }

        foreach ($districts as $district) {
            // Get properties in this district
            $properties = Property::where('district_id', $district->id)->pluck('id');

            // Skip if no properties in this district
            if ($properties->isEmpty()) {
                continue;
            }

            // Get units in these properties
            $units = Unit::whereIn('property_id', $properties)->pluck('id');

            // Get invoices for these units in the selected quarter
            $invoices = Invoice::whereIn('unit_id', $units)
                ->whereBetween('invoice_date', [$startDate, $endDate])
                ->get();

            // Calculate district metrics
            $totalRevenue = $invoices->sum('amount');
            $totalPaid = $invoices->where('payment_status', 'Paid')->sum('amount');
            $totalOutstanding = $invoices->where('payment_status', '!=', 'Paid')->sum('amount');
            $collectionRate = $totalRevenue > 0 ? ($totalPaid / $totalRevenue) * 100 : 0;
            $propertyCount = count($properties);
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

            // Get monthly breakdown for this district
            foreach ($monthlyTrends as $month => $data) {
                $monthNumber = Carbon::parse("1 $month $currentYear")->month;
                $monthStart = Carbon::createFromDate($currentYear, $monthNumber, 1)->startOfDay();
                $monthEnd = Carbon::createFromDate($currentYear, $monthNumber, 1)->endOfMonth()->endOfDay();

                $monthInvoices = Invoice::whereIn('unit_id', $units)
                    ->whereBetween('invoice_date', [$monthStart, $monthEnd])
                    ->get();

                $monthlyTrends[$month][$district->name] = $monthInvoices->where('payment_status', 'Paid')->sum('amount');
            }

            // Store district data
            $districtData[] = [
                'district_id' => $district->id,
                'district_name' => $district->name,
                'totalRevenue' => $totalRevenue,
                'totalPaid' => $totalPaid,
                'totalOutstanding' => $totalOutstanding,
                'collectionRate' => $collectionRate,
                'propertyCount' => $propertyCount,
                'unitCount' => $unitCount,
                'invoiceCount' => $invoiceCount,
                'paidInvoiceCount' => $paidInvoiceCount,
                'revenueGrowth' => $revenueGrowth
            ];

            // Update system totals
            $totalSystemRevenue += $totalRevenue;
            $totalSystemPaid += $totalPaid;
            $totalSystemOutstanding += $totalOutstanding;

            // Update performance metrics
            if ($collectionRate > $bestCollectionRate['rate'] && $totalRevenue > 0) {
                $bestCollectionRate['district'] = $district->name;
                $bestCollectionRate['rate'] = $collectionRate;
            }

            if ($collectionRate < $worstCollectionRate['rate'] && $totalRevenue > 0) {
                $worstCollectionRate['district'] = $district->name;
                $worstCollectionRate['rate'] = $collectionRate;
            }

            if ($totalPaid > $highestRevenue['amount']) {
                $highestRevenue['district'] = $district->name;
                $highestRevenue['amount'] = $totalPaid;
            }
        }

        // Sort districts by collection rate (descending)
        usort($districtData, function ($a, $b) {
            return $b['collectionRate'] <=> $a['collectionRate'];
        });

        // Calculate system-wide collection rate
        $systemCollectionRate = $totalSystemRevenue > 0 ? ($totalSystemPaid / $totalSystemRevenue) * 100 : 0;

        // Generate recommendations for each district
        foreach ($districtData as &$district) {
            $district['recommendations'] = $this->generateDistrictRecommendations($district, $systemCollectionRate);
        }

        return $districtData;
    }

    public function quaterly()
    {

//clean this code get the function in dashboard controller then use that as reusable instead rewrittign the whole things againm 
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

        return view('reports.quater', compact('quarterSummaries'));
    }
}
