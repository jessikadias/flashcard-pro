<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class AuthenticateApiToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check for Authorization header
        $authHeader = $request->header('Authorization');
        
        if (empty($authHeader) || !str_starts_with($authHeader, 'Bearer ')) {
            return $this->unauthorizedResponse('Missing or invalid authorization header');
        }

        // Extract token
        $token = substr($authHeader, 7);
        
        if (empty($token)) {
            return $this->unauthorizedResponse('Empty token provided');
        }

        // Find user by token (with caching for better performance)
        $user = Cache::remember("api_token:{$token}", 300, function () use ($token) {
            return User::where('api_token', $token)->first();
        });

        if (!$user) {
            return $this->unauthorizedResponse('Invalid token');
        }

        // Set authenticated user for the request
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        return $next($request);
    }

    /**
     * Return a consistent unauthorized response
     */
    private function unauthorizedResponse(string $message = 'Unauthorized'): Response
    {
        return response()->json([
            'error' => 'Unauthorized',
            'message' => $message,
        ], 401);
    }
}
