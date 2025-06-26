<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Book;
use Illuminate\Http\Request;
use App\Models\Archive;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Dashboard', [
            'totalBooks' => Book::count(),
            'totalUsers' => User::count(),
            'activeUsers' => User::where('verified', true)->count(),
            'flash' => session('success')
        ]);
    }

    public function __invoke()
    {
        $totalBooks = Book::count();
        $totalUsers = User::count();
        $activeUsers = User::where('verified', true)->count();

        // Archive stats
        $totalArchives = Archive::count();
        $publicArchives = Archive::where('is_public', true)->count();
        $categories = ['Resolution', 'Photo', 'Event', 'History', 'Misc'];
        $archivesByCategory = [];
        foreach ($categories as $cat) {
            $archivesByCategory[$cat] = Archive::where('category', $cat)->count();
        }
        $recentArchives = Archive::with('uploader')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return Inertia::render('Admin/Dashboard', [
            'totalBooks' => $totalBooks,
            'totalUsers' => $totalUsers,
            'activeUsers' => $activeUsers,
            'totalArchives' => $totalArchives,
            'publicArchives' => $publicArchives,
            'archivesByCategory' => $archivesByCategory,
            'recentArchives' => $recentArchives,
            'flash' => session('success'),
        ]);
    }

    public function activityOverview(Request $request)
    {
        $range = $request->query('range', '7d');
        $days = 7;
        if ($range === '30d') $days = 30;
        if ($range === '90d') $days = 90;
        $labels = [];
        $bookReads = [];
        $archiveUploads = [];
        $newUsers = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $label = $date->format('M d');
            $labels[] = $label;
            $bookReads[] = DB::table('download_logs')->whereDate('created_at', $date)->count();
            $archiveUploads[] = DB::table('archives')->whereDate('created_at', $date)->count();
            $newUsers[] = DB::table('users')->whereDate('created_at', $date)->count();
        }
        return response()->json([
            'labels' => $labels,
            'bookReads' => $bookReads,
            'archiveUploads' => $archiveUploads,
            'newUsers' => $newUsers,
        ]);
    }
}
