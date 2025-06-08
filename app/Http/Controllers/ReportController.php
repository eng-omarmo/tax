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
    public function incomeByDistrictReport()
    {
        $districts = Property::distinct()->pluck('district_id');
        $districtData = [];

        foreach ($districts as $district) {
            $invoices = Invoice::whereHas('unit.property', function ($query) use ($district) {
                $query->where('district_id', $district);
            })->get();

            $totalRevenue = $invoices->sum('amount');
            $totalPaid = $invoices->where('payment_status', 'Paid')->sum('amount');
            $totalOutstanding = $invoices->where('payment_status', '!=', 'Paid')->sum('amount');
            $collectionRate = $totalRevenue > 0 ? ($totalPaid / $totalRevenue) * 100 : 0;

            $districtData[] = [
                'district_name' => $this->getDistrictName($district),
                'totalRevenue' => $totalRevenue,
                'totalPaid' => $totalPaid,
                'totalOutstanding' => $totalOutstanding,
                'collectionRate' => $collectionRate,
            ];
        }
        $currentYear = '2023';
        $currentQuarter = 'Q2';

        return view(
            'reports.income_by_district_report',
            [
                'incomeByDistrict' => $districtData,
                'currentYear' => $currentYear,
                'currentQuarter' => $currentQuarter,
            ]
        );
    }

    private function getDistrictName($districtId)
    {

        $district = District::find($districtId);

        return $district ? $district->name : 'Unknown';
    }
}
