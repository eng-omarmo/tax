<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\Tax;
use App\Models\Branch;
use App\Models\District;
use App\Models\Landlord;
use App\Models\Property;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class propertyController extends Controller
{

    public function index(Request $request)
    {
        $statuses = Property::pluck('status')->unique();

        $monitoringStatuses = Property::pluck('monitoring_status')->unique();

        $query  = Property::with('transactions', 'landlord');
        if ($request->filled('search')) {
            // has landlord
            $query->whereHas('landlord', function ($q) use ($request) {
                $q->whereHas('user', function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%');
                });
            });

            $query->where('property_name', 'like', '%' . $request->search . '%')
                ->orWhere('property_phone', 'like', '%' . $request->search . '%')
                ->orWhere('house_code', 'like', '%' . $request->search . '%')
                ->orWhere('nbr', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('monetering_status')) {
            $query->where('monitoring_status', $request->monetering_status);
        }
        if (auth()->user()->role === 'Landlord') {
            $query->whereHas('landlord.user', function ($q) {
                $q->where('id', auth()->id());
            });
        }
        $properties = $query->orderby('id', 'desc')->paginate(10);

        return view('property.index', compact('properties', 'statuses', 'monitoringStatuses'));
    }

    public function getBranches($districtId)
    {
        $branches = Branch::where('district_id', $districtId)->get();
        if ($branches->isEmpty()) {
            return response()->json(['error' => 'No branches found for the selected district.']);
        }
        return response()->json($branches);
    }



    public function ReportDetails(Request $request)
    {


        $query = Property::query();

        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('created_at', '>=', Carbon::parse($request->start_date)->format('Y-m-d'));
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('created_at', '<=', Carbon::parse($request->end_date)->format('Y-m-d'));
        }

        if ($request->has('nbr') && $request->nbr) {
            $query->where('nbr', $request->house_code);
        }

        if ($request->has('status') && $request->status && $request->status !== 'all') {
            $query->where('district', $request->status);
        }

        if ($request->has('branch') && $request->branch && $request->branch !== 'all') {
            $query->where('branch', $request->branch->id);
        }

        if ($request->has('zone') && $request->zone && $request->zone !== 'all') {
            $query->where('zone', $request->zone);
        }
        if (request('isPrint') == 1) {
            if ($request->input('isPrint') == 1) {
                return $this->exportPdf($query->get());
            }
        }
        $properties = $query->paginate(10);
        dd($properties);
        foreach ($properties as $property) {
            $property->balance = $property->transactions->sum(function ($transaction) {
                return $transaction->debit - $transaction->credit;
            });
        }

        return view('property.report', [
            'properties' => $properties,
            'data' => $this->returnReports()
        ]);
    }

    public function exportPdf($properties)
    {
        try {
            $pdf = Pdf::loadView('property.report_pdf', compact('properties'));
            return $pdf->download('Property_Report.pdf');
        } catch (\Throwable $th) {
            Log::error('Error generating PDF: ' . $th->getMessage());
            return redirect()->back()->withErrors(['error' => 'Failed to generate PDF. Please try again.']);
        }
    }



    public function create()
    {
        $data['districts'] = District::select('id', 'name')->get();
        return view('property.create', $data);
    }

    public function report()
    {

        return view(
            'property.report',
            [
                'data' => $this->returnReports()
            ]
        );
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $request->validate([
                'property_name' => 'required',
                'property_phone' => 'required',
                'house_type' => 'required',
                'branch_id' => 'required',
                'district_id' => 'required',
                'zone' => 'required',
                'latitude' => 'required',
                'longitude' => 'required',
                'lanlord_id' => 'required',
                'image' => 'image|mimes:jpeg,png,jpg,webp|max:2048', // Validate file type & size
            ]);
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();

            $path = $image->storeAs('uploads', $imageName, 'public');

            $request->merge(['image' => $imageName]);
            $properties = Property::where('property_name', $request->property_name)
                ->where('property_phone', $request->property_phone)
                ->first();

            if ($properties) {
                return back()->with('error', 'Property name and phone already exists.');
            }
            $code = 'HOUSE-' . strtoupper(Str::random(3)) . '-' . rand(100, 999);

            $request->merge(['house_code' => $code]);



            Property::create([
                'property_name' => $request->property_name,
                'property_phone' => $request->property_phone,
                'house_code' => $request->house_code,
                'branch_id' => $request->branch_id,
                'zone' => $request->zone,
                'house_type' => $request->house_type,
                'house_rent' => $request->house_rent,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'monitoring_status' => $request->monitoring_status,
                'status' => $request->status,
                'district_id' => $request->district_id,
                'landlord_id' => $request->lanlord_id,
                'monitoring_status' => 'Pending',
                'status' => 'InActive',
                'image' => $path
            ]);

            DB::commit();

            return redirect()->route('property.index')->with('success', 'Property registered successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Error creating property: ' . $th->getMessage());
            return back()->with('error', $th->getMessage());
        }
    }


    public function propertyCreate()
    {

        $data['districts'] = District::select('id', 'name')->get();
        return view('property.lanlord.create', $data);
    }


    public function edit($id)
    {
        $property = Property::with('branch')->findorFail($id);
        $districts = District::select('id', 'name')->get();
        $branches = Branch::where('district_id', $property->district_id)->get();

        return view('property.edit', compact('property', 'districts', 'branches'));
    }

    public function show($id)
    {
        $property = Property::findorFail($id);
        return view('property.show', compact('property'));
    }
    public function update(Request $request, $property)
    {

        try {
            $request->validate([
                'property_name' => 'required|string|max:255',
                'property_phone' => 'nullable|string|max:25',
                'nbr' => 'nullable|string|max:100',
                'house_code' => 'nullable|string|max:50',
                'branch' => 'nullable|string|max:255',
                'quarterly_tax_fee' => 'nullable|numeric',
                'yearly_tax_fee' => 'nullable|numeric',
                'zone' => 'nullable|string|max:255',
                'house_type' => 'nullable|string|max:255',
                'house_rent' => 'nullable|numeric',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'dalal_company_name' => 'nullable|string|max:255',
                'designation' => 'nullable|string|max:255',
                'monitoring_status' => 'required|in:Pending,Approved',
                'status' => 'required|in:Active,Inactive',
                'district_id' => 'required|exists:districts,id',
            ]);

            $property = Property::find($property);

            if (!$property) {
                return back()->with('error', 'Property not found.');
            }



            $property->update([
                'property_name' => $request->property_name,
                'property_phone' => $request->property_phone,
                'nbr' => $request->nbr,

                'branch_id' => $request->branch,
                'zone' => $request->zone,
                'house_type' => $request->house_type,
                'house_rent' => $request->house_rent,
                'quarterly_tax_fee' => $request->quarterly_tax_fee,
                'yearly_tax_fee' => $request->yearly_tax_fee,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'designation' => $request->designation,

                'district_id' => $request->district_id,
                'monitoring_status' => $request->monitoring_status,
                'status' => $request->status,
            ]);

            if (auth()->user()->role == 'Admin') {
                return redirect()->route('property.index')->with('success', 'Property updated successfully.');
            }
            return redirect()->route('monitor.index')->with('success', 'Property updated successfully.');
        } catch (Exception $th) {
            return back()->with('error', $th->getMessage());
            Log::info($th->getMessage());
        }
    }

    public function destroy($id)
    {
        Property::query()->find($id)->delete();
        return redirect()->route('property.index')->with('success', 'Property deleted successfully.');
    }

    private function returnReports()
    {
        $getDistrictIDs = Property::pluck('district_id');
        $data['statuses'] = Property::pluck('status')
            ->unique();
        $getBranchIDs = Property::with('branch')->pluck('branch_id')
            ->unique();
        $data['zones'] = Property::pluck('zone')
            ->unique();
        $data['districts'] = District::whereIn('id', $getDistrictIDs)->select('name', 'id')->get();
        $data['branches'] = Branch::whereIn('id', $getBranchIDs)->select('name', 'id')->get();
        return $data;
    }

    public function search(Request $request)
    {

        $lanlord = Landlord::where('phone_number', $request->search_lanlord)->first();
        $districts = District::select('id', 'name')->get();
        if (!$lanlord) {
            return back()->with('error', 'Landlord not found');
        }
        return view('property.create', compact('lanlord', 'districts'));
    }
    private function createTransaction($property)
    {
        try {
            return Transaction::create([
                'tenant_id' => null,
                'transaction_id' => 'Tran' . rand(1000, 9999) . rand(1000, 9999),
                'property_id' => $property->id,
                'transaction_type' => 'Tax',
                'amount' => $property->yearly_tax_fee,
                'description' => 'Tenant Rent',
                'credit' => 0,
                'debit' => $property->yearly_tax_fee,
                'status' => 'Pending',
            ]);
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
        }
    }


    private function recordTaxFee($property)
    {

        try {
            return Tax::create([
                'property_id' => $property->id,
                'tax_amount' => $property->yearly_tax_fee,
                'due_date' => now()->addMonths(1),
                'status' => 'Completed',
                'tax_code' => 'T' . rand(1000, 9999) . rand(1000, 9999),
            ]);
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
        }
    }
}
