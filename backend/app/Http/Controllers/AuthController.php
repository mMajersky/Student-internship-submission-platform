<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            /** @var User $user */
            $user = Auth::user();

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
            ]);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:student,company',
        ]);

        // Generovanie aktivačného tokenu (len infraštruktúra, neukladá sa do DB)
        $activationToken = Str::random(60);
        Log::info("Generated activation token for {$validated['email']}: {$activationToken}");

        $user = User::create([
            'name' => $validated['name'],
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
            \App\Models\Company::create([
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
        // Príprava pre budúci email systém (napr. aktivácia účtu)
        // Mail::to($user->email)->send(new ActivationMail($activationToken));
        return response()->json([
            'message' => 'Registrácia prebehla úspešne. Email aktivačný systém bude pridaný neskôr.',
            'user' => $user,
            // Len pre testovanie v local režime – zobrazí token, ktorý by sa odosielal mailom
            'debug_activation_token' => config('app.env') === 'local' ? $activationToken : null,
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
