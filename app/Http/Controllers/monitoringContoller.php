<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;

class monitoringContoller extends Controller
{
    //

    public function index(Request $request)
    {
        $statuses = Property::pluck('status')->unique();
        $monitoringStatuses = Property::pluck('monitoring_status')->unique();
        $query  = Property::with('transactions', 'landlord')->where(['status' => 'inActive', 'monitoring_status' => 'Pending']);
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
        $properties = $query->paginate(5);
        foreach ($properties as $property) {
            $property->balance = $property->transactions->sum(function ($transaction) {
                return $transaction->debit - $transaction->credit;
            });
        }
        return view('property.monitor.index', compact('properties', 'statuses', 'monitoringStatuses'));
    }

    public function approve(Request $request)
    {
     
        try {
            $status = 'Active';
            $monitoring_status= 'Approved';
            $property = Property::where('id', $request->property_id)->first();
            if (!$property) {
                return response()->json(['success' => false, 'message' => 'Property not found'], 404);
            }
            $data = $property->calculateTax($property->house_type, $property->house_rent);
            $property->update([
                'monitoring_status' => $monitoring_status,
                'status' => $status,
                'quarterly_tax_fee' => $data['quarterly_tax'],
                'yearly_tax_fee' => $data['yearly_tax'],
            ]);
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
