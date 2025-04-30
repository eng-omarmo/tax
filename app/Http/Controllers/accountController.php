<?php

namespace App\Http\Controllers;

use App\Models\Accounts;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use PhpParser\Builder\Function_;
use PhpParser\Node\Expr\FuncCall;

class accountController extends Controller
{

    public function index()
    {
        $accounts = Accounts::orderby('id', 'desc')->paginate(10);
        return view(
            'account.index',
            compact('accounts')
        );
    }

     public function create()
     {

        $paymentMethods    = PaymentMethod::all();
        return view('account.create', compact('paymentMethods') );
     }

}
