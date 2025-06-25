<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReadingChallenge;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReadingChallengeMail;

class ReadingChallengeController extends Controller
{
    // Admin: List all challenges
    public function index()
    {
        $challenges = ReadingChallenge::with('creator')->orderByDesc('start_date')->paginate(15);
        return Inertia::render('Admin/ReadingChallenges', [
            'challenges' => $challenges,
        ]);
    }

    // Admin: Create a challenge
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'target_books' => 'required|integer|min:1',
            'send_email' => 'boolean',
            'custom_subject' => 'nullable|string|max:255',
            'custom_intro' => 'nullable|string|max:255',
        ]);
        $data['created_by'] = Auth::id();
        $challenge = ReadingChallenge::create($data);
        // Send email if requested
        if (!empty($data['send_email'])) {
            $users = User::whereNotNull('email')->get();
            foreach ($users as $user) {
                Mail::to($user->email)->send(new ReadingChallengeMail($challenge, $data['custom_subject'] ?? null, $data['custom_intro'] ?? null));
            }
        }
        return redirect()->back()->with('success', 'Challenge created!');
    }

    // Admin: Delete a challenge
    public function destroy($id)
    {
        $challenge = ReadingChallenge::findOrFail($id);
        $challenge->delete();
        return redirect()->back()->with('success', 'Challenge deleted.');
    }

    // User: List active challenges
    public function list()
    {
        $today = Carbon::today();
        $challenges = ReadingChallenge::where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->orderBy('end_date')
            ->get();
        return response()->json(['challenges' => $challenges]);
    }

    // User: Join a challenge
    public function join($id)
    {
        $challenge = ReadingChallenge::findOrFail($id);
        $user = Auth::user();
        $user->readingChallenges()->syncWithoutDetaching([$challenge->id]);
        return response()->json(['success' => true]);
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
        return response()->json(['success' => true]);
    }

    // Leaderboard for a challenge
    public function leaderboard($id)
    {
        $challenge = ReadingChallenge::findOrFail($id);
        $users = $challenge->participants()
            ->orderByDesc('pivot_progress')
            ->orderBy('users.name')
            ->get(['users.id', 'users.name', 'pivot_progress', 'pivot_completed_at']);
        return response()->json(['leaderboard' => $users]);
    }
} 