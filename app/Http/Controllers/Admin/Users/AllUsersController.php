<?php

namespace App\Http\Controllers\Admin\Users;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AllUsersController extends Controller
{
    public function index () {

        $users = User::where('role', '!=', 'admin')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($user) {
                $userArray = $user->toArray();
                $userArray['avatar_url'] = $user->avatar_url;
                return $userArray;
            });
        return inertia('Admin/Users/AllUsers',[
            'users' => $users
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
