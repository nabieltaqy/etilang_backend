<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Ticket;

class CheckViolationTicket
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
         $violationId = $request->route('id');

        $ticketExists = Ticket::where('violation_id', $violationId)->exists();

        if ($ticketExists) {
            return response()->json([
                'message' => 'This Violations Already Have Ticket.'
            ], 403);
        }

        return $next($request);
    }
}
