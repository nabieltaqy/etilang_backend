<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use App\Http\Resources\TicketResource;

class TicketController extends Controller
{
    public function index()
    {
        // Logic to retrieve and return all tickets
        $tickets = Ticket::with(['violation', 'user', 'hearingSchedule', 'vehicle', 'notifications', 'activities'])->paginate(10);

        return new TicketResource($tickets);
    }

    public function show($id)
    {
        // Logic to retrieve and return a specific ticket by ID
        $ticket = Ticket::with(['violation', 'user', 'hearingSchedule', 'vehicle', 'notifications', 'activities'])->find($id);
        if (!$ticket) {
            return response()->json(['message' => 'Ticket not found'], 404);
        }
        return new TicketResource($ticket);
    }

    public function store(Request $request)
    {
        // Logic to create a new ticket
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

    public function destroy($id)
    {
        // Logic to delete a ticket
        $ticket = Ticket::find($id);
        if (!$ticket) {
            return response()->json(['message' => 'Ticket not found'], 404);
        }
        if ($ticket->delete()) {
            return response()->json(['message' => 'Ticket deleted'], 200);
        } else {
            return response()->json(['message' => 'Failed to delete ticket'], 500);
        }
    }
}
