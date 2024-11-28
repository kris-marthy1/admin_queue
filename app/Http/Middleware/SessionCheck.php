<?php

// namespace App\Http\Middleware;

// use Closure;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;

// class SessionCheck
// {
//     public function handle(Request $request, Closure $next)
//     {
//         // Check if the session variable 'logged_in_user' is set
//         if (!session()->has('logged_in_user')) {
//             return redirect()->back()->withErrors(['login_error' => 'Please log in to access the dashboard']);
//         }
//         if (session()->has('logged_in_user')) {
//             return redirect('/dashboard')->withErrors(['login_error' => 'Please log out first']);
//         }
//         return $next($request);
//     }
// }

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionCheck
{
    public function handle(Request $request, Closure $next)
    {
        // If the user is trying to access the login page and they are already logged in, redirect to dashboard
        if ($request->is('/') && session()->has('logged_in_user')) {
            return redirect('/dashboard');
        }

        // If the user is trying to access the dashboard and they are not logged in, redirect to login
        if ($request->is('dashboard') && !session()->has('logged_in_user')) {
            return redirect('/')->withErrors(['login_error' => 'Please log in to access the dashboard']);
        }

        return $next($request);
    }
}
