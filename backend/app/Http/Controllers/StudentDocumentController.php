<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Internship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\InternshipPdfController;

class StudentDocumentController extends Controller
{
    public function getSignedAgreementMeta(Request $request, $internshipId)
    {
        $user = Auth::user();

        $internship = Internship::with('student')->findOrFail($internshipId);

        if (!$user || !$user->isStudent()) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        if (!$user->student || $internship->student_id !== $user->student->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $document = Document::where('internship_id', $internship->id)
            ->where('type', 'agreement_signed')
            ->first();

        // Vrátime 200 aj keď dokument neexistuje - toto je normálne
        if (!$document) {
            return response()->json([
                'document' => null
            ], 200);
        }

        return response()->json([
            'document' => [
                'id' => $document->id,
                'name' => $document->name,
                'status' => $document->status,
                'type' => $document->type,
                'created_at' => $document->created_at,
            ]
        ]);
    }
    public function uploadSignedAgreement(Request $request, $internshipId)
    {
        $user = Auth::user();

        $internship = Internship::with('student')
            ->findOrFail($internshipId);

        if (!$user || !$user->isStudent()) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        if (!$user->student || $internship->student_id !== $user->student->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if ($internship->status !== Internship::STATUS_SCHVALENA) {
            return response()->json([
                'message' => 'Upload je povolený až keď je prax v stave schválená.'
            ], 422);
        }

        $validated = $request->validate([
            'file' => 'required|file|mimes:pdf|max:10240',
        ]);

        $file = $validated['file'];
        $originalName = $file->getClientOriginalName();
        $path = $file->storeAs(
            'internships/' . $internship->id,
            'agreement_signed-' . time() . '.pdf',
            'public'
        );

        $existing = Document::where('internship_id', $internship->id)
            ->where('type', 'agreement_signed')
            ->first();

        if ($existing && $existing->file_path && Storage::disk('public')->exists($existing->file_path)) {
            Storage::disk('public')->delete($existing->file_path);
        }

        $document = Document::updateOrCreate(
            [
                'internship_id' => $internship->id,
                'type' => 'agreement_signed',
            ],
            [
                'status' => 'uploaded',
                'file_path' => $path,
                'name' => $originalName,
            ]
        );

        return response()->json([
            'message' => 'Podpísaná dohoda bola nahraná.',
            'document' => $document,
        ], 201);
    }

    public function downloadSignedAgreement(Request $request, $internshipId)
    {
        $user = Auth::user();

        $internship = Internship::with('student')
            ->findOrFail($internshipId);

        if (!$user || !$user->isStudent()) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        if (!$user->student || $internship->student_id !== $user->student->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $document = Document::where('internship_id', $internship->id)
            ->where('type', 'agreement_signed')
            ->first();

        if (!$document || !$document->file_path || !Storage::disk('public')->exists($document->file_path)) {
            return response()->json(['message' => 'Dokument neexistuje'], 404);
        }

        $filename = $document->name ?: 'agreement_signed.pdf';
        $fullPath = Storage::disk('public')->path($document->file_path);

        return response()->download($fullPath, $filename);
    }

    public function downloadGeneratedAgreement(Request $request, $internshipId)
    {
        $user = Auth::user();

        $internship = Internship::with('student')->findOrFail($internshipId);

        if (!$user || !$user->isStudent()) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        if (!$user->student || $internship->student_id !== $user->student->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $controller = app(InternshipPdfController::class);
        return $controller->generate($internship);
    }
}


