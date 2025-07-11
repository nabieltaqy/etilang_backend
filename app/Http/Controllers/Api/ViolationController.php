<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TicketResource;
use App\Http\Resources\ViolationResource;
use App\Http\Resources\ViolationSummaryResource;
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
        $violations = Violation::with(['violationType', 'camera', 'ticket', 'vehicle'])
            ->orderBy('status')
            ->orderByDesc('created_at')
            ->paginate(10);
        return ViolationSummaryResource::collection($violations);
    }

    public function show($id)
    {
        $violation = Violation::with(['violationType', 'camera', 'ticket', 'vehicle'])->find($id);
        return new ViolationResource($violation);
    }

    public function store(Request $request)
{
    $request->validate([
        'number'     => 'required',
        'stream_key' => 'nullable|exists:cameras,stream_key',
        'location'   => 'nullable|string',
        'violation_evidence'   => 'required|image|mimes:jpeg,png,jpg',
        'number_evidence'      => 'required|image|mimes:jpeg,png,jpg',
    ]);

    if (!$request->filled('stream_key') && !$request->filled('location')) {
        return response()->json(['message' => 'Either stream_key or location must be provided'], 422);
    }

    if ($request->hasFile('violation_evidence') && $request->hasFile('number_evidence')) {
        // Simpan file
        $violation_relative_path = $request->file('violation_evidence')->store('violance_evidences', 'public');
        $number_relative_path = $request->file('number_evidence')->store('number_evidences', 'public');

        // Ambil lokasi
        $location = null;
        $camera_id = null;

        if ($request->filled('stream_key')) {
            $camera = Camera::where('stream_key', $request->stream_key)->first();
            $camera_id = $camera->id;
            $location = $camera->location;
        } elseif ($request->filled('location')) {
            $location = $request->location;
        }

        // Simpan pelanggaran
        $violation = Violation::create([
            'number'             => $request->number,
            'camera_id'          => $camera_id, // nullable jika pakai location manual
            'location'           => $location,
            'violation_evidence' => $violation_relative_path,
            'number_evidence'    => $number_relative_path,
        ]);

        return response()->json([
            'message'   => 'Violation created',
            'violation' => new ViolationResource($violation),
        ], 201);
    }

    return response()->json(['message' => 'No evidence file uploaded'], 400);
}

    public function updateNumber(Request $request, $id)
    {
        $request->validate([
            'number'     => 'required'
        ]);

        $violation = Violation::find($id);
        if (!$violation) {
            return response()->json(['message' => 'Violation not found'], 404);
        }
        $violation->number = $request->number;
        $violation->save();
        return response()->json([
            'message'   => 'Violation updated',
            'violation' => new ViolationResource($violation),
        ], 200);
    }

public function verifyViolation(Request $request, $id)
{
    // Cek apakah tiket sudah dibuat untuk pelanggaran ini
    $ticket = Ticket::where('violation_id', $id)->first();
    if ($ticket) {
        return response()->json(['message' => 'Violation already has a ticket'], 400);
    }

    // Cek apakah pelanggaran ditemukan
    $violation = Violation::find($id);
    if (!$violation) {
        return response()->json(['message' => 'Violation not found'], 404);
    }

    // Cek apakah kendaraan terdaftar
    $vehicle = Vehicle::where('number', $violation->number)->first();
    if (!$vehicle) {
        return response()->json(['message' => 'Vehicle not found. Please check the vehicle number!'], 404);
    }

    // Jika semua validasi lolos, baru ubah status pelanggaran
    $violation->status = 'Tilang';
    $violation->save();

    // Buat tiket
    $user = auth()->user();
    $ticket = Ticket::create([
        'violation_id'          => $violation->id,
        'investigator_id'       => $user->id,
        'status'                => 'Tilang',
        'vehicle_id'            => $vehicle->id,
        'deadline_confirmation' => now()->addDays(3),
    ]);

    // Buat aktivitas
    if ($ticket) {
        Activity::create([
            'ticket_id'   => $ticket->id,
            'name'        => 'Tilang',
            'description' => 'Kendaraan terverifikasi melanggar lalu lintas',
        ]);
    }

    // Revoke token
    $request->user()->currentAccessToken()->delete();

    return response()->json([
        'message' => 'Ticket created and token revoked',
        'ticket'  => new TicketResource($ticket),
    ], 201);
}


    public function cancelViolation(Request  $request, $id)
    {
        $request->validate([
            'cancel_description' => 'required'
        ]);

        $violation         = Violation::find($id);
        $violation->status = 'Batal';
        $violation->cancel_description = $request->cancel_description;
        $violation->save();

        // revoke the token
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message'   => 'Violation updated and token revoked',
            'violation' => new ViolationResource($violation),
        ], 200);
    }

    public function createTokenForVerification(Request $request, $id)
    {
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

    public function revokeToken(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Token revoked successfully.'], 200);
    }
}
