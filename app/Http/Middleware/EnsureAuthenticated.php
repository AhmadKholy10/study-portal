<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use Nette\InvalidStateException;
use Symfony\Component\HttpFoundation\Response;

class EnsureAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['message' => 'Unauthorized: No token provided'], 401);
        }

        $accessToken = PersonalAccessToken::findToken($token);

        if (!$accessToken || $accessToken->tokenable_type !== 'App\Models\User') {
            return response()->json(['message' => 'Unauthorized: Invalid token'], 401);
        }

        $user = $accessToken->tokenable;

        if (!$user) {
            return response()->json(['message' => 'Unauthorized: User not found'], 401);
        }

        // Attach the token to the user object
        $user->currentAccessToken = $accessToken;
        // Set the authenticated user for the current request
        Auth::setUser($user);
        $request->setUserResolver(function () use ($user) {
            return $user;
        });


        return $next($request);
    }
}
