<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketSummaryResource extends JsonResource
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
            'number' => $this->violation?->number,
            'violation_type' => $this->violation?->violationType?->name,
            'location' => $this->violation?->location,
            'investigator' => $this->investigator?->name,
            'status' => $this->status,
            'number_evidence' => $this->violation?->number_evidence,
            'created_at' => $this->created_at,
        ];
    }
}
