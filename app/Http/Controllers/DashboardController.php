<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Unit;
use App\Models\Tenant;
use App\Models\Property;
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

        $query = Property::query();

        $transactionQuery = Transaction::query();
        if (auth()->user()->role == 'Landlord') {

            $query->whereHas('landlord', function ($q) {
                $q->where('user_id', auth()->id());
            });

            $transactionQuery->whereHas('property', function ($q) {
                $q->whereHas('landlord', function ($q) {
                    $q->where('user_id', auth()->id());
                });
            });
        }




        $noProperties =  $query->count();
        $IncreaseByThisWeek = $query->where('created_at', '>=', Carbon::now()->startOfWeek())->count();

        //total unpaid Tax
        $totalUnpaidTax = $transactionQuery->where('transaction_type', 'debit')->where('status', 'Completed')->sum('debit');
        $unpaidTaxIncreaseByThisWeek = $transactionQuery->where('transaction_type', 'debit')->where('status', 'Increase')->sum('debit');
        $totalPaidTax = $transactionQuery->where('transaction_type', 'Tax')->where('status', 'Completed')->sum('credit');
        $totalUnPaidTaxIncreaseByThisWeek = $transactionQuery->where('transaction_type', 'Tax')->where('status', 'Completed')->sum('credit');

        //rent calculation
        $totalUnpaidRent = $transactionQuery->where('transaction_type', 'Rent')->where('status', 'Completed')->sum('debit');
        $unpaidRentIncreaseByThisWeek = $transactionQuery->where('transaction_type', 'Rent')->where('status', 'Increase')->sum('debit');
        $totalPaidRent = $transactionQuery->where('transaction_type', 'Rent')->where('status', 'Completed')->sum('credit');
        $totalPaidRentIncreaseByThisWeek = $transactionQuery->where('transaction_type', 'Rent')->where('status', 'Completed')->sum('credit');


        //tenant calculation
        $totalTenants = Unit::where('is_available' ,1)->count();
        $totalTenantsIncreaseByThisWeek = Tenant::where('created_at', '>=', now()->startOfWeek())->count();

        $noIncome =  $totalPaidTax;
        $profit = $totalUnpaidTax - $noIncome;

        //last 7 days transaction
        $transactions = $transactionQuery->where('created_at', '>=', now()->subDays(7))->get();

        //property calculation
        $noNewProperties = Property::where('created_at', '>=', now()->subDays(7))->count();
        $noActiveProperties = Property::where('status', 'Active')->count();


        //transaction calculation
        $noTransaction = $transactionQuery->count();

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
