<?php

namespace App\Http\Controllers\Admin\Books;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AgricultureandLivelihoodBookController extends Controller
{
    public function index()
    {
        $books = Book::with('category')->where('category_id', 3)->get();

        // Prepend full URL to cover_image path
        $books->transform(function ($book) {
            $book->cover_image = $book->cover_image
                ? asset('storage/' . $book->cover_image)
                : null;
            return $book;
        });

        return inertia('Admin/Books/AgricultureAndLivelihood', [
            'books' => $books,
        ]);
    }

    public function store(Request $request)
    {
        // Validate and store the book data
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file_path' => 'required|file|mimes:pdf,epub',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'published_year' => 'required|integer|min:1900|max:' . date('Y'),
            'category_id' => 'required|exists:categories,id',
        ]);

        // Handle file uploads
        if ($request->hasFile('file_path')) {
            $data['file_path'] = $request->file('file_path')->store('books/agriculture&livelihood_books', 'public');
        }

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('books/cover_images', 'public');
        }

        // Create the book
        Book::create($data);

        return redirect()->back()->with('success', 'Book added successfully!');
    }

    public function update(Request $request, $id)
    {
        // Find the book first
        $book = Book::findOrFail($id);

        // Validate the request data
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'description' => 'required|string',
            'file_path' => 'nullable|file|mimes:pdf,epub|max:10240', // 10MB max
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'published_year' => 'required|integer|min:1900|max:' . date('Y'),
            'category_id' => 'required|exists:categories,id',
        ]);

        // Prepare data for update, starting with non-file fields
        $updateData = $request->only(['title', 'author', 'description', 'published_year', 'category_id']);

        // Handle file_path upload
        if ($request->hasFile('file_path')) {
            // Delete old file if it exists
            if ($book->file_path && Storage::disk('public')->exists($book->file_path)) {
                Storage::disk('public')->delete($book->file_path);
            }
            // Store new file and add to update data
            $updateData['file_path'] = $request->file('file_path')->store('books/story_books', 'public');
        }

        // Handle cover_image upload
        if ($request->hasFile('cover_image')) {
            // Delete old cover image if it exists
            if ($book->cover_image && Storage::disk('public')->exists($book->cover_image)) {
                Storage::disk('public')->delete($book->cover_image);
            }
            // Store new image and add to update data
            $updateData['cover_image'] = $request->file('cover_image')->store('books/cover_images', 'public');
        }

        // Update the book with the prepared data
        $book->update($updateData);

        // For API/AJAX requests, return JSON response
        if ($request->expectsJson()) {
            // Reload the book with fresh data including asset URLs
            $book->refresh();
            $book->cover_image = $book->cover_image
                ? asset('storage/' . $book->cover_image)
                : null;

            return response()->json([
                'message' => 'Book updated successfully!',
                'book' => $book
            ]);
        }

        return to_route('storybooks')->with('success', 'Book updated successfully!');
    }

    public function destroy($id)
    {
        $book = Book::findOrFail($id);

        // Delete files from storage
        if ($book->file_path && Storage::disk('public')->exists($book->file_path)) {
            Storage::disk('public')->delete($book->file_path);
        }

        if ($book->cover_image && Storage::disk('public')->exists($book->cover_image)) {
            Storage::disk('public')->delete($book->cover_image);
        }

        // Delete book record
        $book->delete();

        return redirect()->back()->with('success', 'Book deleted successfully!');
    }
}
