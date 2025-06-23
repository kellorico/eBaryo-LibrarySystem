<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Bookmark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookMarkController extends Controller
{
    public function store (Request $request) {
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
}
