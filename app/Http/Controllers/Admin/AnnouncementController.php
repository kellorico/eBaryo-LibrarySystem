<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class AnnouncementController extends Controller
{
    // List all announcements (admin view)
    public function index()
    {
        $announcements = Announcement::with('creator')->orderByDesc('created_at')->paginate(15);
        return Inertia::render('Admin/Announcements', [
            'announcements' => $announcements,
        ]);
    }

    // Store a new announcement
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|string',
            'is_urgent' => 'boolean',
            'send_email' => 'boolean',
            'custom_subject' => 'nullable|string|max:255',
            'custom_intro' => 'nullable|string|max:255',
        ]);
        $data['created_by'] = Auth::id();
        $announcement = Announcement::create($data);

        // Send notification if urgent
        if ($announcement->is_urgent) {
            NotificationService::system(
                'Urgent Announcement',
                $announcement->title . ': ' . $announcement->content,
                ['announcement_id' => $announcement->id]
            );
        }
        // Send email if requested
        if ($announcement->send_email) {
            $users = User::whereNotNull('email')->get();
            foreach ($users as $user) {
                \Mail::to($user->email)->send(new \App\Mail\AnnouncementMail($announcement, $data['custom_subject'] ?? null, $data['custom_intro'] ?? null));
            }
        }
        return redirect()->back()->with('success', 'Announcement posted successfully.');
    }

    // Delete an announcement
    public function destroy($id)
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->delete();
        return redirect()->back()->with('success', 'Announcement deleted.');
    }

    // List for users (API/JSON)
    public function list()
    {
        $announcements = Announcement::orderByDesc('is_urgent')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();
        return response()->json(['announcements' => $announcements]);
    }
}
