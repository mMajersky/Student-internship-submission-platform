<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\JsonResponse;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        // Check if user exists
        $user = User::where('email', $credentials['email'])->first();

        // Prevent email enumeration: always return generic error if user doesn't exist or password is wrong
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'error' => 'Invalid credentials',
                'error_type' => 'invalid_credentials'
            ], 401);
        }

        // Check if email is verified
        if (!$user->hasVerifiedEmail()) {
            return response()->json([
                'error' => 'Email not verified. Please check your inbox for verification link.',
                'error_type' => 'email_not_verified'
            ], 403);
        }

        // All checks passed, create token
        $token = $user->createToken('authToken')->accessToken;

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'role_display_name' => ucfirst($user->role),
                'permissions' => $this->getPermissionsForRole($user->role),
                'email_verified_at' => $user->email_verified_at,
            ]
        ]);
    }

    /**
     * Register a new user
     */
    public function register(Request $request): JsonResponse
    {
        // Base validation rules
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*\d).+$/'
            ],
            'role' => 'required|in:student,company',
        ];

        // Custom error messages
        $messages = [
            'password.regex' => 'Heslo musí obsahovať aspoň jedno malé písmeno a aspoň jednu číslicu',
            'password.min' => 'Heslo musí mať minimálne 8 znakov',
            'password.confirmed' => 'Heslá sa nezhodujú',
            'email.unique' => 'Tento email už je registrovaný',
        ];

        // Additional validation rules for students
        // Note: We check $request->input() before validation, but use $validated after
        $role = $request->input('role');
        
        if ($role === 'student') {
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

            $messages = array_merge($messages, [
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

        $validated = $request->validate($rules, $messages);

        // Create user and related profile in a transaction to ensure data consistency
        $user = DB::transaction(function () use ($validated) {
        // Create user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        // Create related Student profile
        if ($user->role === 'student') {
            \App\Models\Student::create([
                'user_id' => $user->id,
                'name' => $validated['name'],
                'surname' => $validated['surname'],
                'student_email' => $validated['email'],
                'alternative_email' => $validated['alternative_email'] ?? null,
                'phone_number' => $validated['phone_number'] ?? null,
                'study_level' => $validated['study_level'],
                'study_field' => $validated['study_field'],
                'state' => $validated['state'],
                'region' => $validated['region'],
                'city' => $validated['city'],
                'postal_code' => $validated['postal_code'],
                'street' => $validated['street'],
                'house_number' => $validated['house_number'],
            ]);
        }

        // Create related Company profile
        if ($user->role === 'company') {
            \App\Models\Company::create([
                'user_id' => $user->id,
                'name' => $validated['company_name'],
                'state' => $validated['state'],
                'region' => $validated['region'],
                'city' => $validated['city'],
                'postal_code' => $validated['postal_code'],
                'street' => $validated['street'],
                'house_number' => $validated['house_number'],
            ]);
        }

            return $user;
        });

        // Send email verification
        $user->sendEmailVerificationNotification();

        return response()->json([
            'message' => 'Registrácia prebehla úspešne. Overovací email bol odoslaný na vašu adresu.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ], 201);
    }

    /**
     * Handle logout and revoke the current access token.
     */
    public function logout(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        // Revoke all non-revoked tokens for this user
        // Note: We revoke all tokens rather than just the current one to ensure
        // complete logout across all devices. Passport may cache token validation
        // briefly, but tokens are marked as revoked in the database.
        DB::table('oauth_access_tokens')
            ->where('user_id', $user->id)
            ->where('revoked', false)
            ->update(['revoked' => true]);

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    /**
     * Verify email address using signed URL
     */
    public function verifyEmail(Request $request, $id, $hash): JsonResponse
    {
        $user = User::findOrFail($id);

        // Verify the signed URL - prevents forgery
        if (!\Illuminate\Support\Facades\URL::hasValidSignature($request)) {
            return response()->json([
                'error' => 'Invalid or expired verification link'
            ], 400);
        }

        // Additional check: verify the hash matches the user's email
        if (!hash_equals($hash, sha1($user->getEmailForVerification()))) {
            return response()->json([
                'error' => 'Invalid verification link'
            ], 400);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email already verified'
            ]);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return response()->json([
            'message' => 'Email successfully verified'
        ]);
    }

    /**
     * Resend email verification link
     * Works for both authenticated and unauthenticated users
     */
    public function resendVerification(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // Try to get user from auth first, then by email
        $user = $request->user() ?? User::where('email', $request->email)->first();

        // Always return success message to prevent email enumeration
        // Only send email if user exists and is not verified
        if ($user && !$user->hasVerifiedEmail()) {
        $user->sendEmailVerificationNotification();
        }

        return response()->json([
            'message' => 'If the email address exists and is not verified, a verification link has been sent.'
        ]);
    }

    /**
     * Send password reset link
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // Always return success to prevent email enumeration
        // Laravel's Password facade handles the actual sending
        try {
        $status = Password::sendResetLink(
            $request->only('email')
        );
        } catch (\Exception $e) {
            // If sending fails (e.g., mail not configured in tests), still return success
            // to prevent email enumeration
        }

        // Return generic success message regardless of whether email exists
        return response()->json([
            'message' => 'If the email address exists, a password reset link has been sent.'
        ]);
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*\d).+$/'
            ],
        ], [
            'password.regex' => 'Heslo musí obsahovať aspoň jedno malé písmeno a aspoň jednu číslicu',
            'password.min' => 'Heslo musí mať minimálne 8 znakov',
            'password.confirmed' => 'Heslá sa nezhodujú',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                // Revoke all existing access tokens for security
                // This ensures that if a token was compromised, it becomes invalid after password reset
                // Use update with proper where clause to ensure tokens are revoked
                DB::table('oauth_access_tokens')
                    ->where('user_id', $user->id)
                    ->update(['revoked' => true]);
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'message' => 'Password successfully reset'
            ]);
        }

        return response()->json([
            'error' => 'Invalid or expired reset token'
        ], 400);
    }

    /**
     * Get permissions for a specific role (returns Slovak translations)
     */
    private function getPermissionsForRole(string $role): array
    {
        $permissions = [
            'admin' => [
                'manage_users',
                'manage_internships',
                'manage_announcements'
            ],
            'garant' => [
                'manage_internships',
                'manage_announcements'
            ],
            'company' => [
                'review_interns'
            ],
            'student' => [
                'create_internships'
            ],
        ];

        return $permissions[$role] ?? [];
    }
}

