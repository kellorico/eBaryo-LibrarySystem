<?php

namespace App\Http\Controllers\UserControllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $request->user()->id,
            'barangay' => 'nullable|string|max:255',
            'is_student' => 'required|boolean',
            'school_name' => 'nullable|string|max:255',
        ]);
        $user = $request->user();
        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'barangay' => $validated['barangay'] ?? null,
            'is_student' => $validated['is_student'],
            'school_name' => $validated['is_student'] ? $validated['school_name'] : null,
        ]);
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }
        $user->save();
        return back();
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Show the user's profile page (for Inertia/Vue).
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $userId = $user->id;
        $bookmarkCount = \App\Models\Bookmark::where('user_id', $userId)->count();
        $readingProgressCount = \App\Models\ReadingProgress::where('user_id', $userId)->count();
        $challengesJoined = $user->readingChallenges()->count();
        $challengeProgress = $user->readingChallenges()->get()->map(function($challenge) use ($userId) {
            $pivot = $challenge->participants()->where('user_id', $userId)->first();
            return [
                'id' => $challenge->id,
                'title' => $challenge->title,
                'target_books' => $challenge->target_books,
                'progress' => $pivot ? $pivot->pivot->progress : 0,
                'completed_at' => $pivot ? $pivot->pivot->completed_at : null,
            ];
        });
        return Inertia::render('User/Profile', [
            'user' => $user,
            'stats' => [
                'bookmarks' => $bookmarkCount,
                'readingProgress' => $readingProgressCount,
                'challenges' => $challengesJoined,
            ],
            'challengeProgress' => $challengeProgress,
        ]);
    }

    /**
     * Change the user's password.
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current' => 'required',
            'new' => 'required|string|min:8|confirmed',
        ]);
        $user = $request->user();
        if (!Hash::check($request->input('current'), $user->password)) {
            throw ValidationException::withMessages([
                'current' => ['Current password is incorrect.'],
            ]);
        }
        $user->password = bcrypt($request->input('new'));
        $user->save();
        return back();
    }

    /**
     * Upload and update the user's avatar.
     */
    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|max:2048',
        ]);
        $user = $request->user();
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }
        $path = $request->file('avatar')->store('avatars', 'public');
        $user->avatar = $path;
        $user->save();
        return back();
    }

    /**
     * Show the profile completion form after registration.
     */
    public function complete(Request $request)
    {
        return Inertia::render('User/CompleteProfile', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Store the completed profile fields.
     */
    public function storeComplete(Request $request)
    {
        $request->validate([
            'barangay' => 'required|string|max:255',
            'is_student' => 'required|boolean',
            'school_name' => 'nullable|string|max:255',
        ]);
        $user = $request->user();
        $user->barangay = $request->barangay;
        $user->is_student = $request->is_student;
        $user->school_name = $request->school_name;
        $user->save();
        return redirect()->route('home');
    }

    /**
     * Show the user's saved books (bookmarks).
     */
    public function savedBooks(Request $request)
    {
        $user = $request->user();
        $bookmarks = $user->bookmarks()->with('book')->get()->map(function($bm) {
            return $bm->book;
        })->filter();
        return Inertia::render('User/SavedBooks', [
            'books' => $bookmarks->values(),
        ]);
    }
}
