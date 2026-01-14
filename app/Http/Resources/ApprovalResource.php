<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApprovalResource extends JsonResource
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
            'approver' => $this->whenLoaded('approver', function () {
                return [
                    'id' => $this->approver->id,
                    'name' => $this->approver->name,
                    'position' => $this->approver->position,
                ];
            }),
            'step' => $this->step,
            'action' => $this->action,
            'action_label' => $this->getActionLabel(),
            'comment' => $this->comment,
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }

    /**
     * Get action label in Thai
     */
    private function getActionLabel(): string
    {
        return match ($this->action) {
            'approved' => 'อนุมัติ',
            'rejected' => 'ปฏิเสธ',
            'pending' => 'รอดำเนินการ',
            default => $this->action,
        };
    }
}
