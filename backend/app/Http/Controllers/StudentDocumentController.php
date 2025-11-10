<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Internship;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class StudentDocumentController extends Controller
{
    /**
     * Get metadata about signed agreement (check if exists)
     */
    public function getSignedAgreementMeta($internshipId)
    {
        try {
            $user = Auth::user();

            // Check if user has student profile
            if (!$user->student) {
                return response()->json([
                    'message' => 'User does not have a student profile.'
                ], 403);
            }

            $internship = Internship::with('student')->findOrFail($internshipId);

            // Check authorization
            if ($internship->student_id !== $user->student->id) {
                return response()->json([
                    'message' => 'Unauthorized.'
                ], 403);
            }

            // Find signed agreement document
            $signedDoc = Document::where('internship_id', $internshipId)
                ->where('type', 'podpisana_dohoda')
                ->first();

            if (!$signedDoc) {
                return response()->json([
                    'document' => null,
                    'message' => 'Podpísaná dohoda nebola ešte nahraná.'
                ], 200);
            }

            return response()->json([
                'document' => [
                    'id' => $signedDoc->id,
                    'name' => $signedDoc->name,
                    'created_at' => $signedDoc->created_at->toIso8601String(),
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error getting signed agreement meta: ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Upload signed agreement (student only) - STORES IN PRIVATE STORAGE
     */
    public function uploadSignedAgreement(Request $request, $internshipId)
    {
        try {
            $user = Auth::user();

            // Check if user has student profile
            if (!$user->student) {
                return response()->json([
                    'message' => 'User does not have a student profile.'
                ], 403);
            }

            $internship = Internship::with('student')->findOrFail($internshipId);

            // Check authorization
            if ($internship->student_id !== $user->student->id) {
                return response()->json([
                    'message' => 'Unauthorized to upload documents for this internship.'
                ], 403);
            }

            $validated = $request->validate([
                'file' => [
                    'required',
                    'file',
                    'mimes:pdf',
                    'max:10240' // 10MB max
                ]
            ]);

            // Load garant relationship
            $internship->load('garant.user');

            $file = $request->file('file');
            
            // Generate unique filename
            $filename = 'podpisana_dohoda_' . time() . '_' . uniqid() . '.pdf';
            
            // IMPORTANT: Store in PRIVATE storage (local disk) for security
            // This ensures signed documents are NOT publicly accessible
            $path = $file->storeAs(
                'documents/internship_' . $internshipId,
                $filename,
                'local' // Uses private storage (storage/app/private)
            );

            // Check if signed agreement already exists, update or create
            $signedDoc = Document::where('internship_id', $internshipId)
                ->where('type', 'podpisana_dohoda')
                ->first();

            if ($signedDoc) {
                // Delete old file
                if (Storage::disk('local')->exists($signedDoc->file_path)) {
                    Storage::disk('local')->delete($signedDoc->file_path);
                }
                
                // Update document record
                $signedDoc->update([
                    'file_path' => $path,
                    'name' => $file->getClientOriginalName(),
                    'status' => 'uploaded',
                ]);
            } else {
                // Create new document record
                $signedDoc = Document::create([
                    'internship_id' => $internshipId,
                    'type' => 'podpisana_dohoda',
                    'status' => 'uploaded',
                    'file_path' => $path,
                    'name' => $file->getClientOriginalName(),
                ]);
            }

            // Create notification for garant about uploaded document
            if ($internship->garant && $internship->garant->user) {
                NotificationService::create(
                    $internship->garant->user->id,
                    Notification::TYPE_DOCUMENT_UPLOADED,
                    'Študent nahral dokument',
                    'Študent ' . $internship->student->name . ' ' . $internship->student->surname . ' nahral podpísanú dohodu k praxi.',
                    ['internship_id' => $internshipId, 'document_id' => $signedDoc->id]
                );
            }

            return response()->json([
                'message' => 'Podpísaná dohoda bola úspešne nahraná do zabezpečeného úložiska.',
                'document' => [
                    'id' => $signedDoc->id,
                    'name' => $signedDoc->name,
                    'created_at' => $signedDoc->created_at->toIso8601String(),
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error uploading signed agreement: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred while uploading the document.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Download signed agreement (from PRIVATE storage)
     */
    public function downloadSignedAgreement($internshipId)
    {
        try {
            $user = Auth::user();

            // Check if user has student profile
            if (!$user->student) {
                return response()->json([
                    'message' => 'User does not have a student profile.'
                ], 403);
            }

            $internship = Internship::with('student')->findOrFail($internshipId);

            // Check authorization
            if ($internship->student_id !== $user->student->id) {
                return response()->json([
                    'message' => 'Unauthorized.'
                ], 403);
            }

            $signedDoc = Document::where('internship_id', $internshipId)
                ->where('type', 'podpisana_dohoda')
                ->first();

            if (!$signedDoc) {
                return response()->json([
                    'message' => 'Podpísaná dohoda nebola nájdená.'
                ], 404);
            }

            // Check if file exists in PRIVATE storage
            if (!Storage::disk('local')->exists($signedDoc->file_path)) {
                return response()->json([
                    'message' => 'File not found on server.'
                ], 404);
            }

            // Return file from private storage with proper authorization
            return Storage::disk('local')->download(
                $signedDoc->file_path,
                $signedDoc->name
            );

        } catch (\Exception $e) {
            Log::error('Error downloading signed agreement: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred while downloading the document.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Download generated agreement (PDF generation on-the-fly)
     */
    public function downloadGeneratedAgreement($internshipId)
    {
        try {
            $user = Auth::user();

            // Check if user has student profile
            if (!$user->student) {
                return response()->json([
                    'message' => 'User does not have a student profile.'
                ], 403);
            }

            $internship = Internship::with(['student', 'company.contactPersons'])->findOrFail($internshipId);

            // Check authorization
            if ($internship->student_id !== $user->student->id) {
                return response()->json([
                    'message' => 'Unauthorized.'
                ], 403);
            }

            $student = $internship->student;
            $company = $internship->company;
            
            if (!$student || !$company) {
                abort(500, 'K stáži chýba priradený študent alebo firma.');
            }

            $tutor = $company->contactPersons->first();

            $data = [
                'student_name' => $student->name . ' ' . $student->surname,
                'student_address' => $this->formatAddress($student),
                'student_contact' => $student->student_email,
                'study_program' => 'Aplikovaná informatika',
                'company_name' => $company->name,
                'company_address' => $this->formatAddress($company),
                'company_contact' => $tutor ? $tutor->name . ' ' . $tutor->surname : '......................',
                'tutor_name' => $tutor ? $tutor->name . ' ' . $tutor->surname : '.....................................',
                'start_date' => Carbon::parse($internship->start_date)->format('d.m.Y'),
                'end_date' => Carbon::parse($internship->end_date)->format('d.m.Y'),
                'generation_date' => now()->format('d.m.Y'),
            ];

            $pdf = Pdf::loadView('pdf.internship_agreement', $data);
            $pdf->setPaper('A4', 'portrait');
            // Use setOptions for DomPDF to avoid undefined method errors
            if (method_exists($pdf, 'setOptions')) {
                $pdf->setOptions(['isHtml5ParserEnabled' => true]);
            }

            return $pdf->download('Dohoda_o_odbornej_praxi_' . $student->surname . '.pdf');

        } catch (\Exception $e) {
            Log::error('Error generating agreement: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred while generating the document.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Delete signed agreement (student only)
     */
    public function deleteSignedAgreement($internshipId)
    {
        try {
            $user = Auth::user();

            // Check if user has student profile
            if (!$user->student) {
                return response()->json([
                    'message' => 'User does not have a student profile.'
                ], 403);
            }

            $internship = Internship::with('student')->findOrFail($internshipId);

            // Check authorization
            if ($internship->student_id !== $user->student->id) {
                return response()->json([
                    'message' => 'Unauthorized.'
                ], 403);
            }

            $signedDoc = Document::where('internship_id', $internshipId)
                ->where('type', 'podpisana_dohoda')
                ->first();

            if (!$signedDoc) {
                return response()->json([
                    'message' => 'Podpísaná dohoda nebola nájdená.'
                ], 404);
            }

            // Delete file from PRIVATE storage
            if (Storage::disk('local')->exists($signedDoc->file_path)) {
                Storage::disk('local')->delete($signedDoc->file_path);
            }

            // Delete document record
            $signedDoc->delete();

            return response()->json([
                'message' => 'Dokument bol úspešne odstránený.'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error deleting signed agreement: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred while deleting the document.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Helper to format address
     */
    private function formatAddress($entity)
    {
        if (!$entity) {
            return '...................................';
        }

        $parts = array_filter([
            $entity->street ?? '',
            $entity->house_number ?? '',
            $entity->postal_code ?? '',
            $entity->city ?? '',
        ]);

        return !empty($parts) ? implode(' ', $parts) : '...................................';
    }
}
