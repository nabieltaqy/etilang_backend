<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\HearingSchedule;
use Illuminate\Http\Request;
use App\Http\Resources\TicketResource;
use App\Http\Resources\TicketSummaryResource;
use Carbon\Carbon;
use App\Models\Activity;

class TicketController extends Controller
{
    public function index()
    {
        // Logic to retrieve and return all tickets
        $tickets = Ticket::with(['violation', 'violation.camera', 'investigator', 'violation.vehicle', 'transaction'])
            ->orderBy('status')
            ->orderByDesc('created_at')
            ->paginate(10);

        return TicketSummaryResource::collection($tickets);
    }

    public function show($id)
    {
        // Logic to retrieve and return a specific ticket by ID
        $ticket = Ticket::with(['violation.vehicle', 'violation.camera', 'investigator', 'hearingSchedule', 'notifications', 'activities', 'transaction', 'appeal'])->find($id);
        if (!$ticket) {
            return response()->json(['message' => 'Ticket not found'], 404);
        }
        return new TicketResource($ticket);
    }

    public function update(Request $request, $id)
    {
        // Logic to update an existing ticket
        $ticket = Ticket::find($id);
        if (!$ticket) {
            return response()->json(['message' => 'Ticket not found'], 404);
        }
        // Validate and update the ticket
        $ticket->update($request->all());
        return new TicketResource($ticket);
    }
}
