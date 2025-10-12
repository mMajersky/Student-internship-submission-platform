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
            /** @var User $user */ //
            $user = Auth::user();
            $token = $user->createToken('authToken')->accessToken;

            return response()->json([
                'token' => $token,
                'user' => $user
            ]);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
