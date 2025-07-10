<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PublicTicketResource extends JsonResource
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
            'violation' => new ViolationResource($this->whenLoaded('violation')),
            'investigator' => new UserResource($this->whenLoaded('investigator')),
            'color' => $this->color,
            'status' => $this->status,
            'deadline_confirmation' => $this->deadline_confirmation,
            'hearing_schedule' => new HearingScheduleResource($this->whenLoaded('hearingSchedule')),
            'vehicle' => new VehicleResource(optional($this->violation->vehicle)),
            'payment' => new TransactionResource($this->whenLoaded('transaction')),
            'activities' => ActivityResource::collection($this->whenLoaded('activities')),
            'location' => $this->violation->location,
            'appeal' => new AppealResource($this->whenLoaded('appeal')),
            'violation_type' => new ViolationTypeResource($this->whenLoaded('violation.violationType')),
            'transaction' => new TransactionResource($this->whenLoaded('transaction')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
