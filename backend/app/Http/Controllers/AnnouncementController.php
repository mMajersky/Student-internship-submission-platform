<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of announcements (admin view - all announcements)
     */
    public function index()
    {
        $announcements = Announcement::with(['creator', 'updater'])
            ->orderBy('updated_at', 'desc')
            ->get();

        return response()->json($announcements);
    }

    /**
     * Get published announcements for public display
     */
    public function published()
    {
        $announcement = Announcement::published()
            ->orderBy('updated_at', 'desc')
            ->first();

        if (!$announcement) {
            return response()->json([
                'content_sanitized' => null,
                'updated_at' => null
            ]);
        }

        return response()->json([
            'content_sanitized' => $announcement->content_sanitized,
            'updated_at' => $announcement->updated_at,
        ]);
    }

    /**
     * Store a newly created announcement
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|string',
            'is_published' => 'boolean',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['updated_by'] = Auth::id();

        $announcement = Announcement::create($validated);

        return response()->json($announcement, 201);
    }

    /**
     * Display the specified announcement
     */
    public function show(string $id)
    {
        $announcement = Announcement::with(['creator', 'updater'])->findOrFail($id);
        return response()->json($announcement);
    }

    /**
     * Update the specified announcement
     */
    public function update(Request $request, string $id)
    {
        $announcement = Announcement::findOrFail($id);

        $validated = $request->validate([
            'content' => 'sometimes|required|string',
            'is_published' => 'sometimes|boolean',
        ]);

        $validated['updated_by'] = Auth::id();

        $announcement->update($validated);

        return response()->json($announcement);
    }

    /**
     * Remove the specified announcement
     */
    public function destroy(string $id)
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->delete();

        return response()->json(['message' => 'Announcement deleted successfully']);
    }
}
