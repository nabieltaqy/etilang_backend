<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TicketResource;
use App\Http\Resources\ViolationResource;
use App\Models\Activity;
use App\Models\Camera;
use App\Models\Ticket;
use App\Models\Vehicle;
use App\Models\Violation;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken; // Import the PersonalAccessToken model

class ViolationController extends Controller
{
    public function index()
    {
        $violations = Violation::with(['violationType', 'camera', 'ticket'])->paginate(10);
        return ViolationResource::collection($violations);
    }

    public function show($id)
    {
        $violation = Violation::with(['violationType', 'camera', 'ticket'])->find($id);
        return new ViolationResource($violation);
    }

    public function store(Request $request)
    {
        $request->validate([
            // 'number'     => 'required|exists:vehicles,number',
            'stream_key' => 'required|exists:cameras,stream_key',
            'evidence'   => 'required|image|mimes:jpeg,png,jpg',
        ]);

        if ($request->hasFile('evidence')) {
            $relative_path = $request->file('evidence')->store('violance_evidences', 'public');

            // $vehicle_id = Vehicle::where('number', $request->number)->first()->id;
            $camera_id  = Camera::where('stream_key', $request->stream_key)->first()->id;

            $violation = Violation::create([
                'number' => $request->number,
                'camera_id'  => $camera_id,
                'evidence'   => $relative_path,
            ]);

            // return new ViolationResource($violation);
            return response()->json([
                'message'   => 'Violation created',
                'violation' => new ViolationResource($violation),
            ], 201);
        }

        return response()->json(['message' => 'No evidence file uploaded'], 400);
    }

    public function update(Request $request, $id)
    { // masih belum kelar perlu apakah update terus create ticket dengan mencari nomor kendaran
        $request->validate([
            'status' => 'required|in:Terdeteksi,Tilang,Batal',
            'number' => 'required|exists:vehicles,number',
            'violation_id' => 'required|exists:violations,id',
        ]);

        // id checking if violation id exist on tickets
        $ticket = Ticket::where('violation_id', $id)->first();
        if ($ticket) {
            return response()->json(['message' => 'Violation already have ticket'], 400);
        }

        $violation         = Violation::find($request->violation_id);
        $violation->status = $request->status;
        $violation->save();

        // get user login
        $user = auth()->user();

        // create ticket if status is Tilang
        if ($request->status == 'Tilang') {

            // get vehicle id from number
            $vehicle_id = Vehicle::where('number', $request->number)->first()->id;

            $ticket = Ticket::create([
                'violation_id'          => $violation->id,
                'investigator_id'       => $user->id,
                'status'                => 'Tilang',
                'vehicle_id'            => $vehicle_id,
                'deadline_confirmation' => now()->addDays(3),
            ]);

            return response()->json([
                'message' => 'Ticket created',
                'ticket'  => new TicketResource($ticket),
            ], 201);

            // create activity
            Activity::create([
                'ticket_id'   => $ticket->id,
                'name'        => 'Tilang',
                'description' => 'Kendaraan terverifikasi melanggar lalu lintas',
            ]);
        }

        // return new ViolationResource($violation);
        return response()->json([
            'message'   => 'Violation updated',
            'violation' => new ViolationResource($violation),
        ], 200);
    }

    public function createTokenForVerification(Request $request, $id)
    {
        // Ambil pelanggaran berdasarkan ID
        $violation = Violation::findOrFail($id);

        // Cek apakah ada token yang sudah dibuat untuk pelanggaran ini
        $existingToken = PersonalAccessToken::where('name', 'violation-' . $id)->first();

        // Jika ada token yang sudah ada, cek apakah token tersebut kadaluarsa
        if ($existingToken) {
            // Jika token masih valid
            if ($existingToken->expires_at && now()->lessThan($existingToken->expires_at)) {
                return response()->json(['message' => 'Token already exists and is still valid.'], 403);
            }

            // Jika token sudah kedaluwarsa, hapus token yang lama dan buat yang baru
            $existingToken->delete();
        }

        // Buat token baru dengan nama unik dan ability khusus
        $token = $request->user()->createToken('violation-' . $id, ['verify-violation'], now()->addMinutes(10));

        return response()->json([
            'message' => 'Token created successfully.',
            'token' => $token->plainTextToken
        ]);
    }
}
