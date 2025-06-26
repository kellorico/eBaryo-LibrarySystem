<?php

namespace App\Http\Controllers\UserControllers;

use App\Http\Controllers\Controller;
use App\Models\ReadingProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ReadingProgressController extends Controller
{
    public function store(Request $request, $bookId)
    {
        $user = Auth::user();
        $request->validate(['progress' => 'required|integer|min:0']);
        $rp = ReadingProgress::updateOrCreate(
            ['user_id' => $user->id, 'book_id' => $bookId],
            ['progress' => $request->progress]
        );
        return redirect()->back()->with('success', 'Progress saved.');
    }

    public function index()
    {
        $user = Auth::user();
        $progress = ReadingProgress::where('user_id', $user->id)->with('book')->get();
        return Inertia::render('ReadingProgress', [
            'progress' => $progress,
        ]);
    }
} 