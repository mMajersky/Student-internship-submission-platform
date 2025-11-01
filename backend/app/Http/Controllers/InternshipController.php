<?php

namespace App\Http\Controllers;

use App\Models\Internship;
use App\Models\Student;
use App\Models\Company;
use App\Models\Garant;
use App\Models\Notification;
use App\Mail\InternshipCreatedNotification;
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

            // Send notification to company if created with "potvrdená" status
            if ($validated['status'] === Internship::STATUS_POTVRDENA && $internship->company && $internship->company->user) {
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
                ];

                // Create notification and email
                NotificationService::createAndNotify(
                    $internship->company->user->id,
                    Notification::TYPE_APPROVAL_REQUEST,
                    'Nová žiadosť o stáž',
                    'Študent ' . $internship->student->name . ' ' . $internship->student->surname . ' žiada o potvrdenie stáže.',
                    ['internship_id' => $internship->id],
                    InternshipCreatedNotification::class,
                    $data
                );
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
                        'documents_count' => $internship->documents_count,
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

            // Update the internship
            $internship->update($validated);

            // Load relationships for the response
            $internship->load(['student.user', 'company.user', 'garant.user']);

            // Send notification to company if status changed to "potvrdená"
            if ($statusChangedToPotvrdena && $internship->company && $internship->company->user) {
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
                ];

                NotificationService::createAndNotify(
                    $internship->company->user->id,
                    Notification::TYPE_APPROVAL_REQUEST,
                    'Žiadosť o potvrdenie stáže',
                    'Študent ' . $internship->student->name . ' ' . $internship->student->surname . ' žiada o potvrdenie stáže.',
                    ['internship_id' => $internship->id],
                    InternshipCreatedNotification::class,
                    $data
                );
            }

            // Notify student about status change
            if ($statusChanged && $internship->student && $internship->student->user) {
                NotificationService::create(
                    $internship->student->user->id,
                    Notification::TYPE_INTERNSHIP_STATUS_CHANGED,
                    'Stav praxe bol zmenený',
                    'Stav vašej praxe bol zmenený na: ' . $internship->status,
                    ['internship_id' => $internship->id, 'old_status' => $oldStatus, 'new_status' => $internship->status]
                );
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
            // Add the authenticated student's ID to the validated data
            $validated['student_id'] = $user->student->id;

            // Create the internship
            $internship = Internship::create($validated);

            // Load relationships for the response
            $internship->load(['student', 'company.user', 'garant']);



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

            $internship = Internship::findOrFail($tokenData['internship_id']);

            // Check if internship is still in "Potvrdená" status
            if ($internship->status !== Internship::STATUS_POTVRDENA) {
                return response()->json([
                    'message' => 'This internship has already been processed and is no longer available for action.',
                    'current_status' => $internship->status,
                    'error' => 'already_resolved'
                ], 400);
            }

            // Update status based on action
            $newStatus = $tokenData['action'] === 'confirm'
                ? Internship::STATUS_SCHVALENA // Company confirmed/approved
                : Internship::STATUS_ZAMIETNUTA; // Company rejected

            $internship->update(['status' => $newStatus]);

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
}
