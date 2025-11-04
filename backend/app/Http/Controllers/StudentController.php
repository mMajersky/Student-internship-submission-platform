<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends BaseApiController
{
    /**
     * Display a listing of all students.
     * Used for dropdown selections in forms.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return $this->executeWithExceptionHandling(function () {
            $students = Student::select('id', 'name', 'surname', 'student_email')
                ->orderBy('surname')
                ->orderBy('name')
                ->get();

            return $this->respondWithCollection($students, function ($student) {
                return [
                    'id' => $student->id,
                    'name' => $student->name,
                    'surname' => $student->surname,
                    'student_email' => $student->student_email,
                    'full_name' => $student->name . ' ' . $student->surname,
                ];
            });
        }, 'fetching students');
    }

    /**
     * Display the specified student.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        return $this->executeWithExceptionHandling(function () use ($id) {
            $student = Student::findOrFail($id);

            return $this->respondWithResource($student, function ($student) {
                return [
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
                ];
            });
        }, 'fetching student');
    }
}
