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
        $query  = Property::with('transactions', 'landlord')->where(['status' => 'inActive', 'monitoring_status' => 'Pending'] );
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
}
