<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AppealResource;
use App\Models\Appeal;
use Illuminate\Http\Request;

class AppealController extends Controller
{
    public function index()
    {
        $appeals = Appeal::with('ticket')->get();

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

        return response()->json([
            'message' => 'Appeal created successfully',
            'appeal'  => new AppealResource($appeal),
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'is_accepted' => 'required|boolean',
        ]);

        $appeal = Appeal::find($id);
        $appeal->update($request->all());
        $appeal->save();

        return response()->json([
            'message' => 'Appeal updated successfully',
            'appeal'  => new AppealResource($appeal),
        ]);
    }

    public function destroy($id)
    {
        $appeal = Appeal::find($id);
        $appeal->delete();

        return response()->json(['message' => 'Appeal deleted successfully']);
    }
}
