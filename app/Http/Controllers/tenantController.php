<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class tenantController extends Controller
{


    public function index(Request $request)
    {
        $query = Tenant::query();
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        $tenants = $query->paginate(5);

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

            Tenant::create([
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

            return redirect()->route('tenant.index')->with('success', 'Tenant added successfully!');
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
            return redirect()->route('tenant.index')->with('error', $th->getMessage());
        }
    }

    public function edit($id)
    {
        $tenant = Tenant::query()->find($id);
        return view('tenant.edit', compact('tenant'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tenant_name' => 'required|string|max:255',
            'tenant_phone' => 'required|string',
            'rental_start_date' => 'required|date|before_or_equal:rental_end_date',
            'rental_end_date' => 'nullable|date|after:rental_start_date',
            'reference' => 'required|string|max:255',
            'rent_amount' => 'required|numeric|min:0',
            'tax_fee' => 'nullable|numeric|min:0',
            'status' => 'required|in:Active,Inactive',
        ]);
        Tenant::query()->find($id)->update($request->all());
        return redirect()->route('tenant.index')->with('success', 'Tenant updated successfully.');
    }

    public function destroy($id)
    {
        Tenant::query()->find($id)->delete();
        return redirect()->route('tenant.index')->with('success', 'Tenant deleted successfully.');
    }
}