<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\District;
use App\Models\Property;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class propertyController extends Controller
{

    public function index(Request $request)
    {

        $statuses = Property::pluck('status')->unique();

        $monitoringStatuses = Property::pluck('monitoring_status')->unique();
        //app filter
        $query  = Property::query();
        if ($request->filled('search')) {
            $query->where('property_name', 'like', '%' . $request->search . '%')
                ->orWhere('property_phone', 'like', '%' . $request->search . '%')
                ->orWhere('nbr', 'like', '%' . $request->search . '%')
                ->orWhere('house_code', 'like', '%' . $request->search . '%')
                ->orWhere('tenant_name', 'like', '%' . $request->search . '%')
                ->orWhere('tenant_phone', 'like', '%' . $request->search . '%')
                ->orWhere('branch', 'like', '%' . $request->search . '%')
                ->orWhere('zone', 'like', '%' . $request->search . '%')
                ->orWhere('house_type', 'like', '%' . $request->search . '%')
                ->orWhere('status', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('monetering_status')) {
            $query->where('monitoring_status', $request->monetering_status);
        }

        $properties = $query->paginate(5);
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

        $request->validate([
            'property_name' => 'required|string|max:255',
            'property_phone' => 'nullable|string|max:15',
            'nbr' => 'nullable|string|max:100',
            'house_code' => 'nullable|string|max:50',
            'tenant_name' => 'nullable|string|max:255',
            'tenant_phone' => 'nullable|string|max:15',
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
            'is_owner' => 'required|in:Yes,No',
            'designation' => 'nullable|string|max:255',
            'monitoring_status' => 'required|in:Pending,Approved',
            'status' => 'required|in:Active,Inactive',
            'district_id' => 'required|exists:districts,id',
        ]);
        $checkProperty = Property::where('property_name', $request->property_name)->first();
        if ($checkProperty) {
            return back()->with('error', 'Property name already exists.');
        }

        Property::create([
            'property_name' => $request->property_name,
            'property_phone' => $request->property_phone,
            'nbr' => $request->nbr,
            'house_code' => $request->house_code,
            'tenant_name' => $request->tenant_name,
            'tenant_phone' => $request->tenant_phone,
            'branch' => $request->branch,
            'zone' => $request->zone,
            'house_type' => $request->house_type,
            'house_rent' => $request->house_rent,
            'quarterly_tax_fee' => $request->quarterly_tax_fee,
            'yearly_tax_fee' => $request->yearly_tax_fee,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'is_owner' => $request->is_owner,
            'designation' => $request->designation,
            'dalal_company_name' => $request->dalal_company_name,
            'district_id' => $request->district_id,
            'monitoring_status' => $request->monitoring_status,
            'status' => $request->status,
        ]);
        return redirect()->route('property.index')->with('success', 'Property registered successfully.');
    }

    public function edit($id)
    {
        $property = Property::findorFail($id);

        return view('property.edit', compact('property'));
    }
    public function update(Request $request, Property $property)
    {
        $request->validate([
            'property_name' => 'required|string|max:255',
            'property_phone' => 'nullable|string|max:15',
            'nbr' => 'nullable|string|max:100',
            'house_code' => 'nullable|string|max:50',
            'tenant_name' => 'nullable|string|max:255',
            'tenant_phone' => 'nullable|string|max:15',
            'branch' => 'nullable|string|max:255',
            'zone' => 'nullable|string|max:255',
            'house_type' => 'nullable|string|max:255',
            'house_rent' => 'nullable|numeric',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'status' => 'required|in:Active,Inactive',
        ]);
        $property->update($request->all());
        return redirect()->route('property.index')->with('success', 'Property updated successfully.');
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
        $data['districts'] = District::whereIn('id', $getDistrictIDs)->select('name','id')->get();
        return $data;
    }
}
