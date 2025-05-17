<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\HearingSchedule;
use App\Http\Resources\TicketResource;
use Carbon\Carbon;
use App\Models\Activity;
use App\Models\Appeal;
use App\Http\Resources\AppealResource;

class PublicAccessController extends Controller
{
    // menampilkan tiket sesuai id dan nomor polisi
    public function showTicket($id, $number)
    {
        $ticket = Ticket::with(['violation.vehicle', 'violation.camera', 'violation.violationType'])
            ->where('id', $id)
            ->whereHas('violation.vehicle', function ($query) use ($number) {
                $query->where('number', $number);
            })
            ->first();

        if (!$ticket) {
            return response()->json(['message' => 'Ticket not found or plate number mismatch'], 404);
        }

        return new TicketResource($ticket);
    }

    //Pengajuan banding
    public function appealStore(Request $request)
    {
        $request->validate([
            'ticket_id'   => 'required|exists:tickets,id',
            'argument'    => 'required',
            'evidence'    => 'required|image|mimes:jpeg,png,jpg',
        ]);

        if ($request->hasFile('evidence')) {
            $relative_path = $request->file('evidence')->store('appeal_evidences', 'public');
        }

        $appeal = Appeal::create([
            'ticket_id'   => $request->ticket_id,
            'argument'    => $request->argument,
            'evidence'    => $relative_path,
        ]);

        $ticket = Ticket::find($request->ticket_id);
        $ticket->update(['status' => 'Pengajuan Banding']);
        $ticket->save();

        Activity::create([
            'ticket_id'   => $request->ticket_id,
            'name'        => 'Pengajuan Banding',
            'description' => 'Pengajuan Banding',
        ]);

        return response()->json([
            'message' => 'Appeal created successfully',
            'appeal'  => new AppealResource($appeal),
        ]);
    }

    // Violator memilih sidang
    public function attendHearing($id)
    {
        // Ambil tiket
        $ticket = Ticket::with(['hearingSchedule'])->findOrFail($id);

        // Cek apakah sudah terdaftar dalam jadwal sidang
        if ($ticket->hearing_schedule_id) {
            return response()->json([
                'message' => 'This ticket is already assigned to a hearing schedule',
                'schedule' => $ticket->hearingSchedule,
            ], 200);
        }

        // Ambil hearing schedule terdekat dari hari ini + 7 hari
        $schedule = HearingSchedule::where('date', '>=', Carbon::now()->addDays(7))
            ->orderBy('date', 'asc')
            ->first();

        // Jika tidak ada jadwal ditemukan
        if (!$schedule) {
            return response()->json(['message' => 'No hearing schedule available after 7 days'], 404);
        }

        Activity::create([
            'ticket_id'   => $id,
            'name'        => 'Pilih Sidang',
            'description' => 'Pilih Sidang pada ' . $schedule->date,
        ]);

        // Update ticket dengan hearing_schedule_id
        $ticket->hearing_schedule_id = $schedule->id;
        $ticket->save();

        return response()->json([
            'message' => 'Hearing schedule selected successfully',
            'schedule' => $schedule,
        ]);
    }
}
