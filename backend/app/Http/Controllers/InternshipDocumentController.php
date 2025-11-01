<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Internship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InternshipDocumentController extends Controller
{
    /**
     * Get all documents for a specific internship (Garant/Admin)
     */
    public function index($internshipId)
    {
        try {
            $internship = Internship::with('documents')->findOrFail($internshipId);

            return response()->json([
                'data' => $internship->documents->map(function ($doc) {
                    return [
                        'id' => $doc->id,
                        'internship_id' => $doc->internship_id,
                        'type' => $doc->type,
                        'status' => $doc->status,
                        'name' => $doc->name,
                        'created_at' => $doc->created_at->toIso8601String(),
                        'updated_at' => $doc->updated_at->toIso8601String(),
                    ];
                })
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Error fetching documents: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred while fetching documents.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Download a document (Garant/Admin) from PRIVATE storage
     */
    public function download($internshipId, $documentId)
    {
        try {
            $document = Document::where('internship_id', $internshipId)
                ->where('id', $documentId)
                ->firstOrFail();

            // Check if file exists in PRIVATE storage
            if (!Storage::disk('local')->exists($document->file_path)) {
                return response()->json([
                    'message' => 'File not found on server.'
                ], 404);
            }

            // Return file from private storage
            return Storage::disk('local')->download(
                $document->file_path,
                $document->name
            );

        } catch (\Exception $e) {
            \Log::error('Error downloading document: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred while downloading the document.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}
