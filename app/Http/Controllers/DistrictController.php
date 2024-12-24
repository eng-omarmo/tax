<?php

namespace App\Http\Controllers;

use App\Models\District;
use Illuminate\Http\Request;

class DistrictController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query= District::query();
        if($request->has('search') && $request->search){
            $query->where('name','like','%'.$request->search.'%');
        }
        $districts = $query->paginate(5);
        return view('district.index', compact('districts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('district.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'name' => 'required',
        ]);

        District::create($request->all());
        return redirect()->route('district.index')->with('success', 'District created successfully.');
    }

    /**
     * Display the specified resource.
     */
    // public function show(District $district)
    // {
    //     return view('district.show', compact('district'));
    // }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $district = District::find($id);
        return view('district.edit', compact('district'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,$district)
    {
        $request->validate([
            'name' => 'required',
        ]);
        District::find($district)->update([
            'name' => $request->name,
        ]);
        return redirect()->route('district.index')->with('success', 'District updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($district)
    {
        District::find($district)->delete();
        return redirect()->route('district.index')->with('success', 'District deleted successfully.');
    }
}
