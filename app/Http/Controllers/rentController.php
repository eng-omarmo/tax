<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Rent;
use App\Models\Unit;
use App\Models\Tenant;
use App\Models\Property;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use function Laravel\Prompts\select;
use Illuminate\Support\Facades\Storage;

class rentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $query = Rent::with('property', 'unit');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        $rents = $query->paginate(5);

        foreach ($rents as $rent) {
            $rent->balance =
                Transaction::where('property_id', $rent->property_id)
                ->where('unit_id', $rent->unit_id)
                ->where('transaction_type', 'Rent')
                ->sum('debit')
                -
                Transaction::where('property_id', $rent->property_id)
                ->where('unit_id', $rent->unit_id)
                ->where('transaction_type', 'Rent')
                ->sum('credit');
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

                'property_id' => 'required|exists:properties,id',
                'unit_id' => 'required|exists:units,id',
                'rent_amount' => 'required|numeric|min:0',
                'rent_start_date' => 'required|date',
                'rent_end_date' => 'required|date',
                'status' => 'required',
                'tenant_name' => 'required',
                'rent_document' => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:2048', // Add validation for file
            ]);

            DB::beginTransaction();

            $rentDocumentPath = null;
            if ($request->hasFile('rent_document')) {
                $rentDocumentPath = $request->file('rent_document')->store('uploads/rent_documents', 'public');
            }

            $rent_code = 'R' . rand(1000, 9999) . rand(1000, 9999);
            $rent = Rent::create([
                'tenant_name' => $request->tenant_name,
                'unit_id' => $request->unit_id,
                'rent_code' => $rent_code,
                'property_id' => $request->property_id,
                'rent_amount' => $request->rent_amount,
                'rent_start_date' => $request->rent_start_date,
                'rent_end_date' => $request->rent_end_date,
                'rent_total_amount' => $this->calculateRent($request->rent_start_date, $request->rent_end_date, $request->rent_amount),
                'rent_document' => $rentDocumentPath,
                'status' => $request->status
            ]);

            if ($rent->status == 'active') {
                Unit::where('id', $request->unit_id)->update(['is_available' => false]);
            }

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
    public function edit(Request $request, $rent)

    {

        $rent = Rent::with('unit', 'property')->where('id', $rent)->first();


        return view('rent.edit', compact('rent'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $rent)
    {

        try {
            $request->validate([
                'rent_amount' => 'required|numeric|min:0',
                'rent_start_date' => 'required|date',
                'rent_end_date' => 'required|date',
                'status' => 'required',
                'tenant_name' => 'required',
                'rent_document' => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:2048',
            ]);


            $rent = Rent::find($rent);
            if (!$rent) {
                return redirect()->route('rent.index')->with('error', 'Rent not found.');
            }



            $rentDocumentPath = $rent->rent_document;

            if ($rent->rent_document != $request->rent_document) {
                Storage::disk('public')->delete($rentDocumentPath);
                $rentDocumentPath = $request->file('rent_document')->store('uploads/rent_documents', 'public');
            }

            $rent->update([
                'tenant_name' => $request->tenant_name,
                'rent_amount' => $request->rent_amount,
                'rent_start_date' => $request->rent_start_date,
                'rent_end_date' => $request->rent_end_date,
                'rent_total_amount' => $this->calculateRent($request->rent_start_date, $request->rent_end_date, $request->rent_amount),
                'status' => $request->status,
                'rent_document' => $rentDocumentPath
            ]);

            return redirect()->route('rent.index')->with('success', 'Rent updated successfully.');
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
            return redirect()->route('rent.index')->with('error', $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($rent)
    {
        $rent = Rent::with('property', 'unit')->find($rent);
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


    function calculateRent($startDate, $endDate, $monthlyRent)
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        if ($start->greaterThan($end)) {
            return redirect()->route('rent.index')->with('error', 'Start date is greater than end date');
        }


        $totalDays = $start->diffInDays($end) + 1;
        $daysInMonth = $start->daysInMonth;


        $dailyRent = $monthlyRent / $daysInMonth;


        $totalRent = $dailyRent * $totalDays;

        return $totalRent;
    }

    public function search(Request $request)
    {

        $request->validate([
            'search_unit_number' => 'required',
        ]);


        $unit = Unit::with('property')->where('unit_number', $request->search_unit_number)->first();
        if ($unit && $unit->is_available == false) {
            return back()->withInput()->with('error', 'unit is not available');
        }

        $tenants = Tenant::with('user')
            ->where(
                'registered_by',
                '=',
                auth()->user()->id
            )
            ->get();

        if (!$unit || !$tenants) {
            return back()->with('error', 'unit property not found');
        }

        return view('rent.create', [
            'unit' => $unit,
            'tenants' => $tenants
        ]);
    }


    private function createTransaction($rent)
    {
        try {
            return Transaction::create([
                'tenant_id' => $rent->tenant_id,
                'transaction_id' => 'Tran' . rand(1000, 9999) . rand(1000, 9999),
                'property_id' => $rent->property_id,
                'unit_id' => $rent->unit_id,
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
}
