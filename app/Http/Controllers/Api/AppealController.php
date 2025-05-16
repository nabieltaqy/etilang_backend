<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AppealResource;
use App\Models\Appeal;
use App\Models\Ticket;
use App\Models\Activity;
use Illuminate\Http\Request;

class AppealController extends Controller
{
    public function index()
    {
        $appeals = Appeal::with('ticket')->paginate(10);

        return AppealResource::collection($appeals);
    }

    public function show($id)
    {
        $appeal = Appeal::with('ticket')->find($id);

        return new AppealResource($appeal);
    }

    public function store(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required|exists:tickets,id',
            'argument'  => 'required',
            'evidence'  => 'required|image|mimes:jpeg,png,jpg',
        ]);

        //
        //  if ($request->hasFile('violation_evidence') && $request->hasFile('number_evidence')) {
        //             // Store the uploaded files
        //             $violation_relative_path = $request->file('violation_evidence')->store('violance_evidences', 'public');
        //             $number_relative_path = $request->file('number_evidence')->store('number_evidences', 'public');

        //             // $vehicle_id = Vehicle::where('number', $request->number)->first()->id;
        //             $camera_id  = Camera::where('stream_key', $request->stream_key)->first()->id;

        //             $violation = Violation::create([
        //                 'number' => $request->number,
        //                 'camera_id'  => $camera_id,
        //                 'violation_evidence'   => $violation_relative_path,
        //                 'number_evidence' => $number_relative_path,
        //             ]);
        //
        if ($request->hasFile('evidence')) {
            // Store the uploaded files
            $evidence_relative_path = $request->file('evidence')->store('appeal_evidences', 'public');
            $request->merge(['evidence' => $evidence_relative_path]);

            $ticket = Ticket::find($request->ticket_id);

            if ($ticket->appeal) {
                return response()->json([
                    'message' => 'Ticket already has an appeal',
                ], 422);
            }else{
                $appeal = Appeal::create([
                    'ticket_id' => $request->ticket_id,
                    'argument'  => $request->argument,
                    'evidence'  => $evidence_relative_path,
                ]);
            }
        }

        Activity::create([
            'ticket_id'   => $request->ticket_id,
            'name'        => 'Pengajuan Banding',
            'description' => 'Pengajuan Banding Diajukan',
        ]);

        return response()->json([
            'message' => 'Appeal created successfully',
            'appeal'  => new AppealResource($appeal),
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'is_accepted' => 'required|boolean',
            'note'       => 'required',
        ]);

        $ticket = Ticket::with(['appeal'])->find($id);

        $appeal = $ticket->appeal;
        $appeal->update($request->all());
        $appeal->save();

        if ($request->is_accepted == false) {
            Activity::create([
                'ticket_id'   => $id,
                'name'        => 'Banding Ditolak',
                'description' => 'Pengajuan Banding Ditolak',
            ]);
        } else {
            Activity::create([
                'ticket_id'   => $id,
                'name'        => 'Banding Diterima',
                'description' => 'Pengajuan Banding Diterima',
            ]);
            $ticket->update(['status' => 'Banding Diterima']);
            $ticket->save();
        }

        return response()->json([
            'message' => 'Appeal updated successfully',
            'appeal'  => new AppealResource($appeal),
        ]);
    }
}
