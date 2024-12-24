<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\District;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Branch::query();
        if (request()->has('search') && request()->search) {
            $query->where('name', 'like', '%' . request()->search . '%');
        }
        $branchs  = $query->paginate(5);
        return view('branch.index', compact('branchs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $districts = District::select('id', 'name')->get();
        return view('branch.create', compact('districts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'district_id' => 'required|exists:districts,id',
        ]);
        Branch::create([
            'name' => $request->name,
            'district_id' => $request->district_id
        ]);

        return redirect()->route('branch.index')->with('success', 'Branch created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Branch $branch)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Branch $branch)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,  $branch)
    {
        $request->validate([
            'name' => 'required',
            'district_id' => 'required|exists:districts,id',
        ]);
        Branch::find($branch)->update([
            'name' => $request->name,
            'district_id' => $request->district_id
        ]);
        return redirect()->route('branch.index')->with('success', 'Branch updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($branch)
    {
        Branch::find($branch)->delete();
        return redirect()->route('branch.index')->with('success', 'Branch deleted successfully.');
    }
}
