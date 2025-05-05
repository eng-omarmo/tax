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
            'phone' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
        $image = $request->file('image');
        $imageName = time() . '.' . $image->getClientOriginalExtension();

        $path = $image->storeAs('uploads', $imageName, 'public');
        $landlord =  Landlord::create([
            'name' => $request->name,
            'address' => $request->address,
            'phone_number' => $request->phone,
            'email' => $request->email
        ]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => bcrypt('password'),
            'role' => 'Landlord',
            'status' => 'Active',
            'profile_image' => $path
        ]);
        $landlord->update([
            'user_id' => $user->id
        ]);
        return redirect()->route('lanlord.index');
    }

    public function edit($landlord)
    {
        $landlord = Landlord::with(['user', 'properties.units'])->findOrFail($landlord);

        $totalProperties = $landlord->properties->count();
        $totalUnits = $landlord->properties->sum(function($property) {
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

        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:255',
        ]);

        $landlord = Landlord::find($landlordId);

        if (!$landlord) {
            return redirect()->route('landlord.index')->with('error', 'Landlord not found.');
        }

        $image = $request->file('image');
        $imageName = time() . '.' . $image->getClientOriginalExtension();

        $path = $image->storeAs('uploads', $imageName, 'public');

        $landlord->update([
            'name' => $request->name,
            'address' => $request->address,
            'phone_number' => $request->phone,
            'email' => $request->email

        ]);

        if ($landlord->user) {
            $landlord->user->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'profile_image' => $path
            ]);
        }

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
