<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanyRequest;
use App\Models\ContactPerson;
use App\Models\Garant;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CompanyRequestController extends Controller
{

    /**
     * Submit a public company registration request (no authentication required)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function publicRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_name' => 'required|string|max:100',
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
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $companyRequest = CompanyRequest::create([
                'company_name' => $request->company_name,
                'state' => $request->state,
                'region' => $request->region,
                'city' => $request->city,
                'postal_code' => $request->postal_code,
                'street' => $request->street,
                'house_number' => $request->house_number,
                'contact_person_name' => $request->contact_person_name,
                'contact_person_surname' => $request->contact_person_surname,
                'contact_person_email' => $request->contact_person_email,
                'contact_person_phone' => $request->contact_person_phone,
                'status' => CompanyRequest::STATUS_PENDING,
                'request_source' => CompanyRequest::SOURCE_PUBLIC,
            ]);

            // Notify all garants about the new company request
            $this->notifyGarants($companyRequest);

            return response()->json([
                'message' => 'Company registration request submitted successfully. A garant will review your request.',
                'data' => [
                    'id' => $companyRequest->id,
                    'company_name' => $companyRequest->company_name,
                    'status' => $companyRequest->status,
                ]
            ], 201);

        } catch (\Exception $e) {
            \Log::error('Error creating company request: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred while submitting the company request.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Submit a company request from a student (authenticated)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function studentRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_name' => 'required|string|max:100',
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
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = $request->user();

            $companyRequest = CompanyRequest::create([
                'company_name' => $request->company_name,
                'state' => $request->state,
                'region' => $request->region,
                'city' => $request->city,
                'postal_code' => $request->postal_code,
                'street' => $request->street,
                'house_number' => $request->house_number,
                'contact_person_name' => $request->contact_person_name,
                'contact_person_surname' => $request->contact_person_surname,
                'contact_person_email' => $request->contact_person_email,
                'contact_person_phone' => $request->contact_person_phone,
                'status' => CompanyRequest::STATUS_PENDING,
                'request_source' => CompanyRequest::SOURCE_STUDENT,
                'requested_by_user_id' => $user->id,
            ]);

            // Notify all garants about the new company request
            $this->notifyGarants($companyRequest);

            return response()->json([
                'message' => 'Company request submitted successfully. A garant will review your request.',
                'data' => [
                    'id' => $companyRequest->id,
                    'company_name' => $companyRequest->company_name,
                    'status' => $companyRequest->status,
                ]
            ], 201);

        } catch (\Exception $e) {
            \Log::error('Error creating student company request: ' . $e->getMessage());

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
    public function index(Request $request)
    {
        try {
            $status = $request->query('status'); // pending, approved, rejected, or all

            $query = CompanyRequest::query()
                ->with(['requestedByUser', 'reviewedByGarant', 'company'])
                ->orderBy('created_at', 'desc');

            if ($status && $status !== 'all') {
                $query->where('status', $status);
            }

            $requests = $query->get();

            return response()->json([
                'data' => $requests->map(function ($req) {
                    return [
                        'id' => $req->id,
                        'company_name' => $req->company_name,
                        'state' => $req->state,
                        'region' => $req->region,
                        'city' => $req->city,
                        'postal_code' => $req->postal_code,
                        'street' => $req->street,
                        'house_number' => $req->house_number,
                        'contact_person_name' => $req->contact_person_name,
                        'contact_person_surname' => $req->contact_person_surname,
                        'contact_person_email' => $req->contact_person_email,
                        'contact_person_phone' => $req->contact_person_phone,
                        'status' => $req->status,
                        'request_source' => $req->request_source,
                        'requested_by' => $req->requestedByUser ? [
                            'id' => $req->requestedByUser->id,
                            'name' => $req->requestedByUser->name,
                            'email' => $req->requestedByUser->email,
                        ] : null,
                        'reviewed_by' => $req->reviewedByGarant ? [
                            'id' => $req->reviewedByGarant->id,
                            'name' => $req->reviewedByGarant->full_name,
                        ] : null,
                        'rejection_reason' => $req->rejection_reason,
                        'reviewed_at' => $req->reviewed_at?->toIso8601String(),
                        'company_id' => $req->company_id,
                        'created_at' => $req->created_at?->toIso8601String(),
                        'updated_at' => $req->updated_at?->toIso8601String(),
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
     * Show a specific company request
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $request = CompanyRequest::with(['requestedByUser', 'reviewedByGarant', 'company'])
                ->findOrFail($id);

            return response()->json([
                'data' => [
                    'id' => $request->id,
                    'company_name' => $request->company_name,
                    'state' => $request->state,
                    'region' => $request->region,
                    'city' => $request->city,
                    'postal_code' => $request->postal_code,
                    'street' => $request->street,
                    'house_number' => $request->house_number,
                    'contact_person_name' => $request->contact_person_name,
                    'contact_person_surname' => $request->contact_person_surname,
                    'contact_person_email' => $request->contact_person_email,
                    'contact_person_phone' => $request->contact_person_phone,
                    'status' => $request->status,
                    'request_source' => $request->request_source,
                    'requested_by' => $request->requestedByUser ? [
                        'id' => $request->requestedByUser->id,
                        'name' => $request->requestedByUser->name,
                        'email' => $request->requestedByUser->email,
                    ] : null,
                    'reviewed_by' => $request->reviewedByGarant ? [
                        'id' => $request->reviewedByGarant->id,
                        'name' => $request->reviewedByGarant->full_name,
                    ] : null,
                    'rejection_reason' => $request->rejection_reason,
                    'reviewed_at' => $request->reviewed_at?->toIso8601String(),
                    'company_id' => $request->company_id,
                    'created_at' => $request->created_at?->toIso8601String(),
                    'updated_at' => $request->updated_at?->toIso8601String(),
                ]
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Company request not found.'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Error fetching company request: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred while fetching the company request.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Approve a company request and create the company
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function approve(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $companyRequest = CompanyRequest::findOrFail($id);

            if (!$companyRequest->isPending()) {
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

            // Create the company
            $company = Company::create([
                'name' => $companyRequest->company_name,
                'state' => $companyRequest->state,
                'region' => $companyRequest->region,
                'city' => $companyRequest->city,
                'postal_code' => $companyRequest->postal_code,
                'street' => $companyRequest->street,
                'house_number' => $companyRequest->house_number,
            ]);

            // Create the contact person
            ContactPerson::create([
                'name' => $companyRequest->contact_person_name,
                'surname' => $companyRequest->contact_person_surname,
                'email' => $companyRequest->contact_person_email,
                'phone_number' => $companyRequest->contact_person_phone,
                'company_id' => $company->id,
            ]);

            // Update the request status
            $companyRequest->update([
                'status' => CompanyRequest::STATUS_APPROVED,
                'reviewed_by_garant_id' => $garant->id,
                'reviewed_at' => now(),
                'company_id' => $company->id,
            ]);

            // Notify the requester if it was a student request
            if ($companyRequest->request_source === CompanyRequest::SOURCE_STUDENT && $companyRequest->requested_by_user_id) {
                NotificationService::create(
                    $companyRequest->requested_by_user_id,
                    'company_request',
                    'Company Request Approved',
                    "Your company request for '{$companyRequest->company_name}' has been approved and is now available in the system.",
                    ['request_id' => $companyRequest->id]
                );
            }

            DB::commit();

            return response()->json([
                'message' => 'Company request approved successfully.',
                'data' => [
                    'request_id' => $companyRequest->id,
                    'company_id' => $company->id,
                    'company_name' => $company->name,
                ]
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Company request not found.'
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
            $companyRequest = CompanyRequest::findOrFail($id);

            if (!$companyRequest->isPending()) {
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

            // Update the request status
            $companyRequest->update([
                'status' => CompanyRequest::STATUS_REJECTED,
                'reviewed_by_garant_id' => $garant->id,
                'reviewed_at' => now(),
                'rejection_reason' => $request->rejection_reason,
            ]);

            // Notify the requester if it was a student request
            if ($companyRequest->request_source === CompanyRequest::SOURCE_STUDENT && $companyRequest->requested_by_user_id) {
                NotificationService::create(
                    $companyRequest->requested_by_user_id,
                    'company_request',
                    'Company Request Rejected',
                    "Your company request for '{$companyRequest->company_name}' has been rejected. Reason: {$request->rejection_reason}",
                    ['request_id' => $companyRequest->id]
                );
            }

            return response()->json([
                'message' => 'Company request rejected.',
                'data' => [
                    'request_id' => $companyRequest->id,
                    'status' => $companyRequest->status,
                    'rejection_reason' => $companyRequest->rejection_reason,
                ]
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Company request not found.'
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
     * @param CompanyRequest $companyRequest
     */
    protected function notifyGarants(CompanyRequest $companyRequest)
    {
        try {
            // Get all garant users
            $garantUsers = User::where('role', 'garant')->get();

            $source = $companyRequest->request_source === CompanyRequest::SOURCE_PUBLIC 
                ? 'public registration' 
                : 'student request';

            foreach ($garantUsers as $garantUser) {
                NotificationService::create(
                    $garantUser->id,
                    'company_request',
                    'New Company Request',
                    "A new company registration request for '{$companyRequest->company_name}' has been submitted via {$source}.",
                    ['request_id' => $companyRequest->id]
                );
            }
        } catch (\Exception $e) {
            \Log::error('Error notifying garants about company request: ' . $e->getMessage());
            // Don't throw exception - notification failure shouldn't stop the request
        }
    }
}
