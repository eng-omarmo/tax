<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;

class RoleandaccessController extends Controller
{
    public function assignRole()
    {
        $users = User::all();
        $roles = Role::all();
        return view('roleandaccess.assignRole', compact('users', 'roles'));
    }

    public function roleAaccess()
    {
        $roles = Role::with('permissions')->get();
        return view('roleandaccess.roleAaccess', compact('roles'));
    }

    public function updateUserRoles(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
        ]);

        $user = User::findOrFail($request->user_id);
        $roles = Role::whereIn('id', $request->roles)->get();

        $user->syncRoles($roles);

        return redirect()->back()->with('success', 'User roles updated successfully');
    }
}
