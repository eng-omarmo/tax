<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;


class UsersController extends Controller
{
    public function create()
    {
        $districts = District::all();
        return view('users.create', compact('districts'));
    }

    public function store(Request $request)
    {

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:18',
            'status' => 'required|string',
            'district_id' => 'nullable|string'
            // 'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validate the image
        ]);
        // if ($request->hasFile('profile_image')) {

        //     $image = $request->file('profile_image');

        //     $imageName = time() . '.' . $image->getClientOriginalExtension();

        //     $imagePath = $image->storeAs('profile_images', $imageName, 'public');
        // } else {
        //     $imagePath = null;
        // }


        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make('password'),
            'phone' => $validated['phone'],
            'status' => $validated['status'],
            'district_id' => $validated['district_id'],
            'profile_image' => null
        ]);


        return redirect()->route('user.index')->with('success', 'User created successfully!');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $districts= District::all();
        return view('users.edit', compact('user','districts'));
    }

    public function usersGrid()
    {

        return view('users/usersGrid');
    }

    public function index(Request $request)
    {
        $request->validate([
            'search' => 'nullable|string|max:255',
            'status' => 'nullable|string|in:Active,Inactive',
        ]);

        $query = User::query();
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%')
                ->orWhere('phone', 'like', '%' . $request->search . '%')

                ->orWhere('status', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', ucfirst($request->status));
        }

        $users = $query->paginate(10);
        return view('users.index', compact('users'));
    }
        public function viewProfile()
    {
        return view('users/viewProfile');
    }

    public function delete($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->route('user.index')->with('success', 'User deleted successfully!');
    }

    public function update(Request $request, $id){
        try {
            $user = User::findOrFail($id);
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'district_id' => $request->district_id,
                'status' => $request->status
            ]);
            return redirect()->route('user.index')->with('success', 'User Updated successfully!');
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
            return redirect()->route('user.index')->with('error', $th->getMessage());
        }
    }

    /**
     * Display detailed information about the user including activities.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function details($id)
    {
        // Find the user with related data
        $user = User::with([
            'loginActivities',
            'properties' => function($query) {
                $query->with(['district', 'branch', 'landlord', 'units', 'taxs']);
            },
            'landlords' => function($query) {
                $query->with(['properties' => function($q) {
                    $q->with(['units', 'taxs']);
                }]);
            },
            'district'
        ])->findOrFail($id);

        // Get statistics
        $stats = [
            'total_properties' => $user->properties->count(),
            'total_units' => $user->properties->sum(function($property) {
                return $property->units->count();
            }),
            'total_landlords' => $user->landlords->count(),
            'total_logins' => $user->loginActivities->count(),
            'last_login' => $user->loginActivities->sortByDesc('logged_in_at')->first()?->logged_in_at,
            'total_taxes' => $user->properties->sum(function($property) {
                return $property->taxs->count();
            }),
        ];

        // Get recent activities
        $recentProperties = $user->properties()->latest()->take(5)->get();
        $recentLogins = $user->loginActivities()->latest('logged_in_at')->take(5)->get();

        return view('users.details', compact('user', 'stats', 'recentProperties', 'recentLogins'));
    }
}
