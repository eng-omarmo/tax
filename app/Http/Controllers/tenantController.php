<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Tenant;
use App\Models\Property;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use app\Models\User;

class tenantController extends Controller
{

    public function index(Request $request)
    {
        $query = Tenant::where('registered_by', auth()->user()->id)->with('user', 'transactions');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }


        $tenants = $query->paginate(5);

        foreach ($tenants as $tenant) {
            $tenant->balance = $tenant->transactions->sum(function ($transaction) {
                return $transaction->debit - $transaction->credit;
            });
        }



        return view('tenant.index', compact('tenants'));
    }

    public function search(Request $request)
    {
        $request->validate([
            'search_property' => 'required|string',
        ]);

        $search = trim($request->input('search_property'));

        $property = Property::where('property_phone', 'like', '%' . $search . '%')->first();
        if (!$property) {
            return back()->with('error', 'Property not found.');
        }

        return view('tenant.create', ['property' => $property]);
    }



    public function create()
    {
        return view('tenant.create');
    }

    public function store(Request $request)
    {

        try {
            $request->validate([
                'name' => 'required',
                'email' => 'required|string|max:255',
                'phone' => 'required|string',
                'status' => 'required|in:Active,Inactive',
            ]);
            DB::beginTransaction();


            $user =  User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'status' => $request->status,
                'password' => bcrypt('password'),
                'role' => 'Tenant',
                'profile_image' => null
            ]);
            Tenant::create([
                'user_id' => $user->id,
                'registered_by' => auth()->user()->id,
                'status' => $request->status,
            ]);

            DB::commit();
            return redirect()->route('tenant.index')->with('success', 'Tenant added successfully!');
        } catch (Exception $th) {
            Log::info($th->getMessage());
            return redirect()->back()->withInput()->with('error', $th->getMessage());
        }
    }


    public function edit($id)
    {
        $tenant = Tenant::with('user')->findOrFail($id);
        return view('tenant.edit', compact('tenant'));
    }

    public function getBalance($tenantId)
    {
        $tenant = Tenant::with('transactions')->findOrFail($tenantId);
        return  $tenant->calculateBalance();
    }

    public function update(Request $request, $id)
    {
        $tenant = Tenant::find($id);
        $tenant->user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'status' => $request->status,
        ]);

        return redirect()->route('tenant.index')->with('success', 'Tenant updated successfully.');
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $tenant = Tenant::find($id);
            $tenant->user->delete();
            $tenant->delete();

            DB::commit();
            return redirect()->route('tenant.index')->with('success', 'Tenant deleted successfully.');
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
            return redirect()->route('tenant.index')->with('error', $th->getMessage());
        }
    }
}
