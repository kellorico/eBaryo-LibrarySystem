<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rating;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Request as FacadeRequest;

class ReviewModerationController extends Controller
{
    // Show all pending and reported reviews with filtering and pagination
    public function index(Request $request)
    {
        $query = Rating::with(['user', 'book']);

        // Filtering
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('reported')) {
            $query->where('reported', $request->boolean('reported'));
        }
        
        // Advanced search by specific fields
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $searchField = $request->input('search_field', 'all');
            
            if ($searchField === 'user') {
                $query->whereHas('user', function ($q) use ($searchTerm) {
                    $q->where('name', 'like', '%' . $searchTerm . '%');
                });
            } elseif ($searchField === 'book') {
                $query->whereHas('book', function ($q) use ($searchTerm) {
                    $q->where('title', 'like', '%' . $searchTerm . '%');
                });
            } elseif ($searchField === 'review') {
                $query->where('review', 'like', '%' . $searchTerm . '%');
            } elseif ($searchField === 'report_reason') {
                $query->where('report_reason', 'like', '%' . $searchTerm . '%');
            } else {
                // Search in all fields
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('review', 'like', '%' . $searchTerm . '%')
                      ->orWhere('report_reason', 'like', '%' . $searchTerm . '%')
                      ->orWhereHas('user', function ($userQuery) use ($searchTerm) {
                          $userQuery->where('name', 'like', '%' . $searchTerm . '%');
                      })
                      ->orWhereHas('book', function ($bookQuery) use ($searchTerm) {
                          $bookQuery->where('title', 'like', '%' . $searchTerm . '%');
                      });
                });
            }
        }
        
        // Date range filtering
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        // Pagination
        $perPage = $request->input('per_page', 10);
        $reviews = $query->orderByDesc('created_at')->paginate($perPage)->withQueryString();

        // For legacy: also provide pending/reported for default view
        $pendingReviews = Rating::with(['user', 'book'])
            ->where('status', 'pending')
            ->orderByDesc('created_at')
            ->get();
        $reportedReviews = Rating::with(['user', 'book'])
            ->where('reported', true)
            ->orderByDesc('created_at')
            ->get();

        return Inertia::render('Admin/ReviewModeration', [
            'reviews' => $reviews,
            'pendingReviews' => $pendingReviews,
            'reportedReviews' => $reportedReviews,
            'filters' => $request->only(['status', 'reported', 'search', 'search_field', 'per_page', 'date_from', 'date_to']),
        ]);
    }

    // Bulk approve/reject/dismiss
    public function bulkAction(Request $request)
    {
        $ids = $request->input('ids', []);
        $action = $request->input('action');
        $note = $request->input('note');
        $reviews = Rating::whereIn('id', $ids)->get();
        foreach ($reviews as $review) {
            if ($action === 'approve') {
                $review->status = 'approved';
                $review->admin_note = $note;
                $review->save();
                if ($review->user && $review->book) {
                    NotificationService::reviewApproved($review->user, $review->book);
                }
            } elseif ($action === 'reject') {
                $review->status = 'rejected';
                $review->admin_note = $note;
                $review->save();
                if ($review->user && $review->book) {
                    NotificationService::reviewRejected($review->user, $review->book);
                }
            } elseif ($action === 'dismiss') {
                $review->reported = false;
                $review->report_reason = null;
                $review->admin_note = $note;
                $review->save();
            }
        }
        return back()->with('success', 'Bulk action completed.');
    }

    // Approve a review
    public function approve($id)
    {
        $review = Rating::findOrFail($id);
        $review->status = 'approved';
        $review->save();
        // Notify user
        if ($review->user && $review->book) {
            NotificationService::reviewApproved($review->user, $review->book);
        }
        return back()->with('success', 'Review approved.');
    }

    // Reject a review
    public function reject($id)
    {
        $review = Rating::findOrFail($id);
        $review->status = 'rejected';
        $review->save();
        // Notify user
        if ($review->user && $review->book) {
            NotificationService::reviewRejected($review->user, $review->book);
        }
        return back()->with('success', 'Review rejected.');
    }

    // Dismiss a report (mark as not reported)
    public function dismissReport($id)
    {
        $review = Rating::findOrFail($id);
        $review->reported = false;
        $review->report_reason = null;
        $review->save();
        // Optionally notify user or admin here if needed
        return back()->with('success', 'Report dismissed.');
    }
} 