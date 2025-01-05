<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('signin')->with('error', 'You must log in first.');
        }
        $user = Auth::user();

        if ($user && ucfirst($user->role) != 'Admin' && ucfirst($user->role)  != 'Landlord' && ucfirst($user->role)  != 'Tax officer' && $user->status != 'Active') {

            return redirect()->route('signin')->with('error', 'Access denied.');
        }

        return $next($request);
    }
}
