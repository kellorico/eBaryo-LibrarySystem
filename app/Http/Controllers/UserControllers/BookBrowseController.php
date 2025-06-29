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
        $query = request()->input('search', '');
        $author = request()->input('author', '');
        $category = request()->input('category', '');
        $books = collect();
        if ($query || $author || $category) {
            $booksQuery = Book::query();
            if ($query) {
                $booksQuery->where(function($q) use ($query) {
                    $q->where('title', 'like', "%$query%")
                      ->orWhere('description', 'like', "%$query%")
                      ->orWhere('author', 'like', "%$query%")
                      ;
                });
            }
            if ($author) {
                $booksQuery->where('author', 'like', "%$author%") ;
            }
            if ($category) {
                $booksQuery->where('category_id', $category);
            }
            $books = $booksQuery->with('category')->get()->map(function($book) {
                $data = $book->toArray();
                if (!$data['cover_image']) {
                    $data['cover_image'] = asset('assets/images/image.png');
                } else {
                    $data['cover_image'] = asset('storage/' . ltrim($data['cover_image'], '/'));
                }
                $data['category'] = $book->category ? $book->category->only(['id','name']) : null;
                return $data;
            });
        }

        // Group books by category with recommended, latest, and top rated
        $categoriesWithBooks = $categories->map(function($cat) {
            $catBooks = Book::where('category_id', $cat->id)->get();
            // Recommended: random 4
            $recommendedBooks = $catBooks->shuffle()->take(4)->map(function($book) {
                $data = $book->toArray();
                if (!$data['cover_image']) {
                    $data['cover_image'] = asset('assets/images/image.png');
                } else {
                    $data['cover_image'] = asset('storage/' . ltrim($data['cover_image'], '/'));
                }
                return $data;
            });
            // Latest: newest 4
            $latestBooks = $catBooks->sortByDesc('created_at')->take(4)->map(function($book) {
                $data = $book->toArray();
                if (!$data['cover_image']) {
                    $data['cover_image'] = asset('assets/images/image.png');
                } else {
                    $data['cover_image'] = asset('storage/' . ltrim($data['cover_image'], '/'));
                }
                return $data;
            });
            // Top Rated: by avg rating, 4
            $topRatedBooks = $catBooks->sortByDesc(function($book) {
                return $book->ratings()->avg('rating') ?? 0;
            })->take(4)->map(function($book) {
                $data = $book->toArray();
                $data['avg_rating'] = (float) ($book->ratings()->avg('rating') ?? 0);
                $data['ratings_count'] = $book->ratings()->count();
                if (!$data['cover_image']) {
                    $data['cover_image'] = asset('assets/images/image.png');
                } else {
                    $data['cover_image'] = asset('storage/' . ltrim($data['cover_image'], '/'));
                }
                return $data;
            });
            return [
                'id' => $cat->id,
                'name' => $cat->name,
                'recommendedBooks' => $recommendedBooks->values(),
                'latestBooks' => $latestBooks->values(),
                'topRatedBooks' => $topRatedBooks->values(),
            ];
        });

        return Inertia::render('User/BrowseBooks', [
            'categories' => $categories,
            'books' => $books,
            'search' => $query,
            'searchAuthor' => $author,
            'searchCategory' => $category,
            'categoriesWithBooks' => $categoriesWithBooks,
        ]);
    }

    public function show($id)
    {
        $book = Book::with('category')->findOrFail($id);
        $user = auth()->user();
        $avgRating = $book->ratings()->avg('rating');
        if ($avgRating !== null) {
            $avgRating = (float) $avgRating;
        }
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