<?php

namespace App\Http\Controllers\UserControllers;

use App\Http\Controllers\Controller;
use App\Models\Suggestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\NotificationService;
use App\Models\User;
use App\Models\SuggestionUpvote;
use Inertia\Inertia;

class SuggestionController extends Controller
{
    // Store a new suggestion
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'suggestion' => 'required|string',
        ]);
        $data['user_id'] = Auth::id();
        $suggestion = Suggestion::create($data);
        // Notify admins
        if (class_exists(NotificationService::class)) {
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                NotificationService::system(
                    'New Suggestion',
                    $suggestion->name . ' submitted a suggestion.',
                    ['suggestion_id' => $suggestion->id],
                    $admin->id
                );
            }
        }
        return redirect()->back()->with('success', 'Thank you for your suggestion!');
    }

    public function upvote($id)
    {
        $user = Auth::user();
        $suggestion = Suggestion::findOrFail($id);
        if (!$suggestion->hasUpvoted($user->id)) {
            SuggestionUpvote::create([
                'user_id' => $user->id,
                'suggestion_id' => $suggestion->id,
            ]);
        }
        return redirect()->back()->with('success', 'Upvoted!');
    }

    public function listPublic()
    {
        $user = Auth::user();
        $suggestions = Suggestion::where('approved', true)
            ->withCount('upvotes')
            ->get()
            ->map(function ($suggestion) use ($user) {
                $suggestion->has_upvoted = $user ? $suggestion->hasUpvoted($user->id) : false;
                return $suggestion;
            });
        return Inertia::render('PublicSuggestions', [
            'suggestions' => $suggestions,
        ]);
    }
}
