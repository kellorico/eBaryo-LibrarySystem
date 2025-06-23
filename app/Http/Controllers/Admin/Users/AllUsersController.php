<?php

namespace App\Http\Controllers\Admin\Users;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AllUsersController extends Controller
{
    public function index () {

        $user = User::where('role', '!=', 'admin')
            ->orderBy('created_at', 'desc')
            ->get();
        return inertia('Admin/Users/AllUsers',[
            'users' => $user
        ]);
    }

    public function verify (Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $user->verified = true;
        $user->save();

        return redirect()->back()->with('message', 'User verified successfully.');
    }
}
