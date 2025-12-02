<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        // Manually authenticate user (don't use session guard)
        $user = User::where('email', $credentials['email'])->first();

        if (!$user) {
            return response()->json([
                'message' => 'User with this email does not exist.'
            ], 404);
        }

        // STEP 2 — check password
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Incorrect password.'
            ], 401);
        }
        // Create token with 24-hour expiration for security balance
        $token = $user->createToken('authToken')->accessToken;

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role, // priamo string z DB
                'role_display_name' => ucfirst($user->role), // napr. 'Admin'
                'permissions' => $this->getPermissionsForRole($user->role)
            ]
        ], 200);
    }
    public function register(Request $request)
    {
        // Base validation rules
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:student',
        ];

        // Additional validation rules for students
        if ($request->input('role') === 'student') {
            $allowedDomains = config('services.university.allowed_email_domains', []);
            $domainPattern = implode('|', array_map('preg_quote', $allowedDomains));

            $rules = array_merge($rules, [
                'surname' => 'required|string|max:100',
                'email' => [
                    'required',
                    'email',
                    'unique:users,email',
                    'regex:/^[a-zA-Z0-9._%+-]+@(' . $domainPattern . ')$/i'
                ],
                'alternative_email' => 'nullable|email|different:email|max:100',
                'phone_number' => 'nullable|string|max:20',
                'study_level' => 'required|string|max:50',
                'study_field' => 'required|string|max:100',
                'state' => 'required|string|max:100',
                'region' => 'required|string|max:100',
                'city' => 'required|string|max:100',
                'postal_code' => 'required|string|max:20',
                'street' => 'required|string|max:100',
                'house_number' => 'required|string|max:20',
            ]);
        }



        // Custom error messages
        $messages = [
            'email.regex' => 'Email musí byť univerzitný email (@student.ukf.sk)',
            'surname.required' => 'Priezvisko je povinné',
            'study_level.required' => 'Stupeň štúdia je povinný',
            'study_field.required' => 'Študijný odbor je povinný',
            'alternative_email.different' => 'Alternatívny email musí byť odlišný od univerzitného emailu',
            'state.required' => 'Štát je povinný',
            'region.required' => 'Región je povinný',
            'city.required' => 'Mesto je povinné',
            'postal_code.required' => 'PSČ je povinné',
            'street.required' => 'Ulica je povinná',
            'house_number.required' => 'Číslo domu je povinné',
        ];

        $validated = $request->validate($rules, $messages);

        $user = User::create([
            'name' => $validated['name'],
            'surname' => $validated['surname'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        // Ak ide o študenta
        if ($user->role === 'student') {
            \App\Models\Student::create([
                'user_id' => $user->id,
                'name' => $request->input('name'),
                'surname' => $request->input('surname'),
                'student_email' => $request->input('email'),
                'alternative_email' => $request->input('alternative_email'),
                'phone_number' => $request->input('phone_number'),
                'study_level' => $request->input('study_level'),
                'study_field' => $request->input('study_field'),
                'state' => $request->input('state'),
                'region' => $request->input('region'),
                'city' => $request->input('city'),
                'postal_code' => $request->input('postal_code'),
                'street' => $request->input('street'),
                'house_number' => $request->input('house_number'),
            ]);
        }



        return response()->json([
            'message' => 'Registrácia prebehla úspešne.',
            'user' => $user,
        ], 201);
    }



    /**
     * Optionálne – jednoduché mapovanie rolí na povolenia
     */
    private function getPermissionsForRole(string $role): array
    {
        $permissions = [
            'admin' => ['manage_users', 'manage_internships', 'manage_announcements'],
            'garant' => ['manage_internships', 'manage_announcements'],
            'company' => ['review_interns'],
            'student' => ['create_internships'],
        ];

        return $permissions[$role] ?? [];
    }
}
