<?php

namespace App\Http\Controllers;

use App\Models\Internship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Controller for third-party API access to internships
 * Limited to specific endpoints only for external integration
 */
class ExternalInternshipController extends Controller
{
    /**
     * Get all internships as objects (limited data)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            // Get all internships with basic relationships for external use
            $internships = Internship::with(['student:id,name,surname', 'company:id,name'])
                ->select([
                    'id',
                    'student_id',
                    'company_id',
                    'status',
                    'academy_year',
                    'start_date',
                    'end_date',
                    'created_at',
                    'updated_at'
                ])
                ->get();

            return response()->json([
                'data' => $internships->map(function ($internship) {
                    return [
                        'id' => $internship->id,
                        'student' => $internship->student ? [
                            'id' => $internship->student->id,
                            'name' => $internship->student->name,
                            'surname' => $internship->student->surname,
                        ] : null,
                        'company' => $internship->company ? [
                            'id' => $internship->company->id,
                            'name' => $internship->company->name,
                        ] : null,
                        'status' => $internship->status,
                        'academy_year' => $internship->academy_year,
                        'start_date' => $internship->start_date?->format('Y-m-d'),
                        'end_date' => $internship->end_date?->format('Y-m-d'),
                        'created_at' => $internship->created_at?->toIso8601String(),
                        'updated_at' => $internship->updated_at?->toIso8601String(),
                    ];
                }),
                'count' => $internships->count()
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error fetching external internships: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred while fetching internships.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Defend internship - change status from 'approved by garant' to 'defended by student'
     * Only allows this specific status change
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function defend($id)
    {
        try {
            $internship = Internship::findOrFail($id);

            // Validate that the internship is in 'approved by garant' status
            if ($internship->status !== Internship::STATUS_APPROVED) {
                return response()->json([
                    'message' => 'Status change not allowed. Internship must be in "approved by garant" status to be defended.',
                    'current_status' => $internship->status,
                    'required_status' => Internship::STATUS_APPROVED
                ], 422);
            }

            // Update status to 'defended by student'
            $internship->update(['status' => Internship::STATUS_DEFENDED]);

            Log::info('Internship status changed via external API', [
                'internship_id' => $internship->id,
                'old_status' => Internship::STATUS_APPROVED,
                'new_status' => Internship::STATUS_DEFENDED,
                'changed_via' => 'external-api'
            ]);

            return response()->json([
                'message' => 'Internship successfully defended.',
                'data' => [
                    'id' => $internship->id,
                    'old_status' => Internship::STATUS_APPROVED,
                    'new_status' => Internship::STATUS_DEFENDED,
                    'changed_at' => now()->toIso8601String(),
                ]
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Internship not found.'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error defending internship via external API: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred while defending the internship.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}
