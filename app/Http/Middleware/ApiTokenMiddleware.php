<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token || !User::where('api_token', $token)->exists()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->setUserResolver(function () use ($token) {
            return User::where('api_token', $token)->first();
        });

        return $next($request);
    }
}
