<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Landlord;
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
        $landlords = $query->paginate(10);
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
        ]);

        $landlord =  Landlord::create([
            'name' => $request->name,
            'address' => $request->address,
            'phone_number' => $request->phone,
            'email' => $request->email
        ]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt('password'),
            'role' => 'Landlord',
            'status' => 'Active',
            'profile_image' => null
        ]);
        $landlord->update([
            'user_id' => $user->id
        ]);
        return redirect()->route('lanlord.index');
    }

    public function edit($landlord)
    {
        $landlord = Landlord::find($landlord);
        return view('landlord.edit', [
            'lanlord' => $landlord
        ]);
    }


    public function update(Request $request, $landlord)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:255',

        ]);
        Landlord::find($landlord)->update([
            'name' => $request->name,
            'address' => $request->address,
            'phone_number' => $request->phone,
        ]);
        return redirect()->route('lanlord.index');
    }
    public function destroy($landlord)
    {
        Landlord::find($landlord)->delete();
        return redirect()->route('lanlord.index')->with('success', 'Landlord deleted successfully');
    }
}
