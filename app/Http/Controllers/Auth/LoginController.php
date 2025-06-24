<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function show () {
        return inertia('Auth/Login');
    }

    public function login (Request $request) {
        
        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string|min:8',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();
            if ($user->role === 'admin') {
                return redirect()->route('dashboard')->with('success', 'Login successful! Welcome Back Admin!');
            } else {
                return redirect()->route('home')->with('success', 'Login successful! Welcome Back!');
            }
        }   
    }

    public function logout (Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('welcome')->with('success', 'Logout successful!');
    }
}
