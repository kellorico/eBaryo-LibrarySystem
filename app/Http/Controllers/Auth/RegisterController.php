<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function create()
    {
        return inertia('Auth/Register');
    }

    public function register(Request $request)
    {
        $credentials = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|lowercase',
            'password' => 'required|min: 8|confirmed'
        ]);

        $user = User::create($credentials);

        Auth::login($user);

        return redirect()->route('home');
    }
}
