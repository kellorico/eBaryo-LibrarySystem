<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function show()
    {
        return inertia('Auth/Register');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);


        if (User::where('email', $credentials['email'])->exists()) {
            return back()->withErrors([
                'email' => 'This email is already registered.',
            ])->onlyInput('email');
        }

        $user = User::create($credentials);

        // Create notification for admin
        NotificationService::userRegistered($user);

        Auth::login($user);
        return redirect()->route('home')->with('success', 'Registration successful!');
    }
}
