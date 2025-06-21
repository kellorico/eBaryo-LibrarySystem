<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Book;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminDashboardController extends Controller
{
    public function index () {
        return Inertia::render('Admin/Dashboard', [
            'totalBooks' => Book::count(),
            'totalUsers' => User::count(),
            'activeUsers' => User::where('verified', true)->count()
        ]);
    }
}
