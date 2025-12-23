<?php

namespace App\Http\Controllers;

use App\Models\Internship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Controller for third-party API access to internships
 * Limited to specific endpoints only for external integration
 */
class ExternalInternshipController extends BaseApiController
{
    /**
     * Get all internships as objects (limited data)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return $this->executeWithExceptionHandling(function () {
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

            return $this->respondWithCollection($internships, function ($internship) {
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
            });
        }, 'fetching external internships');
    }

    /**
     * Defend internship - change status from 'schválená' to 'obhájená'
     * Only allows this specific status change
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function defend($id)
    {
        return $this->executeWithExceptionHandling(function () use ($id) {
            $internship = Internship::findOrFail($id);

            // Validate that the internship is in 'schválená' (schválená) status
            if ($internship->status !== Internship::STATUS_SCHVALENA) {
                return response()->json([
                    'message' => 'Status change not allowed. Internship must be in "schválená" status to be defended.',
                    'current_status' => $internship->status,
                    'required_status' => Internship::STATUS_SCHVALENA
                ], 422);
            }

            // Update status to 'obhájená' (defended)
            $internship->update(['status' => Internship::STATUS_OBHAJENA]);

            Log::info('Internship status changed via external API', [
                'internship_id' => $internship->id,
                'old_status' => Internship::STATUS_SCHVALENA,
                'new_status' => Internship::STATUS_OBHAJENA,
                'changed_via' => 'external-api'
            ]);

            return response()->json([
                'message' => 'Internship successfully defended.',
                'data' => [
                    'id' => $internship->id,
                    'old_status' => Internship::STATUS_SCHVALENA,
                    'new_status' => Internship::STATUS_OBHAJENA,
                    'changed_at' => now()->toIso8601String(),
                ]
            ], 200);

        }, 'defending internship via external API');
    }
}
