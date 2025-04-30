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
        return view('account.create', compact('paymentMethods'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|exists:payment_methods,id',
            'account_number' => 'nullable|string|max:255',
            'status' => 'required|in:active,Inactive',

        ]);

        $initialBalance = $request->opening_balance ?? 0;

        try {
            Accounts::create([
                'payment_method_id' => $request->payment_method,
                'account_number' => $request->account_number,
                'balance' => $initialBalance,
                'status' => $request->status,
            ]);

            return redirect()->route('account.index')->with('success', 'Account created successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Something went wrong: ' . $th->getMessage());
        }
    }

    public function edit($id)
    {
        $account = Accounts::findOrFail($id);
        $paymentMethods = PaymentMethod::all();
        return view('account.edit', compact('account', 'paymentMethods'));
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'payment_method' => 'required|exists:payment_methods,id',
            'account_number' => 'nullable|string|max:255',
            'status' => 'required|in:active,Inactive',

        ]);
        $account = Accounts::findOrFail($id);
        $account->update([
            'payment_method_id' => $request->payment_method,
            'account_number' => $request->account_number,
            'status' => $request->status,
        ]);
        return redirect()->route('account.index')->with('success', 'Account updated successfully.');
    }
    public function destroy($id)
    {
        $account = Accounts::findOrFail($id);
        $account->delete();
        return redirect()->route('account.index')->with('success', 'Account deleted successfully.');
    }
}
