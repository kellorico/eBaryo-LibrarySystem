<?php

namespace App\Http\Controllers\UserControllers;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\User;
use Inertia\Inertia;

class BookBrowseController extends Controller
{
    public function story()
    {
        $books = Book::where('category_id', 1)->get();
        return Inertia::render('User/Books/StoryBooks', [
            'books' => $books,
        ]);
    }

    public function educational()
    {
        $books = Book::where('category_id', 2)->get();
        return Inertia::render('User/Books/Educational', [
            'books' => $books,
        ]);
    }

    public function agriculture()
    {
        $books = Book::where('category_id', 3)->get();
        return Inertia::render('User/Books/Agriculture', [
            'books' => $books,
        ]);
    }

    public function cultural()
    {
        $books = Book::where('category_id', 4)->get();
        return Inertia::render('User/Books/CulturalHeritage', [
            'books' => $books,
        ]);
    }

    public function browse()
    {
        $categories = \App\Models\Categories::all(['id', 'name']);
        return Inertia::render('User/BrowseBooks', [
            'categories' => $categories,
        ]);
    }

    public function show($id)
    {
        $book = Book::with('category')->findOrFail($id);
        $user = auth()->user();
        $avgRating = $book->ratings()->avg('rating');
        $userReview = $user ? $book->ratings()->where('user_id', $user->id)->first() : null;
        $reviews = $book->ratings()->with('user:id,name')->latest()->get();
        $suggestions = $book->suggestions()->with('user:id,name')->latest()->get();

        // Fix cover_image URL
        $data = $book->toArray();
        if (!$data['cover_image']) {
            $data['cover_image'] = asset('assets/images/image.png');
        } else {
            $data['cover_image'] = asset('storage/' . ltrim($data['cover_image'], '/'));
        }
        $data['category'] = $book->category ? $book->category->only(['id','name']) : null;

        return Inertia::render('User/Books/BookDetails', [
            'book' => $data,
            'avgRating' => $avgRating,
            'userReview' => $userReview,
            'reviews' => $reviews,
            'suggestions' => $suggestions,
        ]);
    }
} 