<?php

namespace App\Http\Controllers;

use App\Models\TaxRate;
use Illuminate\Http\Request;

class taxRateController extends Controller
{
    //

    public function index()
    {
        //

        $taxRates = TaxRate::paginate(5);

        return view('tax.rate.index',['taxRates'=>$taxRates]);
    }

    public function create()
    {
        return view('tax.rate.create');
    }
}
