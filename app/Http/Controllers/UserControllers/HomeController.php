<?php

namespace App\Http\Controllers\UserControllers;

use App\Http\Controllers\Controller;
use App\Models\Bookmark;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\Book;
use App\Models\ReadingProgress;
use App\Models\ReadingChallenge;
use App\Models\Announcement;

class HomeController extends Controller
{
    public function home()
    {
        $userId = Auth::id();
        $bookmarkCount = Bookmark::where('user_id', $userId)->count();

        // Books in progress (progress < 100)
        $inProgress = ReadingProgress::where('user_id', $userId)
            ->where('progress', '<', 100)
            ->count();

        // Challenges joined
        $challengesJoined = ReadingChallenge::whereHas('participants', function($q) use ($userId) {
            $q->where('user_id', $userId);
        })->count();

        // Recommended books (random 5)
        $recommendedBooks = Book::inRandomOrder()->limit(5)->get(['id', 'title', 'author', 'cover_image', 'file_path']);

        // Announcements (latest 3)
        $announcements = Announcement::orderByDesc('created_at')->limit(3)->get(['id', 'title', 'content as message', 'created_at']);

        // Reading progress (books in progress)
        $readingProgress = ReadingProgress::where('user_id', $userId)
            ->where('progress', '<', 100)
            ->with(['book:id,title,cover_image,file_path'])
            ->get(['id', 'book_id', 'progress']);

        // Active challenges
        $today = now()->toDateString();
        $challenges = ReadingChallenge::where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->orderBy('end_date')
            ->get(['id', 'title', 'description', 'start_date', 'end_date', 'target_books']);

        // Leaderboard: leave empty, to be loaded on demand
        $leaderboard = [];

        return Inertia::render('User/Home', [
            'stats' => [
                'bookmarks' => $bookmarkCount,
                'inProgress' => $inProgress,
                'challenges' => $challengesJoined,
            ],
            'recommendedBooks' => $recommendedBooks,
            'announcements' => $announcements,
            'readingProgress' => $readingProgress,
            'challenges' => $challenges,
            'leaderboard' => $leaderboard,
        ]);
    }
} 