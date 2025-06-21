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
use Illuminate\Container\Attributes\Auth;
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
                ->orWhere('house_code', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('monetering_status')) {
            $query->where('monitoring_status', $request->monetering_status);
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
        $query = Property::with(['landlord.user', 'district', 'branch', 'transactions']);

        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('created_at', '>=', Carbon::parse($request->start_date)->format('Y-m-d'));
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('created_at', '<=', Carbon::parse($request->end_date)->format('Y-m-d'));
        }


        if ($request->has('status') && $request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('district') && $request->district && $request->district !== 'all') {
            $query->where('district_id', $request->district);
        }

        if ($request->has('branch') && $request->branch && $request->branch !== 'all') {
            $query->where('branch_id', $request->branch);
        }

        if ($request->has('zone') && $request->zone && $request->zone !== 'all') {
            $query->where('zone', $request->zone);
        }

        // Export PDF if requested
        if ($request->input('isPrint') == 1) {
            return $this->exportPdf($query->get());
        }

        $properties = $query->paginate(10);



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

    public function getPropertiesByDistrict($districtId)
    {
        $district = District::findOrFail($districtId);

        $query = Property::with('transactions', 'landlord')
            ->where('district_id', $districtId);

        $properties = $query->orderBy('id', 'desc')->paginate(10);

        return view('property.index', [
            'properties' => $properties,
            'districtFilter' => $district->name,
            'statuses' => Property::pluck('status')->unique(),
            'monitoringStatuses' => Property::pluck('monitoring_status')->unique()
        ]);
    }


    public function create($id)
    {
        $data['districts'] = District::select('id', 'name')->get();
        $data['branches'] = Branch::select('id', 'name')->get();
        $data['landlord'] = Landlord::findorFail($id);

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
            $document = $request->file('document');
            $documentName = time() . '.' . $document->getClientOriginalExtension();

            $path = $image->storeAs('uploads', $imageName, 'public');
            $documentPath = $document->storeAs('uploads', $documentName, 'public');

            $request->merge(['image' => $imageName]);
            $properties = Property::where('property_name', $request->property_name)
                ->where('property_phone', $request->property_phone)
                ->first();

            if ($properties) {
                return back()->with('error', 'Property name and phone already exists.');
            }
            $code = 'HOUSE-' . strtoupper(Str::random(3)) . '-' . rand(100, 999);

            $request->merge(['house_code' => $code]);

            // Change default monitoring status based on configuration or request
            $monitoringStatus = $request->has('skip_monitoring') && $request->skip_monitoring == 1 ? 'Approved' : 'Pending';
            $status = $monitoringStatus == 'Approved' ? 'Active' : 'InActive';

            $property = Property::create([
                'property_name' => $request->property_name,
                'property_phone' => $request->property_phone,
                'house_code' => $request->house_code,
                'branch_id' => $request->branch_id,
                'zone' => $request->zone,
                'house_type' => $request->house_type,
                'house_rent' => $request->house_rent,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'monitoring_status' => $monitoringStatus,
                'status' => $status,
                'district_id' => $request->district_id,
                'landlord_id' => $request->lanlord_id,
                'document'  => $documentPath,
                'image' => $path,
                'created_by' =>Auth()->user()->id
            ]);

            DB::commit();

            // Check if the user wants to continue to unit registration
            if ($request->has('continue_to_unit') && $request->continue_to_unit == 1) {
                return redirect()->route('unit.create', $property->id)
                    ->with('success', 'Property registered successfully. Now register units.');
            }

            return redirect()->route('property.index')->with('success', 'Property registered successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Error creating property: ' . $th->getMessage());
            return back()->with('error', $th->getMessage());
        }
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

                'house_code' => 'nullable|string|max:50',
                'branch' => 'nullable|string|max:255',
                'quarterly_tax_fee' => 'nullable|numeric',
                'yearly_tax_fee' => 'nullable|numeric',
                'zone' => 'nullable|string|max:255',
                'house_type' => 'nullable|string|max:255',
                'house_rent' => 'nullable|numeric',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'designation' => 'nullable|string|max:255',
                'monitoring_status' => 'required|in:Pending,Approved',
                'status' => 'required|in:Active,Inactive',
                'district_id' => 'required|exists:districts,id',
            ]);

            $property = Property::find($property);
            if (!$property) {
                return back()->with('error', 'Property not found.');
            }

            $updateData = $request->only([
                'property_name',
                'property_phone',
                'branch',
                'zone',
                'house_type',
                'house_rent',
                'quarterly_tax_fee',
                'yearly_tax_fee',
                'latitude',
                'longitude',
                'designation',
                'district_id',
                'monitoring_status',
                'status'
            ]);

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $updateData['image'] = $image->storeAs('uploads', $imageName, 'public');
            }

            if ($request->hasFile('document')) {
                $document = $request->file('document');
                $documentName = time() . '.' . $document->getClientOriginalExtension();
                $updateData['document'] = $document->storeAs('uploads', $documentName, 'public');
            }

            $property->update($updateData);

            $redirectRoute = auth()->user()->hasRole('Admin') ? 'property.index' : 'monitor.index';

            return redirect()->route($redirectRoute)->with('success', 'Property updated successfully.');
        } catch (Exception $th) {
            Log::error('Error updating property: ' . $th->getMessage());
            return back()->with('error', $th->getMessage());
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
}
