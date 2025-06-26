<?php

namespace App\Http\Controllers\AdminControllers;

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

    
}
