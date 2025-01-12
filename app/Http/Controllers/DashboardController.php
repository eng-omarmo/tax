<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Tenant;
use App\Models\Transaction;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {

        return view('dashboard/index', []);
    }

    public function index2()
    {
        $noProperties =   Property::count();
        $IncreaseByThisWeek = Property::where('created_at', '>=', \Carbon\Carbon::now()->startOfWeek())->count();

        //total unpaid Tax
        $totalUnpaidTax = Transaction::where('transaction_type', 'Tax')->where('status', 'Pending')->sum('debit');
        $unpaidTaxIncreaseByThisWeek = Transaction::where('transaction_type', 'Tax')->where('status', 'Increase')->sum('debit');
        $totalPaidTax = Transaction::where('transaction_type', 'Tax')->where('status', 'Completed')->sum('credit');
        $totalUnPaidTaxIncreaseByThisWeek = Transaction::where('transaction_type', 'Tax')->where('status', 'Completed')->sum('credit');

        //rent calculation
        $totalUnpaidRent = Transaction::where('transaction_type', 'Rent')->where('status', 'Pending')->sum('debit');
        $unpaidRentIncreaseByThisWeek = Transaction::where('transaction_type', 'Rent')->where('status', 'Increase')->sum('debit');
        $totalPaidRent = Transaction::where('transaction_type', 'Rent')->where('status', 'Completed')->sum('credit');
        $totalPaidRentIncreaseByThisWeek = Transaction::where('transaction_type', 'Rent')->where('status', 'Completed')->sum('credit');


        //tenant calculation
        $totalTenants = Tenant::count();
        $totalTenantsIncreaseByThisWeek = Tenant::where('created_at', '>=', \Carbon\Carbon::now()->startOfWeek())->count();

        $noIncome =  $totalPaidTax;
        $profit = $totalUnpaidTax - $noIncome;

        //last 7 days transaction
        $transactions = Transaction::where('created_at', '>=', \Carbon\Carbon::now()->subDays(7))->get();

        //property calculation
        $noNewProperties = Property::where('created_at', '>=', \Carbon\Carbon::now()->subDays(7))->count();
        $noActiveProperties = Property::where('status', 'Active')->count();


        //transaction calculation
        $noTransaction = Transaction::count();

        return view('dashboard/index2', [
            'noProperties' => $noProperties,
            'IncreaseByThisWeek' => $IncreaseByThisWeek,

            'totalUnpaidTax' => $totalUnpaidTax,
            'totalPaidTax' => $totalPaidTax,
            'unpaidTaxIncreaseByThisWeek' => $unpaidTaxIncreaseByThisWeek,
            'totalPaidTaxIncreaseByThisWeek' => $totalUnPaidTaxIncreaseByThisWeek,
            'totalUnpaidRent' => $totalUnpaidRent,
            'totalPaidRent' => $totalPaidRent,
            'unpaidRentIncreaseByThisWeek' => $unpaidRentIncreaseByThisWeek,
            'totalPaidRentIncreaseByThisWeek' => $totalPaidRentIncreaseByThisWeek,
            'totalTenants' => $totalTenants,
            'totalTenantsIncreaseByThisWeek' => $totalTenantsIncreaseByThisWeek,
            'noTransaction' => $noTransaction,
            'noIncome' => $noIncome,
            'profit' => $profit,
            'transactions' => $transactions,
            'noNewProperties' => $noNewProperties,
            'noActiveProperties' => $noActiveProperties
        ]);
    }

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
