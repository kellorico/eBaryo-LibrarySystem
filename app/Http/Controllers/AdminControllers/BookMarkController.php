<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Bookmark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class BookMarkController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
        ]);

        $userId = Auth::id();

        $exists = Bookmark::where('user_id', $userId)
            ->where('book_id', $request->book_id)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Bookmark already exists'], 409);
        }

        Bookmark::create([
            'user_id' => $userId,
            'book_id' => $request->book_id,
        ]);

        return response()->json(['message' => 'Bookmark created successfully'], 201);
    }

    public function index()
    {
        $userId = Auth::id();
        $bookmarks = Bookmark::where('user_id', $userId)->with('book')->get();
        $books = $bookmarks->map(fn($bm) => $bm->book)->filter()->values();
        return Inertia::render('User/Bookmarks', [
            'books' => $books,
        ]);
    }

    public function destroy($book_id)
    {
        $userId = Auth::id();
        $bookmark = Bookmark::where('user_id', $userId)->where('book_id', $book_id)->first();
        if ($bookmark) {
            $bookmark->delete();
            return response()->json(['message' => 'Bookmark removed']);
        }
        return response()->json(['message' => 'Bookmark not found'], 404);
    }
}
