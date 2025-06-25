<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index () {
        return inertia('Admin/Profile/Profile');
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'barangay' => 'nullable|string|max:255',
            'is_student' => 'required|boolean',
            'school_name' => 'nullable|string|max:255',
            'verified' => 'required|boolean',
        ]);
        $user->update($validated);
        return redirect()->back()->with('success', 'Profile updated successfully!');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current' => ['required', 'current_password'],
            'new' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        $user = Auth::user();
        $user->password = Hash::make($request->new);
        $user->save();
        return redirect()->back()->with('success', 'Password changed successfully!');
    }

    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        $user = Auth::user();
        // Delete previous avatar if it exists
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }
        $file = $request->file('avatar');
        $path = $file->store('avatars', 'public');
        $user->avatar = $path;
        $user->save();
        $user->refresh();
        return redirect()->back()->with('success', 'Avatar updated!');
    }

    public function sendVerificationEmail(Request $request)
    {
        $user = Auth::user();
        if (method_exists($user, 'sendEmailVerificationNotification')) {
            $user->sendEmailVerificationNotification();
            return redirect()->back()->with('success', 'Verification email sent!');
        }
        return redirect()->back()->with('error', 'Unable to send verification email.');
    }

    /**
     * Get activity log for profile activity tab
     */
    public function activityLog()
    {
        $activities = \App\Models\Notification::orderBy('created_at', 'desc')
            ->limit(15)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'icon' => $notification->icon,
                    'color_class' => $notification->color_class,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'time_ago' => $notification->time_ago,
                ];
            });
        return response()->json(['activities' => $activities]);
    }
}
