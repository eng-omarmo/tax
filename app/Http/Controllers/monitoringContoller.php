<?php

namespace App\Http\Controllers;

use App\Models\Rent;
use App\Models\Unit;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class monitoringContoller extends Controller
{
    //

    public function index(Request $request)
    {
        $statuses = Property::pluck('status')->unique();
        $monitoringStatuses = Property::pluck('monitoring_status')->unique();
        $query  = Property::with('transactions', 'landlord')->where('monitoring_status' , 'Pending')->orderBy('id', 'desc');
        if ($request->filled('search')) {
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
        $properties = $query->paginate(10);
        foreach ($properties as $property) {
            $property->balance = $property->transactions->sum(function ($transaction) {
                return $transaction->debit - $transaction->credit;
            });
        }
        return view('property.monitor.index', compact('properties', 'statuses', 'monitoringStatuses'));
    }
    public function show($id)
    {
        $property = Property::with([
            'units',
            'landlord.user',
            'district'
        ])->findOrFail($id);

        return view('property.monitor.details', compact('property'));
    }

    public function rentStore(Request $request)
    {

        try {
            $request->validate([
                'property_id' => 'required|exists:properties,id',
                'unit_id' => 'required|exists:units,id',
                'rent_amount' => 'required|numeric|min:0',
                'rent_start_date' => 'required|date',
                'rent_end_date' => 'required|date',

                'tenant_name' => 'required',
                'rent_document' => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:2048',
            ]);

            DB::beginTransaction();
            $rent_document = $request->file('rent_document');
            $rent_document_name = time() . '.' . $rent_document->getClientOriginalExtension();
            $path = $rent_document->storeAs('uploads', $rent_document_name, 'public');
            $rent_code = 'R' . rand(1000, 9999) . rand(1000, 9999);
            $rent = Rent::create([
                'tenant_name' => $request->tenant_name,
                'tenant_phone' => $request->tenant_phone,
                'unit_id' => $request->unit_id,
                'rent_code' => $rent_code,
                'property_id' => $request->property_id,
                'rent_amount' => $request->rent_amount,
                'rent_start_date' => $request->rent_start_date,
                'rent_end_date' => $request->rent_end_date,
                'rent_total_amount' => $this->calculateRent($request->rent_start_date, $request->rent_end_date, $request->rent_amount),
                'rent_document' => $path,
                'status' => 'active'
            ]);
            if ($rent->status == 'active') {
                Unit::where('id', $request->unit_id)->update(['is_available' => 1]);
            }
            DB::commit();
            return redirect()->route('monitor.show', $rent->property_id)->with('success', 'Rent created successfully.');
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
            return redirect()->route('monitor.show', $request->property_id)->with('error', $th->getMessage());
        }
    }

    private function calculateRent($startDate, $endDate, $rentAmount)
    {
        $start = new \DateTime($startDate);
        $end = new \DateTime($endDate);
        $interval = $start->diff($end);
        $days = $interval->days;
        return $days * $rentAmount;
    }

    public function rentIndex(int $id)
    {
        $unit = Unit::where('id', $id)->first();
        return view('property.monitor.rent', compact('unit'));
    }
    public function viewRent($id)
    {
        $rent = Rent::with([
            'unit' => function ($query) {
                $query->with(['property' => function ($q) {
                    $q->with('landlord.user');
                }]);
            },
            'property',

        ])->findOrFail($id);

        // Calculate rental duration
        $startDate = new \DateTime($rent->rent_start_date);
        $endDate = new \DateTime($rent->rent_end_date);
        $interval = $startDate->diff($endDate);
        $rent->duration = [
            'years' => $interval->y,
            'months' => $interval->m,
            'days' => $interval->d
        ];

        $unit = $rent;

        return view('property.monitor.unit.details', compact('rent'));
    }


    public function approve(Request $request)
    {

        try {
            $status = 'Active';
            $monitoring_status = 'Approved';
            $property = Property::where('id', $request->property_id)->first();
            if (!$property) {
                return response()->json(['success' => false, 'message' => 'Property not found'], 404);
            }
            $property->monitoring_status = $monitoring_status;
            $property->status = $status;
            $property->save();
            return response()->json(['success' => true, 'message' => 'Property approved successfully'], 200);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()], 500);
        }
    }

    public function reject(Request $request)
    {
        try {
            $property = Property::where('id', $request->property_id)->first();
            if (!$property) {
                return response()->json(['message' => 'Property not found'], 404);
            }
            $property->update([
                'monitoring_status' => 'Rejected',
                'status' => 'inActive',
            ]);
            return response()->json(['message' => 'Property rejected successfully'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
