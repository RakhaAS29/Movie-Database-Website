<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckLogin
{
    public function handle(Request $request, Closure $next)
    {
        // Cek apakah user udah login (session 'logged_in')
        if (!session('logged_in')) {
            return redirect('/login')->with('error', 'Please login first');
        }

        return $next($request);
    }
}