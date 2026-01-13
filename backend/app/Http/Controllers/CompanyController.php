<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\ContactPerson;
use App\Models\Garant;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CompanyController extends Controller
{
    /**
     * Display a listing of all ACCEPTED companies.
     * Used for dropdown selections in forms.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $companies = Cache::remember('companies', now()->addHours(8), function() {
                return Company::select('id', 'name', 'city', 'state', 'region')
                    ->orderBy('name')
                    ->get();
            });

            return response()->json([
                'data' => $companies->map(function ($company) {
                    return [
                        'id' => $company->id,
                        'name' => $company->name,
                        'city' => $company->city,
                        'state' => $company->state,
                        'region' => $company->region,
                        'location' => ($company->city && $company->state) 
                            ? ($company->city . ', ' . $company->state)
                            : null,
                    ];
                })
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Error fetching companies: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred while fetching companies.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Display the specified company.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $company = Company::with(['contactPersons'])->findOrFail($id);
            $primaryContact = $company->contactPersons->first();

            return response()->json([
                'data' => [
                    'id' => $company->id,
                    'name' => $company->name,
                    'user_id' => $company->user_id,
                    'state' => $company->state,
                    'region' => $company->region,
                    'city' => $company->city,
                    'postal_code' => $company->postal_code,
                    'street' => $company->street,
                    'house_number' => $company->house_number,
                    'status' => $company->status,
                    'contact_person_name' => $primaryContact?->name,
                    'contact_person_surname' => $primaryContact?->surname,
                    'contact_person_email' => $primaryContact?->email,
                    'contact_person_phone' => $primaryContact?->phone_number,
                    'created_at' => $company->created_at?->toIso8601String(),
                    'updated_at' => $company->updated_at?->toIso8601String(),
                ]
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Company not found.'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Error fetching company: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred while fetching the company.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Create a new company request (for students or public)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createCompany(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'state' => 'nullable|string|max:100',
            'region' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'street' => 'nullable|string|max:100',
            'house_number' => 'nullable|string|max:20',
            'contact_person_name' => 'required|string|max:100',
            'contact_person_surname' => 'required|string|max:100',
            'contact_person_email' => 'required|email|max:100',
            'contact_person_phone' => 'nullable|string|max:50',
            'contact_person_position' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Check if email already exists in users or contact_persons table
            $emailExists = User::where('email', $request->contact_person_email)->exists() ||
                           ContactPerson::where('email', $request->contact_person_email)->exists();
            
            if ($emailExists) {
                return response()->json([
                    'message' => 'This email address is already registered in the system. Please use a different email address.',
                    'errors' => [
                        'contact_person_email' => ['This email address is already in use.']
                    ]
                ], 422);
            }

            DB::beginTransaction();
            
            $user = $request->user('api'); // Use 'api' guard to get authenticated user
           
            // Create the company with pending status
            $company = Company::create([
                'name' => $request->name,
                'state' => $request->state,
                'region' => $request->region,
                'city' => $request->city,
                'postal_code' => $request->postal_code,
                'street' => $request->street,
                'house_number' => $request->house_number,
                'status' => Company::STATUS_PENDING,
                'request_source' => $user ? Company::SOURCE_STUDENT : Company::SOURCE_PUBLIC,
            ]);

            // Create the contact person and assign to company
            ContactPerson::create([
                'name' => $request->contact_person_name,
                'surname' => $request->contact_person_surname,
                'email' => $request->contact_person_email,
                'phone_number' => $request->contact_person_phone,
                'position' => $request->contact_person_position,
                'company_id' => $company->id,
            ]);

            DB::commit();

            // Notify all garants about the new company request
            $this->notifyGarants($company);

            return response()->json([
                'message' => 'Company registration request submitted successfully. A garant will review your request.',
                'data' => [
                    'id' => $company->id,
                    'name' => $company->name,
                    'status' => $company->status,
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating company request: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred while submitting the company request.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * List all company requests (for garants)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function listRequests(Request $request)
    {
        try {
            $status = $request->query('status'); // pending, accepted, declined, or all

            $query = Company::query()
                ->with(['reviewedByGarant', 'contactPersons'])
                ->orderBy('created_at', 'desc');

            if ($status && $status !== 'all') {
                $query->where('status', $status);
            }

            $companies = $query->get();

            return response()->json([
                'data' => $companies->map(function ($company) {
                    $primaryContact = $company->contactPersons->first();
                    
                    return [
                        'id' => $company->id,
                        'name' => $company->name,
                        'state' => $company->state,
                        'region' => $company->region,
                        'city' => $company->city,
                        'postal_code' => $company->postal_code,
                        'street' => $company->street,
                        'house_number' => $company->house_number,
                        'contact_person' => $primaryContact ? [
                            'name' => $primaryContact->name,
                            'surname' => $primaryContact->surname,
                            'email' => $primaryContact->email,
                            'phone' => $primaryContact->phone_number,
                            'position' => $primaryContact->position,
                        ] : null,
                        'status' => $company->status,
                        'request_source' => $company->request_source,
                        'reviewed_by' => $company->reviewedByGarant ? [
                            'id' => $company->reviewedByGarant->id,
                            'name' => $company->reviewedByGarant->full_name,
                        ] : null,
                        'rejection_reason' => $company->rejection_reason,
                        'reviewed_at' => $company->reviewed_at?->toIso8601String(),
                        'created_at' => $company->created_at?->toIso8601String(),
                        'updated_at' => $company->updated_at?->toIso8601String(),
                    ];
                })
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Error fetching company requests: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred while fetching company requests.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Approve a company request
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function approve(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $company = Company::with('contactPersons')->findOrFail($id);

            if (!$company->isPending()) {
                return response()->json([
                    'message' => 'This request has already been processed.'
                ], 400);
            }

            // Find the garant associated with the current user
            $user = $request->user();
            $garant = Garant::where('user_id', $user->id)->first();

            if (!$garant) {
                return response()->json([
                    'message' => 'Garant profile not found for current user.'
                ], 403);
            }

            // Get the primary contact person
            $contactPerson = $company->contactPersons->first();
            
            if (!$contactPerson) {
                return response()->json([
                    'message' => 'No contact person found for this company.'
                ], 400);
            }

            // Check if a user with this email already exists
            $existingUser = User::where('email', $contactPerson->email)->first();
            
            if ($existingUser) {
                // Email already exists - cannot approve this company
                DB::rollBack();
                return response()->json([
                    'message' => 'A user account with this email address already exists. The company cannot be approved with a duplicate email. Please contact the company to use a different email address.'
                ], 400);
            }

            // Generate random password
            $password = \Illuminate\Support\Str::random(12);

            // Create user account for the company
            $companyUser = User::create([
                'name' => $contactPerson->name,
                'email' => $contactPerson->email,
                'password' => \Hash::make($password),
                'role' => 'company',
            ]);

            // Update the company status and link to user
            $company->update([
                'status' => Company::STATUS_ACCEPTED,
                'reviewed_by_garant_id' => $garant->id,
                'reviewed_at' => now(),
                'user_id' => $companyUser->id,
            ]);

            // Clear the companies cache (without tags, since database cache doesn't support tagging)
            Cache::forget('companies');

            // Send email with credentials
            try {
                \Mail::to($contactPerson->email)->send(
                    new \App\Mail\CompanyCredentials(
                        $company->name,
                        $contactPerson->name . ' ' . $contactPerson->surname,
                        $contactPerson->email,
                        $password
                    )
                );
            } catch (\Exception $e) {
                \Log::error('Failed to send company credentials email: ' . $e->getMessage());
                // Don't fail the approval if email fails
            }

            DB::commit();

            return response()->json([
                'message' => 'Company request approved successfully. Login credentials have been sent to the contact person.',
                'data' => [
                    'id' => $company->id,
                    'name' => $company->name,
                    'status' => $company->status,
                ]
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Company not found.'
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error approving company request: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred while approving the company request.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Reject a company request
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function reject(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'rejection_reason' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $company = Company::with('contactPersons')->findOrFail($id);

            if (!$company->isPending()) {
                return response()->json([
                    'message' => 'This request has already been processed.'
                ], 400);
            }

            // Find the garant associated with the current user
            $user = $request->user();
            $garant = Garant::where('user_id', $user->id)->first();

            if (!$garant) {
                return response()->json([
                    'message' => 'Garant profile not found for current user.'
                ], 403);
            }

            // Get the primary contact person
            $contactPerson = $company->contactPersons->first();

            // Update the company status
            $company->update([
                'status' => Company::STATUS_DECLINED,
                'reviewed_by_garant_id' => $garant->id,
                'reviewed_at' => now(),
                'rejection_reason' => $request->rejection_reason,
            ]);

            // Send rejection email to contact person
            if ($contactPerson) {
                try {
                    \Mail::to($contactPerson->email)->send(
                        new \App\Mail\CompanyRejected(
                            $company->name,
                            $contactPerson->name . ' ' . $contactPerson->surname,
                            $request->rejection_reason
                        )
                    );
                } catch (\Exception $e) {
                    \Log::error('Failed to send company rejection email: ' . $e->getMessage());
                    // Don't fail the rejection if email fails
                }
            }

            return response()->json([
                'message' => 'Company request rejected.',
                'data' => [
                    'id' => $company->id,
                    'name' => $company->name,
                    'status' => $company->status,
                    'rejection_reason' => $company->rejection_reason,
                ]
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Company not found.'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Error rejecting company request: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred while rejecting the company request.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Notify all garants about a new company request
     *
     * @param Company $company
     */
    protected function notifyGarants(Company $company)
    {
        try {
            // Get all garant users
            $garantUsers = User::where('role', 'garant')->get();

            $source = $company->request_source === Company::SOURCE_PUBLIC 
                ? 'public registration' 
                : 'student request';

            foreach ($garantUsers as $garantUser) {
                NotificationService::create(
                    $garantUser->id,
                    'company_request_created',
                    'New Company Request',
                    "A new company registration request for '{$company->name}' has been submitted via {$source}.",
                    ['company_id' => $company->id]
                );
            }
        } catch (\Exception $e) {
            \Log::error('Error notifying garants about company request: ' . $e->getMessage());
            // Don't throw exception - notification failure shouldn't stop the request
        }
    }
}
