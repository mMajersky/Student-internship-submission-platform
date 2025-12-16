<?php

namespace App\Http\Controllers;

use App\Models\Internship;
use App\Models\Student;
use App\Models\Company;
use App\Models\Garant;
use App\Models\Notification;
use App\Models\User;
use App\Mail\InternshipCreatedNotification;
use App\Mail\InternshipCreatedForGarant;
use App\Mail\InternshipStatusChanged;
use App\Mail\EvaluationRequest;
use App\Services\EmailService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class InternshipController extends Controller
{
    /**
     * Store a newly created internship in storage (admin/garant only).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'student_id' => [
                'required',
                'integer',
                Rule::exists('students', 'id')
            ],
            'company_id' => [
                'required',
                'integer',
                Rule::exists('companies', 'id')
            ],
            'garant_id' => [
                'nullable',
                'integer',
                Rule::exists('garants', 'id')
            ],
            'status' => [
                'required',
                'string',
                'max:50',
                Rule::in(Internship::getStatuses())
            ],
            'academy_year' => [
                'required',
                'string',
                'max:9',
                'regex:/^\d{4}\/\d{4}$/' // Format: 2024/2025
            ],
            'start_date' => [
                'nullable',
                'date',
                'date_format:Y-m-d'
            ],
            'end_date' => [
                'nullable',
                'date',
                'date_format:Y-m-d',
                'after_or_equal:start_date'
            ],
            'confirmed_date' => [
                'nullable',
                'date',
                'date_format:Y-m-d'
            ],
            'approved_date' => [
                'nullable',
                'date',
                'date_format:Y-m-d'
            ],
        ], [
            // Custom error messages
            'student_id.required' => 'The student field is required.',
            'student_id.exists' => 'The selected student does not exist.',
            'company_id.required' => 'The company field is required.',
            'company_id.exists' => 'The selected company does not exist.',
            'garant_id.exists' => 'The selected garant does not exist.',
            'status.required' => 'The status field is required.',
            'status.in' => 'The status must be one of: ' . implode(', ', Internship::getStatuses()) . '.',
            'academy_year.required' => 'The academy year field is required.',
            'academy_year.regex' => 'The academy year must be in format YYYY/YYYY (e.g., 2024/2025).',
            'end_date.after_or_equal' => 'The end date must be equal to or after the start date.',
        ]);

        try {
            // Create the internship
            $internship = Internship::create($validated);

            // Load relationships for the response
            $internship->load(['student.user', 'company.user', 'garant.user']);

            // Send email to company with approve/reject buttons ONLY when created with "potvrdená" status (garant confirms)
            // Sequence: Created → Confirmed (garant) → Approved (company) → Defended
            // This is the moment when company needs to confirm/reject
            if ($validated['status'] === Internship::STATUS_POTVRDENA && $internship->company && $internship->company->user && $internship->company->user->email) {
                // Generate secure tokens for email actions
                $confirmToken = $this->generateSecureToken($internship->id, 'confirm');
                $rejectToken = $this->generateSecureToken($internship->id, 'reject');

                $emailData = [
                    'studentName' => $internship->student->name . ' ' . $internship->student->surname,
                    'studentEmail' => $internship->student->student_email,
                    'studentPhone' => $internship->student->phone_number ?? 'N/A',
                    'companyName' => $internship->company->name,
                    'academyYear' => $internship->academy_year,
                    'startDate' => $internship->start_date?->format('Y-m-d'),
                    'endDate' => $internship->end_date?->format('Y-m-d'),
                    'status' => $internship->status,
                    'confirmUrl' => config('app.url') . '/internships/company-action?token=' . $confirmToken,
                    'rejectUrl' => config('app.url') . '/internships/company-action?token=' . $rejectToken,
                    'garantEmail' => ($internship->garant && $internship->garant->user) ? $internship->garant->user->email : 'garant@school.sk',
                    'showButtons' => true, // Show buttons for company emails
                ];

                // Always create notification
                NotificationService::create(
                    $internship->company->user->id,
                    Notification::TYPE_APPROVAL_REQUEST,
                    'Žiadosť o potvrdenie stáže',
                    'Garant schválil prax študenta ' . $internship->student->name . ' ' . $internship->student->surname . '. Prosíme o potvrdenie stáže.',
                    ['internship_id' => $internship->id]
                );

                // Always send email to company when garant approves (regardless of email_notifications setting)
                // Company needs to approve/reject via email buttons
                EmailService::send(InternshipCreatedNotification::class, $internship->company->user->email, $emailData);
            }



            // Return success response with created internship
            return response()->json([
                'message' => 'Internship created successfully.',
                'data' => [
                    'id' => $internship->id,
                    'student_id' => $internship->student_id,
                    'student' => $internship->student ? [
                        'id' => $internship->student->id,
                        'name' => $internship->student->name,
                        'surname' => $internship->student->surname,
                        'student_email' => $internship->student->student_email,
                    ] : null,
                    'company_id' => $internship->company_id,
                    'company' => $internship->company ? [
                        'id' => $internship->company->id,
                        'name' => $internship->company->name,
                    ] : null,
                    'garant_id' => $internship->garant_id,
                    'garant' => $internship->garant ? [
                        'id' => $internship->garant->id,
                        'name' => $internship->garant->name ?? null,
                        'surname' => $internship->garant->surname ?? null,
                    ] : null,
                    'status' => $internship->status,
                    'academy_year' => $internship->academy_year,
                    'start_date' => $internship->start_date?->format('Y-m-d'),
                    'end_date' => $internship->end_date?->format('Y-m-d'),
                    'confirmed_date' => $internship->confirmed_date?->format('Y-m-d'),
                    'approved_date' => $internship->approved_date?->format('Y-m-d'),
                    'created_at' => $internship->created_at?->toIso8601String(),
                    'updated_at' => $internship->updated_at?->toIso8601String(),
                ]
            ], 201);

        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Error creating internship: ' . $e->getMessage());

            // Return error response
            return response()->json([
                'message' => 'An error occurred while creating the internship.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Display a listing of internships (admin/garant only - shows all internships).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            // Get all internships for admin/garant with document count
            $internships = Internship::with(['student', 'company', 'garant'])
                ->withCount('documents')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'data' => $internships->map(function ($internship) {
                    // Count digital reports (internship_report) as documents
                    $digitalReportCount = 0;
                    $report = $internship->internship_report ?: [];
                    if (isset($report['submitted_at'])) {
                        $digitalReportCount = 1; // Digital report exists
                    }
                    
                    // Total documents count = uploaded documents + digital reports
                    $totalDocumentsCount = $internship->documents_count + $digitalReportCount;
                    
                    return [
                        'id' => $internship->id,
                        'student_id' => $internship->student_id,
                        'student' => $internship->student ? [
                            'id' => $internship->student->id,
                            'name' => $internship->student->name,
                            'surname' => $internship->student->surname,
                            'student_email' => $internship->student->student_email,
                        ] : null,
                        'company_id' => $internship->company_id,
                        'company' => $internship->company ? [
                            'id' => $internship->company->id,
                            'name' => $internship->company->name,
                        ] : null,
                        'garant_id' => $internship->garant_id,
                        'garant' => $internship->garant ? [
                            'id' => $internship->garant->id,
                            'name' => $internship->garant->name ?? null,
                            'surname' => $internship->garant->surname ?? null,
                        ] : null,
                        'status' => $internship->status,
                        'academy_year' => $internship->academy_year,
                        'start_date' => $internship->start_date?->format('Y-m-d'),
                        'end_date' => $internship->end_date?->format('Y-m-d'),
                        'confirmed_date' => $internship->confirmed_date?->format('Y-m-d'),
                        'approved_date' => $internship->approved_date?->format('Y-m-d'),
                        'documents_count' => $totalDocumentsCount,
                        'created_at' => $internship->created_at?->toIso8601String(),
                        'updated_at' => $internship->updated_at?->toIso8601String(),
                    ];
                })
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Error fetching internships: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred while fetching internships.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Display the specified internship (admin/garant only).
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $internship = Internship::with(['student', 'company', 'garant', 'documents'])
                ->findOrFail($id);

            return response()->json([
                'data' => [
                    'id' => $internship->id,
                    'student_id' => $internship->student_id,
                    'student' => $internship->student ? [
                        'id' => $internship->student->id,
                        'name' => $internship->student->name,
                        'surname' => $internship->student->surname,
                        'student_email' => $internship->student->student_email,
                    ] : null,
                    'company_id' => $internship->company_id,
                    'company' => $internship->company ? [
                        'id' => $internship->company->id,
                        'name' => $internship->company->name,
                    ] : null,
                    'garant_id' => $internship->garant_id,
                    'garant' => $internship->garant ? [
                        'id' => $internship->garant->id,
                        'name' => $internship->garant->name ?? null,
                        'surname' => $internship->garant->surname ?? null,
                    ] : null,
                    'status' => $internship->status,
                    'academy_year' => $internship->academy_year,
                    'start_date' => $internship->start_date?->format('Y-m-d'),
                    'end_date' => $internship->end_date?->format('Y-m-d'),
                    'confirmed_date' => $internship->confirmed_date?->format('Y-m-d'),
                    'approved_date' => $internship->approved_date?->format('Y-m-d'),
                    'evaluation' => $internship->evaluation,
                    'internship_report' => $internship->internship_report,
                    'created_at' => $internship->created_at?->toIso8601String(),
                    'updated_at' => $internship->updated_at?->toIso8601String(),
                ]
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Internship not found.'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Error fetching internship: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred while fetching the internship.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Update the specified internship in storage (admin/garant only).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $internship = Internship::findOrFail($id);

            // Validate the incoming request data
            $validated = $request->validate([
                'student_id' => [
                    'sometimes',
                    'required',
                    'integer',
                    Rule::exists('students', 'id')
                ],
                'company_id' => [
                    'sometimes',
                    'required',
                    'integer',
                    Rule::exists('companies', 'id')
                ],
                'garant_id' => [
                    'nullable',
                    'integer',
                    Rule::exists('garants', 'id')
                ],
                'status' => [
                    'sometimes',
                    'required',
                    'string',
                    'max:50',
                    Rule::in(Internship::getStatuses())
                ],
                'academy_year' => [
                    'sometimes',
                    'required',
                    'string',
                    'max:9',
                    'regex:/^\d{4}\/\d{4}$/' // Format: 2024/2025
                ],
                'start_date' => [
                    'nullable',
                    'date',
                    'date_format:Y-m-d'
                ],
                'end_date' => [
                    'nullable',
                    'date',
                    'date_format:Y-m-d',
                    'after_or_equal:start_date'
                ],
                'confirmed_date' => [
                    'nullable',
                    'date',
                    'date_format:Y-m-d'
                ],
                'approved_date' => [
                    'nullable',
                    'date',
                    'date_format:Y-m-d'
                ],
            ], [
                // Custom error messages
                'student_id.required' => 'The student field is required.',
                'student_id.exists' => 'The selected student does not exist.',
                'company_id.required' => 'The company field is required.',
                'company_id.exists' => 'The selected company does not exist.',
                'garant_id.exists' => 'The selected garant does not exist.',
                'status.required' => 'The status field is required.',
                'status.in' => 'The status must be one of: ' . implode(', ', Internship::getStatuses()) . '.',
                'academy_year.required' => 'The academy year field is required.',
                'academy_year.regex' => 'The academy year must be in format YYYY/YYYY (e.g., 2024/2025).',
                'end_date.after_or_equal' => 'The end date must be equal to or after the start date.',
            ]);

            // Check if status is being changed
            $oldStatus = $internship->status;
            $statusChanged = isset($validated['status']) && $oldStatus !== $validated['status'];
            $statusChangedToPotvrdena = $statusChanged && $validated['status'] === Internship::STATUS_POTVRDENA;
            
            // Check if internship has ended (end_date is today or in the past) and evaluation email hasn't been sent yet
            $oldEndDate = $internship->end_date;
            $newEndDate = isset($validated['end_date']) ? Carbon::parse($validated['end_date']) : $oldEndDate;
            $today = Carbon::today();
            $internshipEnded = $newEndDate && $newEndDate->lte($today);
            $endDateChanged = $oldEndDate != $newEndDate;

            // Update the internship
            $internship->update($validated);

            // Load relationships for the response
            $internship->load(['student.user', 'company.user', 'garant.user']);

            // Send email to company with approve/reject buttons when garant confirms (changes to "potvrdená")
            // Sequence: Created → Confirmed (garant) → Approved (company) → Defended
            // This is the moment when company needs to confirm/reject (after garant confirms)
            if ($statusChangedToPotvrdena && $internship->company && $internship->company->user && $internship->company->user->email) {
                // Generate secure tokens for email actions
                $confirmToken = $this->generateSecureToken($internship->id, 'confirm');
                $rejectToken = $this->generateSecureToken($internship->id, 'reject');

                $emailData = [
                    'studentName' => $internship->student->name . ' ' . $internship->student->surname,
                    'studentEmail' => $internship->student->student_email,
                    'studentPhone' => $internship->student->phone_number ?? 'N/A',
                    'companyName' => $internship->company->name,
                    'academyYear' => $internship->academy_year,
                    'startDate' => $internship->start_date?->format('Y-m-d'),
                    'endDate' => $internship->end_date?->format('Y-m-d'),
                    'status' => $internship->status,
                    'confirmUrl' => config('app.url') . '/internships/company-action?token=' . $confirmToken,
                    'rejectUrl' => config('app.url') . '/internships/company-action?token=' . $rejectToken,
                    'garantEmail' => ($internship->garant && $internship->garant->user) ? $internship->garant->user->email : 'garant@school.sk',
                    'showButtons' => true, // Show buttons for company emails
                ];

                // Always create notification
                NotificationService::create(
                    $internship->company->user->id,
                    Notification::TYPE_APPROVAL_REQUEST,
                    'Žiadosť o potvrdenie stáže',
                    'Garant schválil prax študenta ' . $internship->student->name . ' ' . $internship->student->surname . '. Prosíme o potvrdenie stáže.',
                    ['internship_id' => $internship->id]
                );

                // Always send email to company when garant approves (regardless of email_notifications setting)
                // Company needs to approve/reject via email buttons
                EmailService::send(InternshipCreatedNotification::class, $internship->company->user->email, $emailData);
            }

            // Send evaluation email to company when internship ends (end_date is today or in the past)
            // Only send if evaluation doesn't exist yet and end_date changed to a past/today date
            if ($internshipEnded && $endDateChanged 
                && $internship->company && $internship->company->user && $internship->company->user->email) {
                
                // Reload internship to get updated data
                $internship->refresh();
                
                // Only send if evaluation email hasn't been sent yet and evaluation hasn't been submitted
                $evaluation = $internship->evaluation ?: [];
                if (!isset($evaluation['email_sent_at']) && !isset($evaluation['submitted_at'])) {
                    // Generate secure token for evaluation form
                    $evaluationToken = $this->generateEvaluationToken($internship->id);

                    $evaluationEmailData = [
                        'studentName' => $internship->student->name . ' ' . $internship->student->surname,
                        'companyName' => $internship->company->name,
                        'academyYear' => $internship->academy_year,
                        'startDate' => $internship->start_date?->format('Y-m-d'),
                        'endDate' => $internship->end_date?->format('Y-m-d'),
                        'status' => $internship->status,
                        'evaluationUrl' => config('app.url') . '/internships/evaluation?token=' . $evaluationToken,
                        'garantEmail' => ($internship->garant && $internship->garant->user) ? $internship->garant->user->email : 'garant@school.sk',
                    ];

                    // Always send evaluation email (critical email)
                    $emailSent = EmailService::send(EvaluationRequest::class, $internship->company->user->email, $evaluationEmailData);
                    
                    if ($emailSent) {
                        // Mark email as sent in evaluation JSON
                        $evaluation['email_sent_at'] = Carbon::now()->toIso8601String();
                        $internship->evaluation = $evaluation;
                        $internship->save();
                    }
                }
            }

            // If garant changed status: send emails to student and company (for any status change)
            if ($statusChanged && $internship->student && $internship->student->user) {
                // Prepare email data
                $emailData = [
                    'internshipId' => $internship->id,
                    'studentName' => $internship->student->name . ' ' . $internship->student->surname,
                    'companyName' => $internship->company ? $internship->company->name : 'N/A',
                    'academyYear' => $internship->academy_year,
                    'oldStatus' => $oldStatus,
                    'newStatus' => $internship->status,
                ];

                // Send email to student (if email notifications enabled)
                if ($internship->student->user->email_notifications) {
                    NotificationService::createAndNotify(
                        $internship->student->user->id,
                        Notification::TYPE_INTERNSHIP_STATUS_CHANGED,
                        'Stav praxe bol zmenený',
                        'Stav vašej praxe bol zmenený garantom. Stav: ' . $oldStatus . ' → ' . $internship->status,
                        ['internship_id' => $internship->id, 'old_status' => $oldStatus, 'new_status' => $internship->status],
                        InternshipStatusChanged::class,
                        $emailData
                    );
                } else {
                    // Create system notification only (no email)
                    NotificationService::create(
                        $internship->student->user->id,
                        Notification::TYPE_INTERNSHIP_STATUS_CHANGED,
                        'Stav praxe bol zmenený',
                        'Stav vašej praxe bol zmenený garantom. Stav: ' . $oldStatus . ' → ' . $internship->status,
                        ['internship_id' => $internship->id, 'old_status' => $oldStatus, 'new_status' => $internship->status]
                    );
                }

                // Send email to company (if exists) - always send on status change, not just when email_notifications enabled
                // But skip if we already sent the approval request email above (when changing to "potvrdená")
                if ($internship->company && $internship->company->user && $internship->company->user->email && !$statusChangedToPotvrdena) {
                    if ($internship->company->user->email_notifications) {
                        NotificationService::createAndNotify(
                            $internship->company->user->id,
                            Notification::TYPE_INTERNSHIP_STATUS_CHANGED,
                            'Stav praxe bol zmenený',
                            'Garant zmenil stav praxe študenta ' . $internship->student->name . ' ' . $internship->student->surname . '. Stav: ' . $oldStatus . ' → ' . $internship->status,
                            ['internship_id' => $internship->id, 'old_status' => $oldStatus, 'new_status' => $internship->status],
                            InternshipStatusChanged::class,
                            $emailData
                        );
                    } else {
                        // Create notification only (no email)
                        NotificationService::create(
                            $internship->company->user->id,
                            Notification::TYPE_INTERNSHIP_STATUS_CHANGED,
                            'Stav praxe bol zmenený',
                            'Garant zmenil stav praxe študenta ' . $internship->student->name . ' ' . $internship->student->surname . '. Stav: ' . $oldStatus . ' → ' . $internship->status,
                            ['internship_id' => $internship->id, 'old_status' => $oldStatus, 'new_status' => $internship->status]
                        );
                    }
                }
            }

            // Return success response with updated internship
            return response()->json([
                'message' => 'Internship updated successfully.',
                'data' => [
                    'id' => $internship->id,
                    'student_id' => $internship->student_id,
                    'student' => $internship->student ? [
                        'id' => $internship->student->id,
                        'name' => $internship->student->name,
                        'surname' => $internship->student->surname,
                        'student_email' => $internship->student->student_email,
                    ] : null,
                    'company_id' => $internship->company_id,
                    'company' => $internship->company ? [
                        'id' => $internship->company->id,
                        'name' => $internship->company->name,
                    ] : null,
                    'garant_id' => $internship->garant_id,
                    'garant' => $internship->garant ? [
                        'id' => $internship->garant->id,
                        'name' => $internship->garant->name ?? null,
                        'surname' => $internship->garant->surname ?? null,
                    ] : null,
                    'status' => $internship->status,
                    'academy_year' => $internship->academy_year,
                    'start_date' => $internship->start_date?->format('Y-m-d'),
                    'end_date' => $internship->end_date?->format('Y-m-d'),
                    'confirmed_date' => $internship->confirmed_date?->format('Y-m-d'),
                    'approved_date' => $internship->approved_date?->format('Y-m-d'),
                    'created_at' => $internship->created_at?->toIso8601String(),
                    'updated_at' => $internship->updated_at?->toIso8601String(),
                ]
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Internship not found.'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Error updating internship: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred while updating the internship.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Remove the specified internship from storage (admin/garant only).
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $internship = Internship::findOrFail($id);
            
            // Store internship data before deletion for response
            $internshipData = [
                'id' => $internship->id,
                'student_id' => $internship->student_id,
                'company_id' => $internship->company_id,
                'status' => $internship->status,
                'academy_year' => $internship->academy_year,
            ];

            // Delete the internship
            $internship->delete();

            return response()->json([
                'message' => 'Internship deleted successfully.',
                'data' => $internshipData
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Internship not found.'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Error deleting internship: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred while deleting the internship.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get internships for the authenticated student (student-specific method).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function studentIndex()
    {
        try {
            $user = Auth::user();
            
            // Check if user has a student profile
            if (!$user->student) {
                return response()->json([
                    'message' => 'Student profile not found for this user.'
                ], 403);
            }
            
            // Get only the student's own internships
            $internships = Internship::with(['student', 'company', 'garant'])
                ->where('student_id', $user->student->id)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'data' => $internships->map(function ($internship) {
                    return [
                        'id' => $internship->id,
                        'student_id' => $internship->student_id,
                        'student' => $internship->student ? [
                            'id' => $internship->student->id,
                            'name' => $internship->student->name,
                            'surname' => $internship->student->surname,
                            'student_email' => $internship->student->student_email,
                        ] : null,
                        'company_id' => $internship->company_id,
                        'company' => $internship->company ? [
                            'id' => $internship->company->id,
                            'name' => $internship->company->name,
                        ] : null,
                        'garant_id' => $internship->garant_id,
                        'garant' => $internship->garant ? [
                            'id' => $internship->garant->id,
                            'name' => $internship->garant->name ?? null,
                            'surname' => $internship->garant->surname ?? null,
                        ] : null,
                        'status' => $internship->status,
                        'academy_year' => $internship->academy_year,
                        'start_date' => $internship->start_date?->format('Y-m-d'),
                        'end_date' => $internship->end_date?->format('Y-m-d'),
                        'confirmed_date' => $internship->confirmed_date?->format('Y-m-d'),
                        'approved_date' => $internship->approved_date?->format('Y-m-d'),
                        'created_at' => $internship->created_at?->toIso8601String(),
                        'updated_at' => $internship->updated_at?->toIso8601String(),
                    ];
                })
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Error fetching student internships: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred while fetching internships.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Confirm internship by company (public route for email links).
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function companyConfirm($id)
    {
        try {
            $internship = Internship::findOrFail($id);

            // Update status to confirmed
            $internship->update(['status' => Internship::STATUS_POTVRDENA]);

            return response()->json([
                'message' => 'Internship confirmed successfully.',
                'data' => [
                    'id' => $internship->id,
                    'status' => $internship->status,
                ]
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Internship not found.'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Error confirming internship: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred while confirming the internship.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Reject internship by company (public route for email links).
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function companyReject($id)
    {
        try {
            $internship = Internship::findOrFail($id);

            // Update status to rejected
            $internship->update(['status' => Internship::STATUS_ZAMIETNUTA]);

            return response()->json([
                'message' => 'Internship rejected successfully.',
                'data' => [
                    'id' => $internship->id,
                    'status' => $internship->status,
                ]
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Internship not found.'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Error rejecting internship: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred while rejecting the internship.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Store a newly created internship for the authenticated student (student-specific method).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function studentStore(Request $request)
    {
        $user = Auth::user();
        
        // Check if user has a student profile
        if (!$user->student) {
            return response()->json([
                'message' => 'Student profile not found for this user.'
            ], 403);
        }
        
        // Validate the incoming request data
        // Note: student_id is not required from request, we'll use authenticated user's student_id
        $validated = $request->validate([
            'company_id' => [
                'required',
                'integer',
                Rule::exists('companies', 'id')
            ],
            'garant_id' => [
                'nullable',
                'integer',
                Rule::exists('garants', 'id')
            ],
            // Status is not required from student - will be automatically set to "potvrdená"
            'status' => [
                'nullable',
                'string',
                'max:50',
                Rule::in(Internship::getStatuses())
            ],
            'academy_year' => [
                'required',
                'string',
                'max:9',
                'regex:/^\d{4}\/\d{4}$/' // Format: 2024/2025
            ],
            'start_date' => [
                'nullable',
                'date',
                'date_format:Y-m-d'
            ],
            'end_date' => [
                'nullable',
                'date',
                'date_format:Y-m-d',
                'after_or_equal:start_date'
            ],
            'confirmed_date' => [
                'nullable',
                'date',
                'date_format:Y-m-d'
            ],
            'approved_date' => [
                'nullable',
                'date',
                'date_format:Y-m-d'
            ],
        ], [
            // Custom error messages
            'company_id.required' => 'The company field is required.',
            'company_id.exists' => 'The selected company does not exist.',
            'garant_id.exists' => 'The selected garant does not exist.',
            'status.in' => 'The status must be one of: ' . implode(', ', Internship::getStatuses()) . '.',
            'academy_year.required' => 'The academy year field is required.',
            'academy_year.regex' => 'The academy year must be in format YYYY/YYYY (e.g., 2024/2025).',
            'end_date.after_or_equal' => 'The end date must be equal to or after the start date.',
        ]);

        try {
            // Add the authenticated student's ID to the validated data
            $validated['student_id'] = $user->student->id;
            
            // Automatically set status to "vytvorená" when student creates internship
            $validated['status'] = Internship::STATUS_VYTVORENA;

            // Create the internship
            $internship = Internship::create($validated);

            // Load relationships for the response
            $internship->load(['student.user', 'company.user', 'garant.user']);

            // NO EMAIL TO COMPANY when student creates internship
            // Email with approve/reject buttons will be sent only when garant approves it

            // Check if internship has already ended (end_date is today or in the past)
            // If yes, automatically send evaluation email to company (if no PDF report scan exists)
            if ($internship->end_date && Carbon::parse($internship->end_date)->lte(Carbon::today())
                && $internship->company && $internship->company->user && $internship->company->user->email) {
                
                // Check if student has uploaded a PDF report scan
                $reportScanDoc = \App\Models\Document::where('internship_id', $internship->id)
                    ->where('type', 'vykaz_praxe_scan')
                    ->first();
                
                // Only send if no PDF report scan exists and email hasn't been sent yet
                $report = $internship->internship_report ?: [];
                if (!$reportScanDoc && !isset($report['email_sent_at']) && !isset($report['submitted_at'])) {
                    // Generate secure token for evaluation form
                    $evaluationToken = $this->generateEvaluationToken($internship->id);

                    $evaluationEmailData = [
                        'studentName' => $internship->student->name . ' ' . $internship->student->surname,
                        'companyName' => $internship->company->name,
                        'academyYear' => $internship->academy_year,
                        'startDate' => $internship->start_date?->format('Y-m-d'),
                        'endDate' => $internship->end_date?->format('Y-m-d'),
                        'status' => $internship->status,
                        'evaluationUrl' => config('app.url') . '/internships/evaluation?token=' . $evaluationToken,
                        'garantEmail' => ($internship->garant && $internship->garant->user) ? $internship->garant->user->email : 'garant@school.sk',
                    ];

                    // Send evaluation email to company
                    $emailSent = EmailService::send(EvaluationRequest::class, $internship->company->user->email, $evaluationEmailData);
                    
                    if ($emailSent) {
                        // Mark email as sent in internship_report JSON
                        $report['email_sent_at'] = Carbon::now()->toIso8601String();
                        $internship->internship_report = $report;
                        
                        // Also mark in evaluation for backward compatibility
                        $evaluation = $internship->evaluation ?: [];
                        $evaluation['email_sent_at'] = Carbon::now()->toIso8601String();
                        $internship->evaluation = $evaluation;
                        
                        $internship->save();
                    }
                }
            }

            // Prepare email data for garant notification (using new internship_created_for_garant.blade.php template)
            $emailData = [
                'internshipId' => $internship->id,
                'studentName' => $internship->student->name . ' ' . $internship->student->surname,
                'companyName' => $internship->company ? $internship->company->name : 'N/A',
                'academyYear' => $internship->academy_year,
                'startDate' => $internship->start_date?->format('Y-m-d'),
                'endDate' => $internship->end_date?->format('Y-m-d'),
                'status' => $internship->status,
            ];

            // Notify garant(s) about new internship created by student
            if ($internship->garant && $internship->garant->user) {
                // If garant is already assigned, notify only them
                NotificationService::createAndNotify(
                    $internship->garant->user->id,
                    Notification::TYPE_INTERNSHIP_CREATED,
                    'Študent vytvoril novú prax',
                    'Študent ' . $internship->student->name . ' ' . $internship->student->surname . ' vytvoril novú prax vo firme ' . $internship->company->name . '.',
                    ['internship_id' => $internship->id],
                    InternshipCreatedForGarant::class,
                    $emailData
                );
            } else {
                // If no garant assigned, notify ALL garants about new internship
                $allGarants = Garant::with('user')->whereNotNull('user_id')->get();
                foreach ($allGarants as $garant) {
                    if ($garant->user) {
                        NotificationService::createAndNotify(
                            $garant->user->id,
                            Notification::TYPE_INTERNSHIP_CREATED,
                            'Nová prax čaká na priradenie',
                            'Študent ' . $internship->student->name . ' ' . $internship->student->surname . ' vytvoril novú prax. Prax ešte nemá priradeného garanta.',
                            ['internship_id' => $internship->id],
                            InternshipCreatedForGarant::class,
                            $emailData
                        );
                    }
                }
            }

            // Return success response with created internship
            return response()->json([
                'message' => 'Internship created successfully.',
                'data' => [
                    'id' => $internship->id,
                    'student_id' => $internship->student_id,
                    'student' => $internship->student ? [
                        'id' => $internship->student->id,
                        'name' => $internship->student->name,
                        'surname' => $internship->student->surname,
                        'student_email' => $internship->student->student_email,
                    ] : null,
                    'company_id' => $internship->company_id,
                    'company' => $internship->company ? [
                        'id' => $internship->company->id,
                        'name' => $internship->company->name,
                    ] : null,
                    'garant_id' => $internship->garant_id,
                    'garant' => $internship->garant ? [
                        'id' => $internship->garant->id,
                        'name' => $internship->garant->name ?? null,
                        'surname' => $internship->garant->surname ?? null,
                    ] : null,
                    'status' => $internship->status,
                    'academy_year' => $internship->academy_year,
                    'start_date' => $internship->start_date?->format('Y-m-d'),
                    'end_date' => $internship->end_date?->format('Y-m-d'),
                    'confirmed_date' => $internship->confirmed_date?->format('Y-m-d'),
                    'approved_date' => $internship->approved_date?->format('Y-m-d'),
                    'created_at' => $internship->created_at?->toIso8601String(),
                    'updated_at' => $internship->updated_at?->toIso8601String(),
                ]
            ], 201);

        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Error creating student internship: ' . $e->getMessage());

            // Return error response
            return response()->json([
                'message' => 'An error occurred while creating the internship.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Handle company actions (confirm/reject) via secure token (public route for email links).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function companyAction(Request $request)
    {
        try {
            $token = $request->query('token');

            if (!$token) {
                return response()->json([
                    'message' => 'Token is required.'
                ], 400);
            }

            // Decrypt and validate the token
            $tokenData = $this->validateSecureToken($token);

            if (!$tokenData) {
                return response()->json([
                    'message' => 'Invalid or expired token.'
                ], 400);
            }

            $internship = Internship::with(['student.user', 'company.user', 'garant.user'])->findOrFail($tokenData['internship_id']);

            // Check if internship is still in "potvrdená" status (pending company action)
            // Company can only confirm/reject after garant has confirmed (status "potvrdená")
            // Sequence: Created → Confirmed (garant) → Approved (company) → Defended
            if ($internship->status !== Internship::STATUS_POTVRDENA) {
                return response()->json([
                    'message' => 'This internship has already been processed and is no longer available for action.',
                    'current_status' => $internship->status,
                    'error' => 'already_resolved'
                ], 400);
            }

            // Store old status before update
            $oldStatus = $internship->status;

            // Update status based on action
            // After garant confirmed (status "potvrdená"), company confirms → "schválená" (Approved)
            // Company rejects → "zamietnutá"
            $newStatus = $tokenData['action'] === 'confirm'
                ? Internship::STATUS_SCHVALENA // Company confirmed - status "schválená" (Approved)
                : Internship::STATUS_ZAMIETNUTA; // Company rejected

            $internship->update(['status' => $newStatus]);

            // Prepare email data
            $emailData = [
                'internshipId' => $internship->id,
                'studentName' => $internship->student->name . ' ' . $internship->student->surname,
                'companyName' => $internship->company->name,
                'academyYear' => $internship->academy_year,
                'oldStatus' => $oldStatus,
                'newStatus' => $newStatus,
            ];

            // If company approved (confirmed): send email to student and garant
            // Respects email_notifications setting
            if ($tokenData['action'] === 'confirm') {
                // Send email to student (respects email_notifications setting)
                if ($internship->student && $internship->student->user) {
                    NotificationService::createAndNotify(
                        $internship->student->user->id,
                        Notification::TYPE_INTERNSHIP_STATUS_CHANGED,
                        'Firma schválila vašu prax',
                        'Firma ' . $internship->company->name . ' schválila vašu prax. Stav: ' . $oldStatus . ' → ' . $newStatus,
                        ['internship_id' => $internship->id, 'old_status' => $oldStatus, 'new_status' => $newStatus],
                        InternshipStatusChanged::class,
                        $emailData
                    );
                }

                // Send email to ALL garants (users with role 'garant') - respects email_notifications setting
                $allGarants = User::where('role', 'garant')->whereNotNull('email')->get();
                
                foreach ($allGarants as $garantUser) {
                    NotificationService::createAndNotify(
                        $garantUser->id,
                        Notification::TYPE_INTERNSHIP_STATUS_CHANGED,
                        'Firma schválila prax',
                        'Firma ' . $internship->company->name . ' schválila prax študenta ' . $internship->student->name . ' ' . $internship->student->surname . '. Stav: ' . $oldStatus . ' → ' . $newStatus,
                        ['internship_id' => $internship->id, 'old_status' => $oldStatus, 'new_status' => $newStatus],
                        InternshipStatusChanged::class,
                        $emailData
                    );
                }
            }

            // If company rejected: send email only to student - respects email_notifications setting
            if ($tokenData['action'] === 'reject') {
                if ($internship->student && $internship->student->user) {
                    NotificationService::createAndNotify(
                        $internship->student->user->id,
                        Notification::TYPE_INTERNSHIP_STATUS_CHANGED,
                        'Firma zamietla vašu prax',
                        'Firma ' . $internship->company->name . ' zamietla vašu prax. Stav: ' . $oldStatus . ' → ' . $newStatus,
                        ['internship_id' => $internship->id, 'old_status' => $oldStatus, 'new_status' => $newStatus],
                        InternshipStatusChanged::class,
                        $emailData
                    );
                }
            }

            return response()->json([
                'message' => 'Internship ' . ($tokenData['action'] === 'confirm' ? 'confirmed' : 'rejected') . ' successfully.',
                'data' => [
                    'id' => $internship->id,
                    'status' => $internship->status,
                    'action' => $tokenData['action']
                ]
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Internship not found.'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Error processing company action: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred while processing the action.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Generate a secure token for company actions.
     *
     * @param  int  $internshipId
     * @param  string  $action
     * @return string
     */
    private function generateSecureToken($internshipId, $action)
    {
        $data = [
            'internship_id' => $internshipId,
            'action' => $action,
            'timestamp' => Carbon::now()->timestamp,
            'expires_at' => Carbon::now()->addDays(30)->timestamp // Token expires in 30 days for companies to have ample time
        ];

        return Crypt::encrypt($data);
    }

    /**
     * Resend internship approval email to company (garant only).
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function resendApprovalEmail($id)
    {
        try {
            $internship = Internship::with(['company.user', 'student', 'garant'])->findOrFail($id);

            // Check if internship is in "potvrdená" status (awaiting company approval)
            if ($internship->status !== Internship::STATUS_POTVRDENA) {
                return response()->json([
                    'message' => 'Email can only be resent for internships awaiting company approval.',
                    'current_status' => $internship->status
                ], 400);
            }

            // Check if company exists and has a user with email
            if (!$internship->company || !$internship->company->user || !$internship->company->user->email) {
                return response()->json([
                    'message' => 'Company email not found.'
                ], 400);
            }

            // Generate secure tokens for email actions
            $confirmToken = $this->generateSecureToken($internship->id, 'confirm');
            $rejectToken = $this->generateSecureToken($internship->id, 'reject');

            $data = [
                'studentName' => $internship->student->name . ' ' . $internship->student->surname,
                'studentEmail' => $internship->student->student_email,
                'studentPhone' => $internship->student->phone_number ?? 'N/A',
                'companyName' => $internship->company->name,
                'academyYear' => $internship->academy_year,
                'startDate' => $internship->start_date?->format('Y-m-d'),
                'endDate' => $internship->end_date?->format('Y-m-d'),
                'status' => $internship->status,
                'confirmUrl' => config('app.url') . '/internships/company-action?token=' . $confirmToken,
                'rejectUrl' => config('app.url') . '/internships/company-action?token=' . $rejectToken,
                'garantEmail' => ($internship->garant && $internship->garant->user) ? $internship->garant->user->email : 'garant@school.sk',
                'showButtons' => true, // Show buttons for company emails
            ];

            $emailSent = EmailService::send(InternshipCreatedNotification::class, $internship->company->user->email, $data);

            if ($emailSent) {
                return response()->json([
                    'message' => 'Approval email resent successfully to ' . $internship->company->user->email,
                    'email' => $internship->company->user->email
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Failed to send email. Please try again or contact system administrator.'
                ], 500);
            }

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Internship not found.'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Error resending approval email: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred while resending the email.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Send evaluation request email to company (garant only).
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendEvaluationEmail($id)
    {
        try {
            $internship = Internship::with(['company.user', 'student', 'garant.user'])->findOrFail($id);

            // Check if company exists and has a user with email
            if (!$internship->company || !$internship->company->user || !$internship->company->user->email) {
                return response()->json([
                    'message' => 'Company email not found.'
                ], 400);
            }

            // Check if evaluation already exists (company has already submitted evaluation)
            $evaluation = $internship->evaluation ?: [];
            if (isset($evaluation['submitted_at'])) {
                return response()->json([
                    'message' => 'Company has already submitted the evaluation. Email cannot be sent.'
                ], 400);
            }

            // Generate secure token for evaluation form
            $evaluationToken = $this->generateEvaluationToken($internship->id);

            $data = [
                'studentName' => $internship->student->name . ' ' . $internship->student->surname,
                'companyName' => $internship->company->name,
                'academyYear' => $internship->academy_year,
                'startDate' => $internship->start_date?->format('Y-m-d'),
                'endDate' => $internship->end_date?->format('Y-m-d'),
                'status' => $internship->status,
                'evaluationUrl' => config('app.url') . '/internships/evaluation?token=' . $evaluationToken,
                'garantEmail' => ($internship->garant && $internship->garant->user) ? $internship->garant->user->email : 'garant@school.sk',
            ];

            $emailSent = EmailService::send(EvaluationRequest::class, $internship->company->user->email, $data);

            if ($emailSent) {
                // Mark email as sent in internship_report JSON
                $report = $internship->internship_report ?: [];
                $report['email_sent_at'] = Carbon::now()->toIso8601String();
                $internship->internship_report = $report;
                
                // Also mark in evaluation for backward compatibility
                $evaluation = $internship->evaluation ?: [];
                $evaluation['email_sent_at'] = Carbon::now()->toIso8601String();
                $internship->evaluation = $evaluation;
                
                $internship->save();

                return response()->json([
                    'message' => 'Evaluation email sent successfully to ' . $internship->company->user->email,
                    'email' => $internship->company->user->email
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Failed to send email. Please try again or contact system administrator.'
                ], 500);
            }

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Internship not found.'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Error sending evaluation email: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred while sending the email.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get internship details for student (including evaluation).
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function studentShow($id)
    {
        try {
            $user = Auth::user();
            
            // Check if user has a student profile
            if (!$user->student) {
                return response()->json([
                    'message' => 'Student profile not found for this user.'
                ], 403);
            }
            
            // Get internship and verify it belongs to the student
            $internship = Internship::with(['student', 'company.user', 'garant.user'])
                ->where('id', $id)
                ->where('student_id', $user->student->id)
                ->firstOrFail();

            return response()->json([
                'data' => [
                    'id' => $internship->id,
                    'student_id' => $internship->student_id,
                    'company_id' => $internship->company_id,
                    'garant_id' => $internship->garant_id,
                    'status' => $internship->status,
                    'academy_year' => $internship->academy_year,
                    'start_date' => $internship->start_date?->format('Y-m-d'),
                    'end_date' => $internship->end_date?->format('Y-m-d'),
                    'evaluation' => $internship->evaluation,
                    'internship_report' => $internship->internship_report,
                    'company' => $internship->company ? [
                        'id' => $internship->company->id,
                        'name' => $internship->company->name,
                    ] : null,
                    'created_at' => $internship->created_at?->toIso8601String(),
                    'updated_at' => $internship->updated_at?->toIso8601String(),
                ]
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Internship not found.'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Error fetching internship: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred while fetching the internship.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Send report to company via email (student only).
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendReportToCompany($id)
    {
        try {
            $user = Auth::user();
            
            // Check if user has a student profile
            if (!$user->student) {
                return response()->json([
                    'message' => 'Student profile not found for this user.'
                ], 403);
            }
            
            // Get internship and verify it belongs to the student
            $internship = Internship::with(['student', 'company.user', 'garant.user'])
                ->where('id', $id)
                ->where('student_id', $user->student->id)
                ->firstOrFail();

            // Check if company exists and has email
            if (!$internship->company || !$internship->company->user || !$internship->company->user->email) {
                return response()->json([
                    'message' => 'Company email not found.'
                ], 400);
            }

            // Check if report already exists (company has already submitted report)
            $report = $internship->internship_report ?: [];
            if (isset($report['submitted_at'])) {
                return response()->json([
                    'message' => 'Company has already submitted the report. Email cannot be sent.'
                ], 400);
            }

            // Check if email was already sent
            if (isset($report['email_sent_at'])) {
                return response()->json([
                    'message' => 'Report email has already been sent. Please wait for company response.'
                ], 400);
            }

            // Check if internship has ended
            if (!$internship->end_date || Carbon::parse($internship->end_date)->gt(Carbon::today())) {
                return response()->json([
                    'message' => 'Internship has not ended yet.'
                ], 400);
            }

            // Generate secure token for evaluation form
            $evaluationToken = $this->generateEvaluationToken($internship->id);

            $emailData = [
                'studentName' => $internship->student->name . ' ' . $internship->student->surname,
                'companyName' => $internship->company->name,
                'academyYear' => $internship->academy_year,
                'startDate' => $internship->start_date?->format('Y-m-d'),
                'endDate' => $internship->end_date?->format('Y-m-d'),
                'status' => $internship->status,
                'evaluationUrl' => config('app.url') . '/internships/evaluation?token=' . $evaluationToken,
                'garantEmail' => ($internship->garant && $internship->garant->user) ? $internship->garant->user->email : 'garant@school.sk',
            ];

            // Send evaluation email
            $emailSent = EmailService::send(EvaluationRequest::class, $internship->company->user->email, $emailData);

            if ($emailSent) {
                try {
                    // Mark email as sent in internship_report JSON
                    $report = $internship->internship_report ?: [];
                    $report['email_sent_at'] = Carbon::now()->toIso8601String();
                    $internship->internship_report = $report;
                    
                    // Also mark in evaluation for backward compatibility
                    $evaluation = $internship->evaluation ?: [];
                    $evaluation['email_sent_at'] = Carbon::now()->toIso8601String();
                    $internship->evaluation = $evaluation;
                    
                    $internship->save();
                    
                    // Refresh the internship to get latest data
                    $internship->refresh();

                    return response()->json([
                        'message' => 'Report email sent successfully to ' . $internship->company->user->email,
                        'email' => $internship->company->user->email
                    ], 200);
                } catch (\Exception $saveException) {
                    \Log::error('Error saving internship after sending email: ' . $saveException->getMessage());
                    // Email was sent but saving failed - return success but log the error
                    return response()->json([
                        'message' => 'Email sent successfully, but there was an error updating the status. Please refresh the page.',
                        'email' => $internship->company->user->email,
                        'warning' => 'Status may not be updated correctly.'
                    ], 200);
                }
            } else {
                return response()->json([
                    'message' => 'Failed to send email. Please try again or contact system administrator.'
                ], 500);
            }

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Internship not found.'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Error sending report to company: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred while sending the report.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Generate secure token for evaluation form.
     *
     * @param  int  $internshipId
     * @return string
     */
    private function generateEvaluationToken($internshipId)
    {
        $data = [
            'internship_id' => $internshipId,
            'action' => 'evaluation',
            'timestamp' => Carbon::now()->timestamp,
            'expires_at' => Carbon::now()->addDays(30)->timestamp, // Token expires in 30 days
        ];

        return Crypt::encrypt($data);
    }

    /**
     * Validate and decrypt a secure token.
     *
     * @param  string  $token
     * @return array|null
     */
    private function validateSecureToken($token)
    {
        try {
            $data = Crypt::decrypt($token);

            // Check if token has expired
            if (isset($data['expires_at']) && Carbon::now()->timestamp > $data['expires_at']) {
                return null;
            }

            // Validate required fields
            if (!isset($data['internship_id'], $data['action'], $data['timestamp'])) {
                return null;
            }

            // Validate action
            if (!in_array($data['action'], ['confirm', 'reject'])) {
                return null;
            }

            return $data;

        } catch (\Exception $e) {
            \Log::warning('Failed to decrypt token: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get internships for the authenticated company (company-specific method).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function companyIndex()
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
            
            // Get only the company's own internships with document count
            $internships = Internship::with(['student', 'company', 'garant'])
                ->withCount('documents')
                ->where('company_id', $company->id)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'data' => $internships->map(function ($internship) {
                    // Count digital reports (internship_report) as documents
                    $digitalReportCount = 0;
                    $report = $internship->internship_report ?: [];
                    if (isset($report['submitted_at'])) {
                        $digitalReportCount = 1; // Digital report exists
                    }
                    
                    // Total documents count = uploaded documents + digital reports
                    $totalDocumentsCount = $internship->documents_count + $digitalReportCount;
                    
                    return [
                        'id' => $internship->id,
                        'student_id' => $internship->student_id,
                        'student' => $internship->student ? [
                            'id' => $internship->student->id,
                            'name' => $internship->student->name,
                            'surname' => $internship->student->surname,
                            'student_email' => $internship->student->student_email,
                        ] : null,
                        'company_id' => $internship->company_id,
                        'company' => $internship->company ? [
                            'id' => $internship->company->id,
                            'name' => $internship->company->name,
                        ] : null,
                        'garant_id' => $internship->garant_id,
                        'garant' => $internship->garant ? [
                            'id' => $internship->garant->id,
                            'name' => $internship->garant->name ?? null,
                            'surname' => $internship->garant->surname ?? null,
                        ] : null,
                        'status' => $internship->status,
                        'academy_year' => $internship->academy_year,
                        'start_date' => $internship->start_date?->format('Y-m-d'),
                        'end_date' => $internship->end_date?->format('Y-m-d'),
                        'confirmed_date' => $internship->confirmed_date?->format('Y-m-d'),
                        'approved_date' => $internship->approved_date?->format('Y-m-d'),
                        'documents_count' => $totalDocumentsCount,
                        'created_at' => $internship->created_at?->toIso8601String(),
                        'updated_at' => $internship->updated_at?->toIso8601String(),
                    ];
                })
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Error fetching company internships: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred while fetching internships.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get internship details for company (company-specific method).
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function companyShow($id)
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
            
            // Get internship and verify it belongs to the company
            $internship = Internship::with(['student', 'company', 'garant', 'documents'])
                ->where('id', $id)
                ->where('company_id', $company->id)
                ->firstOrFail();

            return response()->json([
                'data' => [
                    'id' => $internship->id,
                    'student_id' => $internship->student_id,
                    'student' => $internship->student ? [
                        'id' => $internship->student->id,
                        'name' => $internship->student->name,
                        'surname' => $internship->student->surname,
                        'student_email' => $internship->student->student_email,
                    ] : null,
                    'company_id' => $internship->company_id,
                    'company' => $internship->company ? [
                        'id' => $internship->company->id,
                        'name' => $internship->company->name,
                    ] : null,
                    'garant_id' => $internship->garant_id,
                    'garant' => $internship->garant ? [
                        'id' => $internship->garant->id,
                        'name' => $internship->garant->name ?? null,
                        'surname' => $internship->garant->surname ?? null,
                    ] : null,
                    'status' => $internship->status,
                    'academy_year' => $internship->academy_year,
                    'start_date' => $internship->start_date?->format('Y-m-d'),
                    'end_date' => $internship->end_date?->format('Y-m-d'),
                    'confirmed_date' => $internship->confirmed_date?->format('Y-m-d'),
                    'approved_date' => $internship->approved_date?->format('Y-m-d'),
                    'evaluation' => $internship->evaluation,
                    'internship_report' => $internship->internship_report,
                    'created_at' => $internship->created_at?->toIso8601String(),
                    'updated_at' => $internship->updated_at?->toIso8601String(),
                ]
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Internship not found or access denied.'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Error fetching company internship: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred while fetching the internship.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}
