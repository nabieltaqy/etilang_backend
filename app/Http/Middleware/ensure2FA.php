<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ensure2FA
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the user has a token named 'auth_token'
        if (!$request->user()->tokens->where('name', 'auth_token')->count()) {
            return response()->json(['message' => '2FA verification required'], 403);
        }

        return $next($request);
    }
}
