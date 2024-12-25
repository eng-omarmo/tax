<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\Property;
use App\Models\Transaction;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class tenantController extends Controller
{


    public function index(Request $request)
    {
        $query = Tenant::query();
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        $tenants = $query->paginate(5);

        foreach ($tenants as $tenant) {
            $tenant->rentTotalBalance = $tenant->transactions()->where('transaction_type', 'Rent')->sum('debit');
            $tenant->taxTotalBalance = $tenant->transactions()->where('transaction_type', 'Tax')->sum('debit');
        }

        return view('tenant.index', compact('tenants'));
    }
    public function search(Request $request)
    {
        $request->validate([
            'search_property' => 'required|string',
        ]);

        $search = trim($request->input('search_property'));

        $property = Property::where('property_phone', 'like', '%' . $search . '%')->first();
        if (!$property) {
            return back()->with('error', 'Property not found.');
        }

        return view('tenant.create', ['property' => $property]);
    }



    public function create()
    {
        return view('tenant.create');
    }

    public function store(Request $request)
    {

        try {
            $validatedData = $request->validate([
                'property_id' => 'required|exists:properties,id',
                'tenant_name' => 'required|string|max:255',
                'tenant_phone' => 'required|string',
                'rental_start_date' => 'required|date|before_or_equal:rental_end_date',
                'rental_end_date' => 'nullable|date|after:rental_start_date',
                'reference' => 'required|string|max:255',
                'rent_amount' => 'required|numeric|min:0',
                'tax_fee' => 'nullable|numeric|min:0',
                'status' => 'required|in:Active,Inactive',
            ]);
            DB::beginTransaction();
            $tenant =  Tenant::create([
                'property_id' => $validatedData['property_id'],
                'tenant_name' => $validatedData['tenant_name'],
                'tenant_phone' => $validatedData['tenant_phone'],
                'rental_start_date' => $validatedData['rental_start_date'],
                'rental_end_date' => $validatedData['rental_end_date'],
                'reference' => $validatedData['reference'],
                'rent_amount' => $validatedData['rent_amount'],
                'tax_fee' => $validatedData['tax_fee'],
                'status' => $validatedData['status'],
            ]);
            $this->createTransaction($tenant);
            $this->createTransactionForTaxFee($tenant);
            DB::commit();
            return redirect()->route('tenant.index')->with('success', 'Tenant added successfully!');
        } catch (\Throwable $th) {

            Log::info($th->getMessage());
            return redirect()->route('tenant.index')->with('error', $th->getMessage());
        }
    }


    public function edit($id)
    {
        $tenant = Tenant::query()->find($id);
        $properties = Property::select('property_name', 'id')->get();
        $balance = $this->getBalance($tenant->id);
        return view('tenant.edit', compact('tenant', 'properties', 'balance'));
    }

    public function getBalance($tenantId)
    {
        $tenant = Tenant::with('transactions')->findOrFail($tenantId);
        return  $tenant->calculateBalance();
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tenant_name' => 'required|string|max:255',
            'tenant_phone' => 'required|string',
            'rental_start_date' => 'required|date|before_or_equal:rental_end_date',
            'rental_end_date' => 'nullable|date|after:rental_start_date',
            'reference' => 'required|string|max:255',
            'tax_fee' => 'nullable|numeric|min:0',
            'status' => 'required|in:Active,Inactive',
        ]);
        Tenant::query()->find($id)->update($request->all());
        return redirect()->route('tenant.index')->with('success', 'Tenant updated successfully.');
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            Tenant::query()->find($id)->delete();
            Transaction::where('tenant_id', $id)->delete();
            return redirect()->route('tenant.index')->with('success', 'Tenant deleted successfully.');
            DB::commit();
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
            return redirect()->route('tenant.index')->with('error', $th->getMessage());
        }
    }

    private function createTransaction($tenant)
    {
        return Transaction::create([
            'tenant_id' => $tenant->id,
            'property_id' => $tenant->property_id,
            'transaction_type' => 'Rent',
            'amount' => $tenant->rent_amount,
            'description' => 'Tenant Rent',
            'credit' => 0,
            'debit' => $tenant->rent_amount,
            'status' => 'Pending',
        ]);
    }

    private function createTransactionForTaxFee($tenant)
    {
        return Transaction::create([
            'tenant_id' => $tenant->id,
            'transaction_type' => 'Tax',
            'amount' => $tenant->tax_fee,
            'description' => 'Tenant Tax Fee',
            'credit' => 0,
            'debit' => $tenant->tax_fee,
            'status' => 'Pending',
        ]);
    }
}
