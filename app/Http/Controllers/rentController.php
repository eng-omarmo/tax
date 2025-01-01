<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Rent;
use App\Models\Tenant;
use App\Models\Property;
use App\Models\Transaction;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
            $rent->balance =
                Transaction::where('tenant_id', $rent->tenant_id)
                ->where('property_id', $rent->property_id)
                ->where('transaction_type', 'Rent')
                ->sum('debit')
                -
                Transaction::where('tenant_id', $rent->tenant_id)
                ->where('property_id', $rent->property_id)
                ->where('transaction_type', 'Rent')
                ->sum('credit');


            $rent->total_rent_amount =  $this->calculateMonthsBetween($rent->rent_start_date, $rent->rent_end_date) * $rent->rent_amount;
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
        try {
            $request->validate([
                'tenant_id' => 'required|exists:tenants,id',
                'property_id' => 'required|exists:properties,id',
                'rent_amount' => 'required|numeric|min:0',
                'rent_start_date' => 'required|date',
                'rent_end_date' => 'required|date',
                'status' => 'required',
            ]);
            DB::beginTransaction();
            $rent_code = 'R' . rand(1000, 9999).rand(1000, 9999);
            $rent = Rent::create([
                'tenant_id' => $request->tenant_id,
                'rent_code' => $rent_code,
                'property_id' => $request->property_id,
                'rent_amount' => $request->rent_amount,
                'rent_start_date' => $request->rent_start_date,
                'rent_end_date' => $request->rent_end_date,
                'status' => $request->status

            ]);
            $rent->rent_amount = $this->calculateMonthsBetween($rent->rent_start_date, $rent->rent_end_date) * $rent->rent_amount;
            $this->createTransaction($rent);
            DB::commit();
            return redirect()->route('rent.index')->with('success', 'Rent created successfully.');
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
            return redirect()->route('rent.index')->with('error', $th->getMessage());
        }
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
    public function destroy($rent)
    {
        $rent = Rent::find($rent);
        $rent->delete();
        return redirect()->route('rent.index')->with('success', 'Rent deleted successfully.');
    }


    public function getRentDuration(Rent $rent)
    {

        $startDate = Carbon::parse($rent->rent_start_date);
        $endDate = Carbon::parse($rent->rent_end_date);

        $durationInDays = $startDate->diffInDays($endDate);

        return $durationInDays;
    }


    function calculateMonthsBetween($startDate, $endDate)
    {

        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);


        if ($start->greaterThan($end)) {
            throw new \InvalidArgumentException("Start date cannot be after the end date.");
        }

        $months = $start->diffInMonths($end) + 1;

        return $months;
    }
    public function search(Request $request)
    {

        $request->validate([
            'search_property' => 'required',
        ]);

        $property = Property::where('property_phone', $request->search_property)
            ->select('id', 'property_name', 'property_phone', 'house_rent')->first();
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

        return view('rent.create', [
            'property' => $property,
            'tenants' => $tenants
        ]);
    }


    private function createTransaction($rent)
    {
        try {
            return Transaction::create([
                'tenant_id' => $rent->tenant_id,
                'property_id' => $rent->property_id,
                'transaction_type' => 'Rent',
                'amount' => $rent->rent_amount,
                'description' => 'Tenant Rent',
                'credit' => 0,
                'debit' => $rent->rent_amount,
                'status' => 'Pending',
            ]);
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
        }
    }

    private function createTransactionForTaxFee($rent)
    {
        try {
            return Transaction::create([
                'tenant_id' => $rent->id,
                'transaction_type' => 'Tax',
                'amount' => $tenant->tax_fee,
                'description' => 'Tenant Tax Fee',
                'credit' => 0,
                'debit' => $tenant->tax_fee,
                'status' => 'Pending',
            ]);
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
        }
    }



}
