<?php

namespace App\Http\Controllers\UserControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ReadingChallenge;
use Carbon\Carbon;
use Inertia\Inertia;   

class UserReadingChallengeController extends Controller
{
    // User: List active challenges
    public function list()
    {
        $today = Carbon::today();
        $challenges = ReadingChallenge::where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->orderBy('end_date')
            ->get();
        return Inertia::render('User/Challenges', [
            'challenges' => $challenges,
        ]);
    }

    // User: Join a challenge
    public function join($id)
    {
        $challenge = ReadingChallenge::findOrFail($id);
        $user = Auth::user();
        $user->readingChallenges()->syncWithoutDetaching([$challenge->id]);
        return redirect()->back()->with('success', 'Challenge joined!');
    }

    // User: Progress (books read)
    public function progress($id, Request $request)
    {
        $challenge = ReadingChallenge::findOrFail($id);
        $user = Auth::user();
        $progress = $request->input('progress');
        $user->readingChallenges()->updateExistingPivot($challenge->id, [
            'progress' => $progress,
            'completed_at' => $progress >= $challenge->target_books ? now() : null,
        ]);
        return redirect()->back()->with('success', 'Progress updated!');
    }

    // Leaderboard for a challenge
    public function leaderboard($id)
    {
        $challenge = ReadingChallenge::findOrFail($id);
        $users = $challenge->participants()
            ->orderByDesc('pivot_progress')
            ->orderBy('users.name')
            ->get(['users.id', 'users.name', 'pivot_progress', 'pivot_completed_at']);
        return Inertia::render('User/ChallengeLeaderboard', [
            'leaderboard' => $users,
        ]);
    }
}
