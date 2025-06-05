<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Carbon\Carbon;
use App\Models\Unit;
use App\Models\Landlord;
use App\Models\Payment;
use App\Models\Property;
use Illuminate\Http\Request;

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
        $unpaidUnits = Invoice::with('unit')->where('payment_status', 'Paid')->whereDate('created_at', $today)->get();

        // Get today's paid units
        $paidUnits = Invoice::with('unit')
        ->where('payment_status', 'Paid')
        ->whereDate('updated_at', $today)
        ->get();

        // Get today's registered landlords
        $landlords = Landlord::whereDate('created_at', $today)->get();

        $payments= Payment::with('invoice')
        ->where('status', 'Completed')
        ->whereDate('updated_at', $today)->get();

        return view('reports.today_report', compact('properties', 'unpaidUnits', 'paidUnits', 'landlords', 'payments'));
    }
}
