<?php

namespace App\Http\Controllers\Admin\Users;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use App\Models\User;

class UnVerifiedUsersController extends Controller
{
    public function index()
    {
        $users = User::where('verified', 0)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($user) {
                $userArray = $user->toArray();
                $userArray['avatar_url'] = $user->avatar_url;
                return $userArray;
            });
        return inertia('Admin/Users/UnverifiedUsers', [
            'users' => $users
        ]);
    }

    public function verify(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->verified = true;
        $user->save();

        // Create notification for user verification
        NotificationService::userVerified($user);

        return redirect()->back()->with('message', 'User verified successfully.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->back()->with('message', 'User deleted successfully.');
    }
}
