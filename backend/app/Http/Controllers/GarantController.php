<?php

namespace App\Http\Controllers;

use App\Models\Garant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;


class GarantController extends Controller
{
    /**
     * Display a listing of all garants.
     * GET /api/garants
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $garants = Cache::remember('garants', now()->addHours(8), function() {
                return Garant::with('user:id,name,email,created_at')
                    ->orderBy('created_at', 'desc')
                    ->get();
            });

            return response()->json([
                'data' => $garants->map(function ($garant) {
                    return [
                        'id' => $garant->id,
                        'name' => $garant->name,
                        'surname' => $garant->surname,
                        'full_name' => $garant->getFullNameAttribute(),
                        'faculty' => $garant->faculty,
                        'user_id' => $garant->user_id,
                        'email' => $garant->user->email ?? null,
                        'created_at' => $garant->user->created_at?->toIso8601String(),
                    ];
                })
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Error fetching garants: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred while fetching garants.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Store a newly created garant in storage.
     * POST /api/garants
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'faculty' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Start a database transaction
            DB::beginTransaction();

            // Create the user account with role 'garant'
            $user = User::create([
                'name' => $request->input('name') . ' ' . $request->input('surname'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password')),
                'role' => 'garant',
            ]);

            // Create the garant profile
            $garant = Garant::create([
                'name' => $request->input('name'),
                'surname' => $request->input('surname'),
                'faculty' => $request->input('faculty', 'Faculty of Informatics'),
                'user_id' => $user->id,
            ]);

            // Commit the transaction
            DB::commit();

            // Clear dropdown caches to reflect new garant
            Cache::forget('companies');
            Cache::forget('students');
            Cache::forget('garants');

            return response()->json([
                'message' => 'Garant created successfully.',
                'data' => [
                    'id' => $garant->id,
                    'name' => $garant->name,
                    'surname' => $garant->surname,
                    'full_name' => $garant->getFullNameAttribute(),
                    'faculty' => $garant->faculty,
                    'user_id' => $garant->user_id,
                    'email' => $user->email,
                    'created_at' => $user->created_at->toIso8601String(),
                ]
            ], 201);

        } catch (\Exception $e) {
            // Rollback the transaction on error
            DB::rollBack();
            
            \Log::error('Error creating garant: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred while creating the garant.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Display the specified garant.
     * GET /api/garants/{id}
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $garant = Garant::with('user:id,name,email,created_at')->findOrFail($id);

            return response()->json([
                'data' => [
                    'id' => $garant->id,
                    'name' => $garant->name,
                    'surname' => $garant->surname,
                    'full_name' => $garant->getFullNameAttribute(),
                    'faculty' => $garant->faculty,
                    'user_id' => $garant->user_id,
                    'email' => $garant->user->email ?? null,
                    'created_at' => $garant->user->created_at?->toIso8601String(),
                    'updated_at' => $garant->updated_at?->toIso8601String(),
                ]
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Garant not found.'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Error fetching garant: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred while fetching the garant.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Update the specified garant in storage.
     * PUT /api/garants/{id}
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $garant = Garant::with('user')->findOrFail($id);

            // Validate the incoming request
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'surname' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $garant->user_id,
                'password' => 'sometimes|nullable|string|min:8',
                'faculty' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Start a database transaction
            DB::beginTransaction();

            // Update garant profile
            $garantData = [];
            if ($request->has('name')) {
                $garantData['name'] = $request->input('name');
            }
            if ($request->has('surname')) {
                $garantData['surname'] = $request->input('surname');
            }
            if ($request->has('faculty')) {
                $garantData['faculty'] = $request->input('faculty');
            }

            if (!empty($garantData)) {
                $garant->update($garantData);
            }

            // Update user account if needed
            $userData = [];
            if ($request->has('email')) {
                $userData['email'] = $request->input('email');
            }
            if ($request->has('name') || $request->has('surname')) {
                $name = $request->input('name', $garant->name);
                $surname = $request->input('surname', $garant->surname);
                $userData['name'] = $name . ' ' . $surname;
            }
            if ($request->has('password') && !empty($request->input('password'))) {
                $userData['password'] = Hash::make($request->input('password'));
            }

            if (!empty($userData)) {
                $garant->user->update($userData);
            }

            // Commit the transaction
            DB::commit();

            // Refresh the garant to get updated data
            $garant->refresh();
            $garant->load('user');

            // Clear dropdown caches to reflect updated garant
            Cache::forget('companies');
            Cache::forget('students');
            Cache::forget('garants');

            return response()->json([
                'message' => 'Garant updated successfully.',
                'data' => [
                    'id' => $garant->id,
                    'name' => $garant->name,
                    'surname' => $garant->surname,
                    'full_name' => $garant->getFullNameAttribute(),
                    'faculty' => $garant->faculty,
                    'user_id' => $garant->user_id,
                    'email' => $garant->user->email,
                    'updated_at' => $garant->updated_at?->toIso8601String(),
                ]
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Garant not found.'
            ], 404);
        } catch (\Exception $e) {
            // Rollback the transaction on error
            DB::rollBack();
            
            \Log::error('Error updating garant: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred while updating the garant.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Remove the specified garant from storage.
     * DELETE /api/garants/{id}
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $garant = Garant::with('user')->findOrFail($id);

            // Start a database transaction
            DB::beginTransaction();

            $userId = $garant->user_id;

            // Delete the garant profile first
            $garant->delete();

            // Delete the associated user account
            if ($userId) {
                User::where('id', $userId)->delete();
            }

            // Commit the transaction
            DB::commit();

            // Clear dropdown caches to reflect deleted garant
            Cache::forget('companies');
            Cache::forget('students');
            Cache::forget('garants');

            return response()->json([
                'message' => 'Garant deleted successfully.'
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Garant not found.'
            ], 404);
        } catch (\Exception $e) {
            // Rollback the transaction on error
            DB::rollBack();
            
            \Log::error('Error deleting garant: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred while deleting the garant.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}
