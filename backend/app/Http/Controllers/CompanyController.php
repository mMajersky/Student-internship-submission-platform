<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    /**
     * Display a listing of all companies.
     * Used for dropdown selections in forms.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $companies = Company::select('id', 'name', 'city', 'state', 'region')
                ->orderBy('name')
                ->get();

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
}
