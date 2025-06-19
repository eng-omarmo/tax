<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Landlord;
use App\Models\Property;
use Illuminate\Http\Request;

class landlordController extends Controller
{
    //

    public function index()
    {
        $query = Landlord::query();
        if (request()->has('search') && request()->search) {
            $query->where('name', 'like', '%' . request()->search . '%');
        }
        $landlords = $query->orderby('id', 'desc')->paginate(10);
        return view('landlord.index', [
            'landlords' => $landlords
        ]);
    }

    public function create()
    {
        return view('landlord.create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:15|unique:users,phone|unique:landlords,phone_number',
            'email' => 'required|email|unique:users,email|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
        $image = $request->file('image');
        $imageName = time() . '.' . $image->getClientOriginalExtension();

        $path = $image->storeAs('uploads', $imageName, 'public');
        $landlord =  Landlord::create([
            'name' => $request->name,
            'address' => $request->address,
            'phone_number' => $request->phone,
            'email' => $request->email,
            'profile_image' => $path,
    'user_id' => auth()->user()->id

        ]);


        // Check if the user wants to continue to property registration
        if ($request->has('continue_to_property') && $request->continue_to_property == 1) {
            return redirect()->route('property.create', $landlord->id)
                ->with('success', 'Landlord registered successfully. Now register a property.');
        }

        return redirect()->route('lanlord.index')->with('success', 'Landlord registered successfully.');
    }

    public function edit($landlord)
    {
        $landlord = Landlord::with(['user', 'properties.units'])->findOrFail($landlord);

        $totalProperties = $landlord->properties->count();
        $totalUnits = $landlord->properties->sum(function ($property) {
            return $property->units->count();
        });

        return view('landlord.edit', [
            'lanlord' => $landlord,
            'totalProperties' => $totalProperties,
            'totalUnits' => $totalUnits
        ]);
    }

    public function show($landlord)
    {
        $landlord = Landlord::find($landlord);
        return view('landlord.edit', [
            'lanlord' => $landlord
        ]);
    }


    public function update(Request $request, $landlordId)
    {
        $landlord = Landlord::find($landlordId);

        if (!$landlord) {
            return redirect()->route('landlord.index')->with('error', 'Landlord not found.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone_number' => [
                'required',
                'string',
                'regex:/^([0-9\s\-\+\(\)]*)$/',
                'min:10',
                'max:15',
                'unique:users,phone,' . $landlord->user_id,
                'unique:landlords,phone_number,' . $landlord->id
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email,' . $landlord->user_id,
                'unique:landlords,email,' . $landlord->id
            ],
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('uploads', $imageName, 'public');
        }

        $landlord->update([
            'name' => $request->name,
            'address' => $request->address,
            'phone_number' => $request->phone_number,
            'email' => $request->email,
            'profile_image' => $path,
'user_id' => auth()->user()->id
        ]);



        return redirect()->route('lanlord.index')->with('success', 'Landlord updated successfully');
    }

    public function destroy($landlord)
    {
        $lanlord_has_property = Property::where('landlord_id', $landlord)->first();
        if ($lanlord_has_property) {
            return redirect()->route('lanlord.index')->with('error', 'Landlord has property cannot be deleted');
        }
        Landlord::find($landlord)->delete();
        return redirect()->route('lanlord.index')->with('success', 'Landlord deleted successfully');
    }
}
