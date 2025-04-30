<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class paymentMethodController extends Controller
{
    //

    public function index()
    {
        $paymentMethods = PaymentMethod::paginate(10);
        return view('paymentMethod.index', compact('paymentMethods'));
    }

    public function create()
    {
        return view('paymentMethod.create');
    }

    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required|string|max:255|unique:payment_methods,name',
            'status' => 'required|in:0,1',
        ]);


        PaymentMethod::create([
            'name' => $request->name,
            'status' => $request->status,
        ]);

        return redirect()->route('payment.method.index')->with('success', 'Payment method created successfully.');
    }

    public function edit($id)
    {
        $paymentMethod  = PaymentMethod::where('id', $id)->first();

        return view('paymentMethod.edit', [
            'paymentMethod' =>   $paymentMethod
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:payment_methods,name',
            'status' => 'required|in:0,1',
        ]);
        $paymentMethod  = PaymentMethod::where('id', $id)->first();
        $paymentMethod->update([
            'name' => $request->name,
            'status' => $request->status,
        ]);
        return redirect()->route('payment.method.index')->with('success', 'Payment method created successfully.');
    }

    public function destroy($id)
    {
        $paymentMethod  = PaymentMethod::where('id', $id)->first();
        $paymentMethod->delete();
        return redirect()->route('payment.method.index')->with('success', 'Payment method Deleted successfully.');
    }
}
