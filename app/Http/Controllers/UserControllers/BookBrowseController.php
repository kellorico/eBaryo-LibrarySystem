<?php

namespace App\Http\Controllers\UserControllers;

use App\Http\Controllers\Controller;
use App\Models\Book;
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
} 