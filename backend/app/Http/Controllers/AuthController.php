<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use App\Models\Student;
use App\Models\Company;
use Laravel\Passport\Passport;

class AuthController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'remember_me' => 'sometimes|boolean', // Optional remember me flag
        ]);

        // Find user by email
        $user = User::where('email', $credentials['email'])->first();

        // Check if user exists and password matches
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Determine token expiration based on remember_me
        // If remember_me is true, use personal access token with longer expiration (6 months)
        // Otherwise, use regular token (1 hour) - can be refreshed via refresh endpoint
        $rememberMe = $request->input('remember_me', false);
        
        // Create token - Passport will use the expiration time from AppServiceProvider
        // Personal access tokens always use personalAccessTokensExpireIn setting
        $tokenResult = $user->createToken('authToken');
        $token = $tokenResult->accessToken;
        
        // Calculate expiration time
        $expiresIn = $rememberMe 
            ? 60 * 60 * 24 * 180  // 6 months in seconds (for remember me)
            : 60 * 60;              // 1 hour in seconds (regular session)

        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => $expiresIn,
            'remember_me' => $rememberMe,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role, // priamo string z DB
                'role_display_name' => ucfirst($user->role), // napr. 'Admin'
                'permissions' => $this->getPermissionsForRole($user->role)
            ]
        ]);
    }

    /**
     * Refresh access token
     */
    public function refresh(Request $request): JsonResponse
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        // Revoke old token
        $oldToken = $user->token();
        if ($oldToken) {
            $oldToken->revoke();
        }

        // Create new token
        $tokenResult = $user->createToken('authToken');
        $newToken = $tokenResult->accessToken;

        return response()->json([
            'token' => $newToken,
            'token_type' => 'Bearer',
            'expires_in' => 60 * 60, // 1 hour
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'role_display_name' => ucfirst($user->role),
                'permissions' => $this->getPermissionsForRole($user->role)
            ]
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();
        
        if ($user) {
            $token = $user->token();
            
            if ($token) {
                // Revoke associated refresh tokens using Passport abstraction
                Passport::refreshToken()
                    ->newQuery()
                    ->where('access_token_id', $token->id)
                    ->update(['revoked' => true]);
                
                // Revoke the access token (marks as revoked in database)
                // This ensures Passport will reject the token on future requests
                $token->revoke();
            }
        }

        return response()->json([
            'message' => 'Logged out successfully.',
        ]);
    }

    public function register(Request $request)
    {
        // Base validation rules
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:student,company',
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

        // Additional validation rules for companies
        if ($request->input('role') === 'company') {
            $rules = array_merge($rules, [
                'company_name' => 'required|string|max:255',
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
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        // Ak ide o študenta
        if ($user->role === 'student') {
            Student::create([
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

        // Ak ide o firmu
        if ($user->role === 'company') {
            Company::create([
                'user_id' => $user->id,
                'name' => $request->input('company_name'),
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
     * Optional – jednoduché mapovanie rolí na povolenia
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
