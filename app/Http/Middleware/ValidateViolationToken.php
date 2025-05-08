<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateViolationToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->user()?->currentAccessToken();
        $id = $request->route('id');  // Pastikan id ini sesuai dengan ID pelanggaran yang sedang diproses
    
        // Pastikan token ada dan nama token sesuai dengan 'violation-{id}'
        if (! $token || $token->name !== 'violation-' . $id) {
            return response()->json(['message' => 'Token tidak valid untuk pelanggaran ini'], 403);
        }
    
        // Cek apakah token sudah kedaluwarsa
        if ($token->expires_at && now()->greaterThan($token->expires_at)) {
            return response()->json(['message' => 'Token expired'], 403);
        }
    
        return $next($request);
    }
}
