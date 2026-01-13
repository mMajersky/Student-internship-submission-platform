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
        // Only allow client_credentials grants
        // Reject personal access tokens and password grants
        $client = Auth::guard('api')->client();

        if ($client !== null && in_array('client_credentials', $client->grant_types)) {
            return $next($request);
        }

        // If client_credentials authentication fails, return 401
        return response()->json(['error' => 'Client credentials required'], 401);
    }
}
