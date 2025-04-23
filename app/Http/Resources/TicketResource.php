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
            'violation_id' => $this->violation_id,
            'investigator_id' => $this->investigator_id,
            'color' => $this->color,
            'status' => $this->status,
            'deadline_confirmation' => $this->deadline_confirmation,
            'hearing_schedule_id' => $this->hearing_schedule_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
