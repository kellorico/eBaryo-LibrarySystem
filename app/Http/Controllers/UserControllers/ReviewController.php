<?php

namespace App\Http\Controllers\UserControllers;

use App\Http\Controllers\Controller;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ReviewController extends Controller
{
    public function index($bookId)
    {
        $reviews = Rating::with('user')
            ->where('book_id', $bookId)
            ->where('status', 'approved')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($r) {
                return [
                    'id' => $r->id,
                    'user' => $r->user ? $r->user->name : 'User',
                    'rating' => $r->rating,
                    'review' => $r->review,
                    'created_at' => $r->created_at->toDateTimeString(),
                ];
            });
        $avg = Rating::where('book_id', $bookId)->where('status', 'approved')->avg('rating');
        return Inertia::render('BookReviews', [
            'reviews' => $reviews,
            'average' => round($avg, 2),
        ]);
    }

    public function store(Request $request, $bookId)
    {
        $user = Auth::user();
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:1000',
        ]);
        // Prevent duplicate reviews
        $existing = Rating::where('book_id', $bookId)->where('user_id', $user->id)->first();
        if ($existing) {
            return redirect()->back()->with('error', 'You have already reviewed this book.');
        }
        $review = Rating::create([
            'book_id' => $bookId,
            'user_id' => $user->id,
            'rating' => $request->rating,
            'review' => $request->review,
            'status' => 'pending', // Admin must approve
        ]);
        return redirect()->back()->with('success', 'Review submitted and pending approval.');
    }
} 