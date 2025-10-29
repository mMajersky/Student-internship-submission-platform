<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Internship;
use Illuminate\Support\Facades\Storage;

class InternshipDocumentController extends Controller
{
    public function index($internshipId)
    {
        $internship = Internship::findOrFail($internshipId);

        $documents = Document::where('internship_id', $internship->id)
            ->orderBy('created_at', 'desc')
            ->get(['id', 'type', 'status', 'name', 'file_path', 'created_at']);

        return response()->json([
            'data' => $documents,
        ]);
    }

    public function download($internshipId, $documentId)
    {
        $doc = Document::where('internship_id', $internshipId)
            ->where('id', $documentId)
            ->firstOrFail();

        if (!$doc->file_path || !Storage::disk('public')->exists($doc->file_path)) {
            return response()->json(['message' => 'Súbor neexistuje'], 404);
        }

        $filename = $doc->name ?: 'document.pdf';
        $fullPath = Storage::disk('public')->path($doc->file_path);
        return response()->download($fullPath, $filename);
    }
}




