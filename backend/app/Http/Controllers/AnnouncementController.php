<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class AnnouncementController extends Controller
{
    public function published()
    {
        $announcement = Cache::tags(['announcements'])->remember('published', now()->addDay(), function() {
            return Announcement::published()
                ->orderBy('updated_at', 'desc')
                ->first();
        });

        return response()->json([
            'content_sanitized' => $announcement?->content_sanitized ?? null,
            'updated_at' => $announcement?->updated_at ?? null,
        ]);
    }

    /**
     * Get or update the single announcement for landing page
     */
    public function single(Request $request)
    {
        if ($request->isMethod('GET')) {
            // Get the current announcement (published or unpublished for editing)
            $announcement = Cache::tags(['announcements'])->remember('single', now()->addDay(), function() {
                return Announcement::orderBy('updated_at', 'desc')->first();
            });

            return response()->json([
                'content' => $announcement?->content ?? '',
                'content_sanitized' => $announcement?->content_sanitized ?? '',
                'is_published' => $announcement?->is_published ?? false,
                'updated_at' => $announcement?->updated_at ?? null,
            ]);
        }

        if ($request->isMethod('PUT')) {
            // Update the single announcement
            $validated = $request->validate([
                'content' => 'required|string',
                'is_published' => 'boolean',
            ]);

            // Find existing announcement or create new one
            $announcement = Announcement::orderBy('updated_at', 'desc')->first();
            
            if ($announcement) {
                // Update existing announcement
                $announcement->update([
                    'content' => $validated['content'],
                    'is_published' => $validated['is_published'] ?? true,
                    'updated_by' => Auth::id(),
                ]);
            } else {
                // Create new announcement
                $announcement = Announcement::create([
                    'content' => $validated['content'],
                    'is_published' => $validated['is_published'] ?? true,
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ]);
            }

            // Clear announcement caches immediately after update
            Cache::tags(['announcements'])->flush();

            return response()->json([
                'content' => $announcement->content,
                'content_sanitized' => $announcement->content_sanitized,
                'is_published' => $announcement->is_published,
                'updated_at' => $announcement->updated_at,
            ]);
        }
    }
}
