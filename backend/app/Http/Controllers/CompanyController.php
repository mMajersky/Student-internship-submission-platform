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
            $companies = Cache::tags(['dropdowns'])->remember('companies', now()->addHours(8), function() {
                return Company::select('id', 'name', 'city', 'state', 'region', 'status')
                    ->where('status', Company::STATUS_ACCEPTED)
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
                    'contact_persons' => $company->contactPersons,
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

            $company = Company::findOrFail($id);

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

            // Update the company status
            $company->update([
                'status' => Company::STATUS_ACCEPTED,
                'reviewed_by_garant_id' => $garant->id,
                'reviewed_at' => now(),
            ]);

            // Clear the companies cache
            Cache::tags(['dropdowns'])->forget('companies');



            DB::commit();

            return response()->json([
                'message' => 'Company request approved successfully.',
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
            $company = Company::findOrFail($id);

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

            // Update the company status
            $company->update([
                'status' => Company::STATUS_DECLINED,
                'reviewed_by_garant_id' => $garant->id,
                'reviewed_at' => now(),
                'rejection_reason' => $request->rejection_reason,
            ]);



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
