<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;


class UsersController extends Controller
{
    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:18',
            'role' => 'required|string',
            'status' => 'required|string',
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
            'role' => $validated['role'],
            'status' => $validated['status'],
            'profile_image' => null
        ]);


        return redirect()->route('user.index')->with('success', 'User created successfully!');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('users.edit', compact('user'));
    }

    public function usersGrid()
    {

        return view('users/usersGrid');
    }

    public function index(Request $request)
    {
        $request->validate([
            'search' => 'nullable|string|max:255',
            'role' => 'nullable|string|max:255',
            'status' => 'nullable|string|in:Active,Inactive',
        ]);

        $roles = User::select('role')->distinct()->pluck('role');
        $query = User::query();
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%')
                ->orWhere('phone', 'like', '%' . $request->search . '%')
                ->orWhere('role', 'like', '%' . $request->search . '%')
                ->orWhere('status', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        if ($request->filled('status')) {
            $query->where('status', ucfirst($request->status));
        }

        $users = $query->paginate(5);
        return view('users.index', compact('users', 'roles'));
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

    public function update(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'role' => $request->role,
                'status' => $request->status
            ]);
            return redirect()->route('user.index')->with('success', 'User Updated successfully!');
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
            return redirect()->route('user.index')->with('error', $th->getMessage());
        }
    }
}
