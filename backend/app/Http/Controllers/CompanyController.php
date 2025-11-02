<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends BaseApiController
{
    /**
     * Display a listing of all companies.
     * Used for dropdown selections in forms.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return $this->executeWithExceptionHandling(function () {
            $companies = Company::select('id', 'name', 'city', 'state', 'region')
                ->orderBy('name')
                ->get();

            return $this->respondWithCollection($companies, function ($company) {
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
            });
        }, 'fetching companies');
    }

    /**
     * Display the specified company.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        return $this->executeWithExceptionHandling(function () use ($id) {
            $company = Company::with(['contactPersons'])->findOrFail($id);

            return $this->respondWithResource($company, function ($company) {
                return [
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
                ];
            });
        }, 'fetching company');
    }
}
