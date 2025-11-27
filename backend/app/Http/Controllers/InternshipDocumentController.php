<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Internship;
use App\Models\Company;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

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
                        'company_status' => $doc->company_status,
                        'company_rejection_reason' => $doc->company_rejection_reason,
                        'company_validated_at' => $doc->company_validated_at?->toIso8601String(),
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

    /**
     * Get all documents for a specific internship (Company only - validates ownership)
     */
    public function companyIndex($internshipId)
    {
        try {
            $user = Auth::user();
            
            // Get company profile from user
            $company = Company::where('user_id', $user->id)->first();
            
            if (!$company) {
                return response()->json([
                    'message' => 'Company profile not found for this user.'
                ], 403);
            }
            
            // Verify internship belongs to this company
            $internship = Internship::with('documents')
                ->where('id', $internshipId)
                ->where('company_id', $company->id)
                ->firstOrFail();

            return response()->json([
                'data' => $internship->documents->map(function ($doc) {
                    return [
                        'id' => $doc->id,
                        'internship_id' => $doc->internship_id,
                        'type' => $doc->type,
                        'status' => $doc->status,
                        'name' => $doc->name,
                        'company_status' => $doc->company_status,
                        'company_rejection_reason' => $doc->company_rejection_reason,
                        'company_validated_at' => $doc->company_validated_at?->toIso8601String(),
                        'created_at' => $doc->created_at->toIso8601String(),
                        'updated_at' => $doc->updated_at->toIso8601String(),
                    ];
                })
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Internship not found or access denied.'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Error fetching company documents: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred while fetching documents.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Download a document (Company only - validates ownership)
     */
    public function companyDownload($internshipId, $documentId)
    {
        try {
            $user = Auth::user();
            
            // Get company profile from user
            $company = Company::where('user_id', $user->id)->first();
            
            if (!$company) {
                return response()->json([
                    'message' => 'Company profile not found for this user.'
                ], 403);
            }
            
            // Verify internship belongs to this company
            $internship = Internship::where('id', $internshipId)
                ->where('company_id', $company->id)
                ->firstOrFail();
            
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

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Document not found or access denied.'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Error downloading company document: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred while downloading the document.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Validate (approve/reject) a document (Company only)
     */
    public function companyValidate(Request $request, $internshipId, $documentId)
    {
        try {
            $user = Auth::user();
            
            // Get company profile from user
            $company = Company::where('user_id', $user->id)->first();
            
            if (!$company) {
                return response()->json([
                    'message' => 'Company profile not found for this user.'
                ], 403);
            }
            
            // Verify internship belongs to this company
            $internship = Internship::with(['student.user', 'garant.user'])
                ->where('id', $internshipId)
                ->where('company_id', $company->id)
                ->firstOrFail();
            
            $document = Document::where('internship_id', $internshipId)
                ->where('id', $documentId)
                ->firstOrFail();

            // Validate request
            $validated = $request->validate([
                'action' => 'required|in:approve,reject',
                'rejection_reason' => 'required_if:action,reject|nullable|string|max:1000',
            ], [
                'action.required' => 'Action is required.',
                'action.in' => 'Action must be either approve or reject.',
                'rejection_reason.required_if' => 'Rejection reason is required when rejecting a document.',
            ]);

            // Update document
            $document->company_status = $validated['action'] === 'approve' ? 'schválený' : 'zamietnutý';
            $document->company_rejection_reason = $validated['action'] === 'reject' ? $validated['rejection_reason'] : null;
            $document->company_validated_at = Carbon::now();
            $document->save();

            // Send notification to student about document validation
            if ($internship->student && $internship->student->user) {
                $notificationTitle = $validated['action'] === 'approve' 
                    ? 'Dokument bol schválený firmou' 
                    : 'Dokument bol zamietnutý firmou';
                $notificationMessage = $validated['action'] === 'approve'
                    ? 'Firma ' . $company->name . ' schválila váš dokument: ' . $document->name
                    : 'Firma ' . $company->name . ' zamietla váš dokument: ' . $document->name . '. Dôvod: ' . $validated['rejection_reason'];

                NotificationService::create(
                    $internship->student->user->id,
                    Notification::TYPE_DOCUMENT_STATUS_CHANGED ?? 'document_status_changed',
                    $notificationTitle,
                    $notificationMessage,
                    ['document_id' => $document->id, 'internship_id' => $internship->id]
                );
            }

            // Send notification to garant about document validation
            if ($internship->garant && $internship->garant->user) {
                $notificationTitle = $validated['action'] === 'approve' 
                    ? 'Firma schválila dokument študenta' 
                    : 'Firma zamietla dokument študenta';
                $studentName = $internship->student ? $internship->student->name . ' ' . $internship->student->surname : 'Študent';
                $notificationMessage = $validated['action'] === 'approve'
                    ? 'Firma ' . $company->name . ' schválila dokument študenta ' . $studentName . ': ' . $document->name
                    : 'Firma ' . $company->name . ' zamietla dokument študenta ' . $studentName . ': ' . $document->name;

                NotificationService::create(
                    $internship->garant->user->id,
                    Notification::TYPE_DOCUMENT_STATUS_CHANGED ?? 'document_status_changed',
                    $notificationTitle,
                    $notificationMessage,
                    ['document_id' => $document->id, 'internship_id' => $internship->id]
                );
            }

            return response()->json([
                'message' => $validated['action'] === 'approve' 
                    ? 'Document approved successfully.' 
                    : 'Document rejected successfully.',
                'data' => [
                    'id' => $document->id,
                    'company_status' => $document->company_status,
                    'company_rejection_reason' => $document->company_rejection_reason,
                    'company_validated_at' => $document->company_validated_at?->toIso8601String(),
                ]
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Document not found or access denied.'
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error validating document: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred while validating the document.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}
