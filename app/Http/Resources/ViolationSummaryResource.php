<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ViolationSummaryResource extends JsonResource
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
            'number' => $this->number,
            'location' => $this->location,
            'violation_type' => $this->violationType->name,
            'status' => $this->status,
            'evidence' => $this->number_evidence,
            'created_at' => $this->created_at,
            'ticket_id' => $this->ticket?->id,
            // 'ticket_id' => $this->whenLoaded(new TicketResource($this)),
        ];
    }
}
