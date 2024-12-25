<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\Request;

class businessController extends Controller
{
    public function index()
    {
        $query = Business::query();
        if (request()->has('search') && request()->search) {
            $query->where('name', 'like', '%' . request()->search . '%');
        }
        $businessess = $query->paginate(5);
        return view('business.index', compact('businessess'));
    }
    public function create()
    {
        return view('business.create');
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'address' => 'required|string|max:255',
                'phone' => 'required|string|max:255',
            ]);

            Business::create([
                'name' => $request->name,
                'address' => $request->address,
                'phone' => $request->phone
            ]);
            return redirect()->route('business.index');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            Business::find($id)->delete();
            return redirect()->route('business.index');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }
    public function edit($id)
    {
        try {
            $business = Business::find($id);
            return view('business.edit', compact('business'));
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    public function update(Request $request, $id)
    {

        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'address' => 'required|string|max:255',
                'phone' => 'required|string|max:255',
            ]);
            Business::find($id)->update([
                'name' => $request->name,
                'address' => $request->address,
                'phone' => $request->phone
            ]);
            return redirect()->route('business.index');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }
    public function destroy($id)
    {
        try {
            Business::find($id)->delete();
            return redirect()->route('business.index');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }
}
