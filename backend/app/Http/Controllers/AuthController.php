<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
