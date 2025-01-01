<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\Tax;
use App\Models\District;
use App\Models\Landlord;
use App\Models\Property;
use App\Models\Transaction;
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
                ->orWhere('nbr', 'like', '%' . $request->search . '%')
                ->orWhere('branch', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('monetering_status')) {
            $query->where('monitoring_status', $request->monetering_status);
        }
        if (auth()->user()->role == 'Landlord') {
            $query->where('property.landlord_id.user_id', auth()->user()->landlord_id);
        }
        $properties = $query->paginate(5);
        foreach ($properties as $property) {
            $property->balance = $property->transactions->sum(function ($transaction) {
                return $transaction->debit - $transaction->credit;
            });
        }
        return view('property.index', compact('properties', 'statuses', 'monitoringStatuses'));
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
            $query->where('nbr', $request->nbr);
        }

        if ($request->has('status') && $request->status && $request->status !== 'all') {
            $query->where('district', $request->status);
        }

        if ($request->has('branch') && $request->branch && $request->branch !== 'all') {
            $query->where('branch', $request->branch);
        }

        if ($request->has('zone') && $request->zone && $request->zone !== 'all') {
            $query->where('zone', $request->zone);
        }
        if (request('isPrint') == 1) {
            if ($request->input('isPrint') == 1) {
                return $this->exportPdf($query->get()); // Pass the filtered data to the PDF export method
            }
        }
        $properties = $query->paginate(5);
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
                'property_name' => 'required|string|max:255',
                'property_phone' => 'nullable|string|max:45',
                'nbr' => 'nullable|string|max:100',
                'house_code' => 'nullable|string|max:50',
                'branch' => 'nullable|string|max:255',
                'zone' => 'nullable|string|max:255',
                'house_type' => 'nullable|string|max:255',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'dalal_company_name' => 'nullable|string|max:255',
                'designation' => 'nullable|string|max:255',
                'monitoring_status' => 'required|in:Pending,Approved',
                'status' => 'required|in:Active,Inactive',
                'district_id' => 'required|exists:districts,id',
                'house_rent' => 'nullable|numeric',
                'quarterly_tax_fee' => 'nullable|numeric',
                'yearly_tax_fee' => 'nullable|numeric',
            ]);

            $checkProperty = Property::where('property_name', $request->property_name)
                ->where('property_phone', $request->property_phone)
                ->first();

            if ($checkProperty) {
                return back()->with('error', 'Property name and phone already exists.');
            }

            $property =  Property::create([
                'property_name' => $request->property_name,
                'property_phone' => $request->property_phone,
                'nbr' => $request->nbr,
                'house_code' => $request->house_code,
                'branch' => $request->branch,
                'zone' => $request->zone,
                'house_type' => $request->house_type,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'dalal_company_name' => $request->dalal_company_name,
                'designation' => $request->designation,
                'monitoring_status' => $request->monitoring_status,
                'status' => $request->status,
                'district_id' => $request->district_id,
                'house_rent' => $request->house_rent,
                'quarterly_tax_fee' => $request->quarterly_tax_fee,
                'yearly_tax_fee' => $request->yearly_tax_fee,
                'landlord_id' => $request->lanlord_id
            ]);
            $this->recordTaxFee($property);
            $this->createTransaction($property);
            DB::commit();

            return redirect()->route('property.index')->with('success', 'Property registered successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }


    public function edit($id)
    {
        $property = Property::findorFail($id);
        $districts = District::select('id', 'name')->get();

        return view('property.edit', compact('property', 'districts'));
    }

    public function show($id)
    {
        $property = Property::findorFail($id);
        $districts = District::select('id', 'name')->get();

        return view('property.edit', compact('property', 'districts'));
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
                'quarterly_tax_fee' => 'required|numeric',
                'yearly_tax_fee' => 'required|numeric',
                'longitude' => 'required|numeric',
                'dalal_company_name' => 'nullable|string|max:255',
                'designation' => 'nullable|string|max:255',
                'monitoring_status' => 'required|in:Pending,Approved',
                'status' => 'required|in:Active,Inactive',
                'district_id' => 'required|exists:districts,id',
            ]);
            Property::find($property)->update([
                'property_name' => $request->property_name,
                'property_phone' => $request->property_phone,
                'nbr' => $request->nbr,
                'house_code' => $request->house_code,
                'branch' => $request->branch,
                'zone' => $request->zone,
                'house_type' => $request->house_type,
                'house_rent' => $request->house_rent,
                'quarterly_tax_fee' => $request->quarterly_tax_fee,
                'yearly_tax_fee' => $request->yearly_tax_fee,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'designation' => $request->designation,
                'dalal_company_name' => $request->dalal_company_name,
                'district_id' => $request->district_id,
                'monitoring_status' => $request->monitoring_status,
                'status' => $request->status,
            ]);
            return redirect()->route('property.index')->with('success', 'Property updated successfully.');
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
        $data['branches'] = Property::pluck('branch')
            ->unique();
        $data['zones'] = Property::pluck('zone')
            ->unique();
        $data['districts'] = District::whereIn('id', $getDistrictIDs)->select('name', 'id')->get();
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
                'status' => 'Pending',
                'tax_code' => 'T' . rand(1000, 9999) . rand(1000, 9999),
            ]);
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
        }
    }
}
