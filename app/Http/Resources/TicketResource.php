<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            // 'violation_id' => $this->violation_id,
            'violation' => new ViolationResource($this->whenLoaded('violation')),
            // 'investigator_id' => $this->investigator_id,
            'investigator' => new UserResource($this->whenLoaded('investigator')),
            'color' => $this->color,
            'status' => $this->status,
            'deadline_confirmation' => $this->deadline_confirmation,
            // 'hearing_schedule_id' => new HearingScheduleResource($this->whenLoaded('hearingSchedule')),
            'hearing_schedule' => new HearingScheduleResource($this->whenLoaded('hearingSchedule')),
            // 'notifications' => NotificationResource::collection($this->whenLoaded('notifications')),
            'vehicle' => new VehicleResource(optional($this->violation->vehicle)),
            'payment' => new TransactionResource($this->whenLoaded('transaction')),
            // 'activities' => ActivityResource::collection($this->whenLoaded('activities')),
            'activities' => $this->activities->sortByDesc('created_at')->values(),
            // 'notifications' => NotificationResource::collection($this->whenLoaded('notifications')),
            'notifications' => $this->notifications->groupBy('type'),
            'camera' => new CameraResource($this->whenLoaded('violation.camera')),
            'appeal' => new AppealResource($this->whenLoaded('appeal')),
            'violation_type' => new ViolationTypeResource($this->whenLoaded('violation.violationType')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
