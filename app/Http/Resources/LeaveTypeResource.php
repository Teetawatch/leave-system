<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeaveTypeResource extends JsonResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'max_days_per_year' => $this->max_days_per_year,
            'requires_advance_notice' => (bool) $this->requires_advance_notice,
            'advance_notice_days' => $this->advance_notice_days,
            'allows_retroactive' => (bool) $this->allows_retroactive,
        ];
    }
}
