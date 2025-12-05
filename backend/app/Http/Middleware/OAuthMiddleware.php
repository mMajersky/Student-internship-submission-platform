<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class OAuthMiddleware
{
    /**
     * Handle an incoming request for OAuth authentication (user or client).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only allow client_credentials grants (client authenticated, no user)
        // Reject password grants (client + user) and user JWTs to prevent token reuse
        if (Auth::guard('api')->client() !== null && Auth::guard('api')->user() === null) {
            return $next($request);
        }

        // If client_credentials authentication fails, return 401
        return response()->json(['error' => 'Client credentials required'], 401);
    }
}
