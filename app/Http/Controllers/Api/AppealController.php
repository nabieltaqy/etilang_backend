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

    // public function store(Request $request) //public access another controller
    // {
    // } 

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|enum:Accepted,Rejected',
            'note'       => 'required',
        ]);

        $ticket = Ticket::with(['appeal'])->find($id);

        $appeal = $ticket->appeal;
        $appeal->update($request->all());
        $appeal->save();

        if ($request->status == 'Rejected') {
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
