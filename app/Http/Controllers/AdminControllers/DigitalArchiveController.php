<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\Archive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class DigitalArchiveController extends Controller
{
    public function index()
    {
        $archives = Archive::with('uploader')->latest()->get();
        return Inertia::render('Admin/DigitalArchive', [
            'archives' => $archives,
        ]);
    }

    // Public listing for community
    public function publicIndex()
    {
        $archives = Archive::where('is_public', true)->latest()->get();
        return Inertia::render('Public/DigitalArchive', [
            'archives' => $archives,
        ]);
    }

    // Public download
    public function publicDownload($id)
    {
        $archive = Archive::where('is_public', true)->findOrFail($id);
        if (!Storage::disk('public')->exists($archive->file_path)) {
            abort(404);
        }
        return Storage::disk('public')->download($archive->file_path, $archive->title);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png,docx,xlsx,zip|max:20480', // 20MB max
            'type' => 'required|string',
            'category' => 'nullable|string|max:255',
            'is_public' => 'nullable|boolean',
        ]);
        $file = $request->file('file');
        $path = $file->store('archives', 'public');
        $archive = Archive::create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'file_path' => $path,
            'type' => $data['type'],
            'uploaded_by' => Auth::id(),
            'category' => $data['category'] ?? null,
            'is_public' => $request->boolean('is_public'),
        ]);
        return redirect()->back()->with('success', 'Archive uploaded successfully!');
    }

    public function destroy($id)
    {
        $archive = Archive::findOrFail($id);
        // Allow only uploader or admin to delete
        if (Auth::id() !== $archive->uploaded_by && Auth::user()->role !== 'admin') {
            abort(403);
        }
        if ($archive->file_path && Storage::disk('public')->exists($archive->file_path)) {
            Storage::disk('public')->delete($archive->file_path);
        }
        $archive->delete();
        return redirect()->back()->with('success', 'Archive deleted successfully!');
    }

    public function download($id)
    {
        $archive = Archive::findOrFail($id);
        if (!Storage::disk('public')->exists($archive->file_path)) {
            abort(404);
        }
        return Storage::disk('public')->download($archive->file_path, $archive->title);
    }
}
