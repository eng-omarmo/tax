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
        if($request->filled('search')){
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
        if($request->filled('status')){
            $query->where('status', $request->status);
        }
        if($request->filled('monetering_status')){
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
            'zone' => 'nullable|string|max:255',
            'house_type' => 'nullable|string|max:255',
            'house_rent' => 'nullable|numeric',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'status' => 'required|in:active,inactive',
        ]);
        $checkProperty = Property::where('property_name', $request->property_name)->first();
        if ($checkProperty) {
            return back()->with('error', 'Property name already exists.');
        }
        Property::create($request->all());
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