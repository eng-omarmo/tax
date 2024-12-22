<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;

class propertyController extends Controller
{

    public function index(Request $request)
    {

        $statuses = Property::pluck('status')->unique();

        $monitoringStatuses = Property::pluck('monitoring_status')->unique();
        //app filter
        $query  = Property::query();
        if ($request->filled('search')) {
            $query->where('property_name', 'like', '%' . $request->search . '%')
                ->orWhere('property_phone', 'like', '%' . $request->search . '%')
                ->orWhere('nbr', 'like', '%' . $request->search . '%')
                ->orWhere('house_code', 'like', '%' . $request->search . '%')
                ->orWhere('tenant_name', 'like', '%' . $request->search . '%')
                ->orWhere('tenant_phone', 'like', '%' . $request->search . '%')
                ->orWhere('branch', 'like', '%' . $request->search . '%')
                ->orWhere('zone', 'like', '%' . $request->search . '%')
                ->orWhere('house_type', 'like', '%' . $request->search . '%')
                ->orWhere('status', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('monetering_status')) {
            $query->where('monitoring_status', $request->monetering_status);
        }
        $properties = $query->paginate(10);
        return view('property.index', compact('properties', 'statuses', 'monitoringStatuses'));
    }

    public function create()
    {
        return view('property.create');
    }

    public function store(Request $request)
    {

        $request->validate([
            'property_name' => 'required|string|max:255',
            'property_phone' => 'nullable|string|max:15',
            'nbr' => 'nullable|string|max:100',
            'house_code' => 'nullable|string|max:50',
            'tenant_name' => 'nullable|string|max:255',
            'tenant_phone' => 'nullable|string|max:15',
            'branch' => 'nullable|string|max:255',
            'quarterly_tax_fee' => 'nullable|numeric',
            'yearly_tax_fee' => 'nullable|numeric',
            'zone' => 'nullable|string|max:255',
            'house_type' => 'nullable|string|max:255',
            'house_rent' => 'nullable|numeric',
            'latitude' => 'required|numeric',
            'quarterly_tax_fee' => 'required|numeric',
            'yearly_tax_fee' => 'required|numeric',
            'longitude' => 'required|numeric',
            'dalal_company_name' => 'nullable|string|max:255',
            'is_owner' => 'required|in:Yes,No',
            'designation' => 'nullable|string|max:255',
            'monitoring_status' => 'required|in:Pending,Approved',
            'status' => 'required|in:Active,Inactive',
        ]);
        $checkProperty = Property::where('property_name', $request->property_name)->first();
        if ($checkProperty) {
            return back()->with('error', 'Property name already exists.');
        }

        Property::create([
            'property_name' => $request->property_name,
            'property_phone' => $request->property_phone,
            'nbr' => $request->nbr,
            'house_code' => $request->house_code,
            'tenant_name' => $request->tenant_name,
            'tenant_phone' => $request->tenant_phone,
            'branch' => $request->branch,
            'zone' => $request->zone,
            'house_type' => $request->house_type,
            'house_rent' => $request->house_rent,
            'quarterly_tax_fee' => $request->quarterly_tax_fee,
            'yearly_tax_fee' => $request->yearly_tax_fee,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'is_owner' => $request->is_owner,
            'designation' => $request->designation,
            'dalal_company_name' => $request->dalal_company_name,

            'monitoring_status' => $request->monitoring_status,
            'status' => $request->status,
        ]);
        return redirect()->route('property.index')->with('success', 'Property registered successfully.');
    }

    public function edit($id)
    {
        $property = Property::findorFail($id);

        return view('property.edit', compact('property'));
    }
    public function update(Request $request, Property $property)
    {
        $request->validate([
            'property_name' => 'required|string|max:255',
            'property_phone' => 'nullable|string|max:15',
            'nbr' => 'nullable|string|max:100',
            'house_code' => 'nullable|string|max:50',
            'tenant_name' => 'nullable|string|max:255',
            'tenant_phone' => 'nullable|string|max:15',
            'branch' => 'nullable|string|max:255',
            'zone' => 'nullable|string|max:255',
            'house_type' => 'nullable|string|max:255',
            'house_rent' => 'nullable|numeric',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'status' => 'required|in:active,inactive',
        ]);
        $property->update($request->all());
        return redirect()->route('property.index')->with('success', 'Property updated successfully.');
    }

    public function destroy($id)
    {
        Property::query()->find($id)->delete();
        return redirect()->route('property.index')->with('success', 'Property deleted successfully.');
    }
}
