<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CameraResource;
use Illuminate\Http\Request;
use App\Models\Camera;

class CameraController extends Controller
{
    public function index(Request $request)
    {
        $cameras = Camera::all();

        return CameraResource::collection($cameras);
    }

    public function show($id)
    {
        $camera = Camera::findOrFail($id);

        return new CameraResource($camera);
    }

    public function store(Request $request)
    {
        $request->validate([
            'location' => 'required|string|max:255',
            'stream_key' => 'required|string|max:255',
            'server_url' => 'required|url',
        ]);

        $camera = Camera::create($request->all());

        return response()->json([
            'message' => 'Camera created successfully',
            'camera' => new CameraResource($camera),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $camera = Camera::findOrFail($id);

        $request->validate([
            'location' => 'required|string|max:255',
            'stream_key' => 'required|string|max:255',
            'server_url' => 'required|url',
            'status' => 'required|in:active,inactive',
        ]);

        $camera->update($request->all());

        return response()->json([
            'message' => 'Camera updated successfully',
            'camera' => new CameraResource($camera),
        ], 200);
    }

    public function destroy($id)
    {
        $camera = Camera::findOrFail($id);
        $camera->delete();

        return response()->json(['message' => 'Camera deleted successfully'], 204);
    }
}
