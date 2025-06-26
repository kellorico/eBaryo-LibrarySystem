<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Suggestion;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\SuggestionResponseMail;
use App\Models\User;
use App\Services\NotificationService;

class SuggestionController extends Controller
{
    // List all suggestions
    public function index()
    {
        $suggestions = Suggestion::with('user')->orderByDesc('created_at')->paginate(20);
        return Inertia::render('Admin/Suggestions', [
            'suggestions' => $suggestions,
        ]);
    }

    // Approve or reject a suggestion
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);
        $suggestion = Suggestion::findOrFail($id);
        $suggestion->status = $request->status;
        $suggestion->responded_at = now();
        $suggestion->save();
        // Notify user
        if ($suggestion->email) {
            $subject = $suggestion->status === 'approved' ? 'Your suggestion was approved!' : 'Your suggestion was rejected.';
            Mail::raw($subject . "\n\nSuggestion: " . $suggestion->suggestion, function ($message) use ($suggestion, $subject) {
                $message->to($suggestion->email)->subject('[eBaryo] ' . $subject);
            });
        }
        if ($suggestion->user_id && class_exists(NotificationService::class)) {
            NotificationService::system(
                'Suggestion ' . ucfirst($suggestion->status),
                'Your suggestion has been ' . $suggestion->status . '.',
                ['suggestion_id' => $suggestion->id],
                $suggestion->user_id
            );
        }
        return redirect()->back()->with('success', 'Suggestion status updated.');
    }

    // Respond to a suggestion
    public function respond(Request $request, $id)
    {
        $request->validate([
            'admin_response' => 'required|string',
        ]);
        $suggestion = Suggestion::findOrFail($id);
        $suggestion->admin_response = $request->admin_response;
        $suggestion->responded_at = now();
        $suggestion->save();
        // Email user
        if ($suggestion->email) {
            Mail::to($suggestion->email)->send(new SuggestionResponseMail($suggestion, $request->admin_response));
        }
        if ($suggestion->user_id && class_exists(NotificationService::class)) {
            NotificationService::system(
                'Suggestion Response',
                'An admin has responded to your suggestion.',
                ['suggestion_id' => $suggestion->id],
                $suggestion->user_id
            );
        }
        return redirect()->back()->with('success', 'Response sent.');
    }

    // Public: List approved suggestions (Inertia page)
    public function publicList()
    {
        $suggestions = Suggestion::where('status', 'approved')->orderByDesc('responded_at')->get();
        return Inertia::render('PublicSuggestions', [
            'suggestions' => $suggestions,
        ]);
    }
}
