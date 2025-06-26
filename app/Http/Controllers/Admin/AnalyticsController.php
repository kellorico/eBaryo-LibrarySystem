<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Bookmark;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Inertia\Inertia;

class AnalyticsController extends Controller
{
    /**
     * Show the analytics dashboard with all analytics data as Inertia props
     */
    public function index()
    {
        // Top Read Books
        $topReadBooks = DB::table('download_logs')
            ->select('book_id', DB::raw('count(*) as read_count'))
            ->groupBy('book_id')
            ->orderByDesc('read_count')
            ->limit(5)
            ->get()
            ->map(function ($row) {
                $book = Book::find($row->book_id);
                return [
                    'title' => $book ? $book->title : 'Unknown',
                    'read_count' => $row->read_count,
                ];
            });

        // Top Bookmarked Books
        $topBookmarkedBooks = DB::table('bookmarks')
            ->select('book_id', DB::raw('count(*) as bookmarks'))
            ->groupBy('book_id')
            ->orderByDesc('bookmarks')
            ->limit(5)
            ->get()
            ->map(function ($row) {
                $book = Book::find($row->book_id);
                return [
                    'title' => $book ? $book->title : 'Unknown',
                    'bookmarks' => $row->bookmarks,
                ];
            });

        // Top Rated Books
        $topRatedBooks = DB::table('ratings')
            ->select('book_id', DB::raw('avg(rating) as avg_rating'), DB::raw('count(*) as ratings_count'))
            ->groupBy('book_id')
            ->having('ratings_count', '>', 0)
            ->orderByDesc('avg_rating')
            ->limit(5)
            ->get()
            ->map(function ($row) {
                $book = Book::find($row->book_id);
                return [
                    'title' => $book ? $book->title : 'Unknown',
                    'avg_rating' => round($row->avg_rating, 2),
                    'ratings_count' => $row->ratings_count,
                ];
            });

        // Top Books This Month
        $start = Carbon::now()->startOfMonth();
        $topBooksThisMonth = DB::table('download_logs')
            ->where('created_at', '>=', $start)
            ->select('book_id', DB::raw('count(*) as read_count'))
            ->groupBy('book_id')
            ->orderByDesc('read_count')
            ->limit(5)
            ->get()
            ->map(function ($row) {
                $book = Book::find($row->book_id);
                return [
                    'title' => $book ? $book->title : 'Unknown',
                    'read_count' => $row->read_count,
                ];
            });

        // Most Active Users
        $downloads = DB::table('download_logs')
            ->select('user_id', DB::raw('count(*) as downloads'))
            ->groupBy('user_id');
        $bookmarks = DB::table('bookmarks')
            ->select('user_id', DB::raw('count(*) as bookmarks'))
            ->groupBy('user_id');
        $ratings = DB::table('ratings')
            ->select('user_id', DB::raw('count(*) as ratings'))
            ->groupBy('user_id');

        $mostActiveUsers = DB::table('users')
            ->leftJoinSub($downloads, 'downloads', function ($join) {
                $join->on('users.id', '=', 'downloads.user_id');
            })
            ->leftJoinSub($bookmarks, 'bookmarks', function ($join) {
                $join->on('users.id', '=', 'bookmarks.user_id');
            })
            ->leftJoinSub($ratings, 'ratings', function ($join) {
                $join->on('users.id', '=', 'ratings.user_id');
            })
            ->select(
                'users.id',
                'users.name',
                DB::raw('COALESCE(downloads.downloads,0) as downloads'),
                DB::raw('COALESCE(bookmarks.bookmarks,0) as bookmarks'),
                DB::raw('COALESCE(ratings.ratings,0) as ratings'),
                DB::raw('(COALESCE(downloads.downloads,0) + COALESCE(bookmarks.bookmarks,0) + COALESCE(ratings.ratings,0)) as activity_score')
            )
            ->orderByDesc('activity_score')
            ->limit(5)
            ->get();

        return Inertia::render('Admin/Analytics', [
            'topReadBooks' => $topReadBooks,
            'topBookmarkedBooks' => $topBookmarkedBooks,
            'topRatedBooks' => $topRatedBooks,
            'topBooksThisMonth' => $topBooksThisMonth,
            'mostActiveUsers' => $mostActiveUsers,
        ]);
    }

    public function data(Request $request)
    {
        // Top Read Books
        $topReadBooks = DB::table('download_logs')
            ->select('book_id', DB::raw('count(*) as read_count'))
            ->groupBy('book_id')
            ->orderByDesc('read_count')
            ->limit(5)
            ->get()
            ->map(function ($row) {
                $book = Book::find($row->book_id);
                return [
                    'title' => $book ? $book->title : 'Unknown',
                    'read_count' => $row->read_count,
                ];
            });

        // Top Bookmarked Books
        $topBookmarkedBooks = DB::table('bookmarks')
            ->select('book_id', DB::raw('count(*) as bookmarks'))
            ->groupBy('book_id')
            ->orderByDesc('bookmarks')
            ->limit(5)
            ->get()
            ->map(function ($row) {
                $book = Book::find($row->book_id);
                return [
                    'title' => $book ? $book->title : 'Unknown',
                    'bookmarks' => $row->bookmarks,
                ];
            });

        // Top Rated Books
        $topRatedBooks = DB::table('ratings')
            ->select('book_id', DB::raw('avg(rating) as avg_rating'), DB::raw('count(*) as ratings_count'))
            ->groupBy('book_id')
            ->having('ratings_count', '>', 0)
            ->orderByDesc('avg_rating')
            ->limit(5)
            ->get()
            ->map(function ($row) {
                $book = Book::find($row->book_id);
                return [
                    'title' => $book ? $book->title : 'Unknown',
                    'avg_rating' => round($row->avg_rating, 2),
                    'ratings_count' => $row->ratings_count,
                ];
            });

        // Top Books This Month
        $start = Carbon::now()->startOfMonth();
        $topBooksThisMonth = DB::table('download_logs')
            ->where('created_at', '>=', $start)
            ->select('book_id', DB::raw('count(*) as read_count'))
            ->groupBy('book_id')
            ->orderByDesc('read_count')
            ->limit(5)
            ->get()
            ->map(function ($row) {
                $book = Book::find($row->book_id);
                return [
                    'title' => $book ? $book->title : 'Unknown',
                    'read_count' => $row->read_count,
                ];
            });

        // Most Active Users
        $downloads = DB::table('download_logs')
            ->select('user_id', DB::raw('count(*) as downloads'))
            ->groupBy('user_id');
        $bookmarks = DB::table('bookmarks')
            ->select('user_id', DB::raw('count(*) as bookmarks'))
            ->groupBy('user_id');
        $ratings = DB::table('ratings')
            ->select('user_id', DB::raw('count(*) as ratings'))
            ->groupBy('user_id');

        $mostActiveUsers = DB::table('users')
            ->leftJoinSub($downloads, 'downloads', function ($join) {
                $join->on('users.id', '=', 'downloads.user_id');
            })
            ->leftJoinSub($bookmarks, 'bookmarks', function ($join) {
                $join->on('users.id', '=', 'bookmarks.user_id');
            })
            ->leftJoinSub($ratings, 'ratings', function ($join) {
                $join->on('users.id', '=', 'ratings.user_id');
            })
            ->select(
                'users.id',
                'users.name',
                DB::raw('COALESCE(downloads.downloads,0) as downloads'),
                DB::raw('COALESCE(bookmarks.bookmarks,0) as bookmarks'),
                DB::raw('COALESCE(ratings.ratings,0) as ratings'),
                DB::raw('(COALESCE(downloads.downloads,0) + COALESCE(bookmarks.bookmarks,0) + COALESCE(ratings.ratings,0)) as activity_score')
            )
            ->orderByDesc('activity_score')
            ->limit(5)
            ->get();

        return response()->json([
            'topReadBooks' => $topReadBooks,
            'topBookmarkedBooks' => $topBookmarkedBooks,
            'topRatedBooks' => $topRatedBooks,
            'topBooksThisMonth' => $topBooksThisMonth,
            'mostActiveUsers' => $mostActiveUsers,
        ]);
    }
}
