<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    // Show login page
    public function showLogin()
    {
        // Jika sudah login, redirect ke movies
        if (session('logged_in')) {
            return redirect()->route('movies.index');
        }
        
        return view('auth.login');
    }

    // Handle login
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        // Hardcoded credentials
        if ($request->username === 'aldmic' && $request->password === '123abc123') {
            session(['logged_in' => true]);
            return redirect()->route('movies.index');
        }

        return back()->withErrors(['login' => 'Invalid credentials']);
    }

    // Handle logout
    public function logout()
    {
        session()->forget('logged_in');
        return redirect()->route('login')->with('success', 'Logged out successfully');
    }
}