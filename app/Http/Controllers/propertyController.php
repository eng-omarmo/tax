<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;

class propertyController extends Controller
{

    public function index()
    {
        $properties = Property::paginate(10);
        return view('property.index', compact('properties'));
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
}
