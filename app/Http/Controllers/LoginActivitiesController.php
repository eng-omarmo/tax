<?php

namespace App\Http\Controllers;

use App\Models\LoginActivities;
use App\Models\User;
use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;

class LoginActivitiesController extends Controller
{
    /**
     * Display a listing of login activities.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = LoginActivities::with('user');

        // Filter by user if user_id is provided
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        // Get activities with pagination
        $activities = $query->latest('logged_in_at')->paginate(15);

        // Get all users for the filter dropdown
        $users = User::orderBy('name')->get(['id', 'name', 'email']);

        return view('login-activities.index', compact('activities', 'users'));
    }
}
