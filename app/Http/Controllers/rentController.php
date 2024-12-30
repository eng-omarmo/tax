<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Rent;
use App\Models\Tenant;
use App\Models\Property;
use Illuminate\Http\Request;

use function Laravel\Prompts\select;

class rentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $query = Rent::with('property', 'tenant')
            ->whereHas('tenant', function ($query) {
                $query->where('registered_by', auth()->user()->id);
            });

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        $rents = $query->paginate(5);

        foreach ($rents as $rent) {
            $rent->duration = $this->getRentDuration($rent);
            $rent->total_rent_amount =  $this->getTotalRentAmount($rent);
        }
        return view('rent.index', compact('rents'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('rent.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Rent $rent)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Rent $rent)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Rent $rent)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Rent $rent)
    {
        //
    }


    public function getRentDuration(Rent $rent)
    {
        // Parse the start and end dates using Carbon
        $startDate = Carbon::parse($rent->rent_start_date);
        $endDate = Carbon::parse($rent->rent_end_date);

        // Calculate the rent duration in days
        $durationInDays = $startDate->diffInDays($endDate);

        return $durationInDays;  // Return duration in days
    }

    public function getTotalRentAmount(Rent $rent)
    {
        // Calculate the rent duration in months
        $startDate = Carbon::parse($rent->rent_start_date);
        $endDate = Carbon::parse($rent->rent_end_date);
        $durationInMonths = $startDate->diffInMonths($endDate) + 1;  // Add 1 to include the start month

        // Calculate the total rent amount
        return $rent->rent_amount * $durationInMonths;
    }

    public function search(Request $request)
    {

        $request->validate([
            'search_property' => 'required',
        ]);

        $property = Property::where('property_phone', $request->search_property)
            ->select('id','property_name','property_phone','house_rent')->first();
        $tenants = Tenant::with('user')
            ->where(
                'registered_by',
                '=',
                auth()->user()->id
            )
            ->get();

        if (!$property || !$tenants) {
            return back()->with('error', 'property not found');
        }

        return view('rent.create',[
            'property' => $property,
            'tenants' => $tenants
        ]);
    }
}
