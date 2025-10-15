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

        if (Auth::attempt($credentials)) {
            /** @var User $user */
            $user = Auth::user();
            
            // Load role relationship
            $user->load('role');
            
            $token = $user->createToken('authToken')->accessToken;

            return response()->json([
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role ? $user->role->name : null,
                    'role_display_name' => $user->role ? $user->role->display_name : null,
                    'permissions' => $user->role ? $user->role->permissions : []
                ]
            ]);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Handle user registration.
     */
  
}
