<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    /**
     * Display a listing of all students.
     * Used for dropdown selections in forms.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $students = Student::select('id', 'name', 'surname', 'student_email')
                ->orderBy('surname')
                ->orderBy('name')
                ->get();

            return response()->json([
                'data' => $students->map(function ($student) {
                    return [
                        'id' => $student->id,
                        'name' => $student->name,
                        'surname' => $student->surname,
                        'student_email' => $student->student_email,
                        'full_name' => $student->name . ' ' . $student->surname,
                    ];
                })
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Error fetching students: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred while fetching students.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Display the specified student.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $student = Student::findOrFail($id);

            return response()->json([
                'data' => [
                    'id' => $student->id,
                    'name' => $student->name,
                    'surname' => $student->surname,
                    'student_email' => $student->student_email,
                    'alternative_email' => $student->alternative_email,
                    'phone_number' => $student->phone_number,
                    'user_id' => $student->user_id,
                    'study_level' => $student->study_level,
                    'state' => $student->state,
                    'region' => $student->region,
                    'city' => $student->city,
                    'postal_code' => $student->postal_code,
                    'street' => $student->street,
                    'house_number' => $student->house_number,
                    'created_at' => $student->created_at?->toIso8601String(),
                    'updated_at' => $student->updated_at?->toIso8601String(),
                ]
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Student not found.'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Error fetching student: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred while fetching the student.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}
