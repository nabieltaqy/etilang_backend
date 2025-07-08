<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ViolationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);



        return [
            'id' => $this->id,
            'status' => $this->status,
            'camera_id' => $this->camera_id,
            'camera' => new CameraResource($this->whenLoaded('camera')),
            'location' => $this->location,
            'violation_evidence' => $this->violation_evidence,
            'number_evidence' => $this->number_evidence,
            'violation_type_id' => $this->violation_type_id,
            'violation_type' => new ViolationTypeResource($this->whenLoaded('violationType')),
            'number' => $this->number,
            'cancel_description' => $this->cancel_description,
            'vehicle_data' => $this->vehicle
                ? new VehicleResource($this->vehicle)
                : ['message' => 'Data kendaraan tidak ditemukan'],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
