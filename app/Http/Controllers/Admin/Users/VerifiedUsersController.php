<?php

namespace App\Http\Controllers\Admin\Users;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class VerifiedUsersController extends Controller
{
    public function index()
    {
        $verifiedUsers = User::where('verified', 1)
            ->orderBy('created_at', 'desc')
            ->get();

        return inertia('Admin/Users/VerifiedUsers', [
            'verifiedUsers' => $verifiedUsers
        ]);
    }
}
