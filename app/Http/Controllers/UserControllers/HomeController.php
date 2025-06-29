<?php

namespace App\Http\Controllers\UserControllers;

use App\Http\Controllers\Controller;
use App\Models\Bookmark;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\Book;
use App\Models\ReadingProgress;
use App\Models\Announcement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

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

        // Recommended books (random 5)
        $recommendedBooks = Book::inRandomOrder()->limit(5)->get(['id', 'title', 'author', 'cover_image', 'file_path', 'published_year'])
            ->map(function ($book) {
                $data = $book->toArray();
                if (!$data['cover_image']) {
                    $data['cover_image'] = asset('assets/images/image.png');
                } else {
                    $data['cover_image'] = asset('storage/' . ltrim($data['cover_image'], '/'));
                }
                return $data;
            });

        // Announcements (latest 3)
        $announcements = Announcement::orderByDesc('created_at')->limit(3)->get(['id', 'title', 'content as message', 'created_at']);

        // Reading progress (books in progress)
        $readingProgress = ReadingProgress::where('user_id', $userId)
            ->where('progress', '<', 100)
            ->with(['book:id,title,cover_image,file_path'])
            ->get(['id', 'book_id', 'progress']);

        // Leaderboard: leave empty, to be loaded on demand
        $leaderboard = [];

        // --- New Book Lists ---
        // Latest/New Books
        $latestBooks = Book::orderByDesc('created_at')->limit(8)->get(['id', 'title', 'author', 'cover_image', 'file_path', 'created_at', 'published_year'])
            ->map(function ($book) {
                $data = $book->toArray();
                if (!$data['cover_image']) {
                    $data['cover_image'] = asset('assets/images/image.png');
                } else {
                    $data['cover_image'] = asset('storage/' . ltrim($data['cover_image'], '/'));
                }
                return $data;
            });

        // Most Read (all time)
        $mostReadBooks = DB::table('download_logs')
            ->select('book_id', DB::raw('count(*) as read_count'))
            ->groupBy('book_id')
            ->orderByDesc('read_count')
            ->limit(8)
            ->get()
            ->map(function ($row) {
                $book = Book::find($row->book_id);
                if ($book) {
                    $data = $book->only(['id','title','author','cover_image','file_path','published_year']);
                    if (!$data['cover_image']) {
                        $data['cover_image'] = asset('assets/images/image.png');
                    } else {
                        $data['cover_image'] = asset('storage/' . ltrim($data['cover_image'], '/'));
                    }
                    return array_merge($data, ['read_count' => $row->read_count]);
                }
                return null;
            })->filter()->values();

        // Hot Books (this month)
        $startOfMonth = now()->startOfMonth();
        $hotBooks = DB::table('download_logs')
            ->where('created_at', '>=', $startOfMonth)
            ->select('book_id', DB::raw('count(*) as read_count'))
            ->groupBy('book_id')
            ->orderByDesc('read_count')
            ->limit(8)
            ->get()
            ->map(function ($row) {
                $book = Book::find($row->book_id);
                if ($book) {
                    $data = $book->only(['id','title','author','cover_image','file_path','published_year']);
                    if (!$data['cover_image']) {
                        $data['cover_image'] = asset('assets/images/image.png');
                    } else {
                        $data['cover_image'] = asset('storage/' . ltrim($data['cover_image'], '/'));
                    }
                    return array_merge($data, ['read_count' => $row->read_count]);
                }
                return null;
            })->filter()->values();

        // Highest Rated
        $highestRatedBooks = DB::table('ratings')
            ->select('book_id', DB::raw('avg(rating) as avg_rating'), DB::raw('count(*) as ratings_count'))
            ->groupBy('book_id')
            ->having('ratings_count', '>', 0)
            ->orderByDesc('avg_rating')
            ->limit(8)
            ->get()
            ->map(function ($row) {
                $book = Book::find($row->book_id);
                if ($book) {
                    $data = $book->only(['id','title','author','cover_image','file_path','published_year']);
                    if (!$data['cover_image']) {
                        $data['cover_image'] = asset('assets/images/image.png');
                    } else {
                        $data['cover_image'] = asset('storage/' . ltrim($data['cover_image'], '/'));
                    }
                    return array_merge($data, ['avg_rating' => round($row->avg_rating,2), 'ratings_count' => $row->ratings_count]);
                }
                return null;
            })->filter()->values();

        // Saved Books (bookmarks)
        $user = Auth::user();
        $savedBooks = $user->bookmarks()->with('book')->get()->map(function($bm) {
            return $bm->book;
        })->filter()->values();

        return Inertia::render('User/Home', [
            'stats' => [
                'bookmarks' => $bookmarkCount,
                'inProgress' => $inProgress,
            ],
            'recommendedBooks' => $recommendedBooks,
            'announcements' => $announcements,
            'readingProgress' => $readingProgress,
            'leaderboard' => $leaderboard,
            // New book lists:
            'latestBooks' => $latestBooks,
            'mostReadBooks' => $mostReadBooks,
            'hotBooks' => $hotBooks,
            'highestRatedBooks' => $highestRatedBooks,
            'savedBooks' => $savedBooks,
        ]);
    }
} 