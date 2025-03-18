<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\VehicleResource;
use Illuminate\Http\Request;
use App\Models\Vehicle;

class VehicleController extends Controller
{
    public function index() {
        $vehicles = Vehicle::all();
    
    return VehicleResource::collection($vehicles);
    }

    public function show($id) {
        $vehicle = Vehicle::find($id);
        return new VehicleResource($vehicle);
    }

    
}
