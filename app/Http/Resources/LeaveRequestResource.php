<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeaveRequestResource extends JsonResource
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
            'user' => new UserResource($this->whenLoaded('user')),
            'leave_type' => new LeaveTypeResource($this->whenLoaded('leaveType')),
            'start_date' => $this->start_date->format('Y-m-d'),
            'end_date' => $this->end_date->format('Y-m-d'),
            'total_days' => (float) $this->total_days,
            'reason' => $this->reason,
            'contact_address' => $this->contact_address,
            'status' => $this->status,
            'status_label' => $this->getStatusLabel(),
            'attachment_url' => $this->attachment_path ? asset('storage/' . $this->attachment_path) : null,
            'approvals' => ApprovalResource::collection($this->whenLoaded('approvals')),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
            'cancelled_at' => $this->cancelled_at?->toIso8601String(),
        ];
    }

    /**
     * Get status label in Thai
     */
    private function getStatusLabel(): string
    {
        return match ($this->status) {
            'pending_supervisor' => 'รอหัวหน้างาน',
            'pending_head' => 'รอหัวหน้าแผนก',
            'pending_manager' => 'รอผู้บังคับบัญชา',
            'approved' => 'อนุมัติแล้ว',
            'rejected' => 'ถูกปฏิเสธ',
            'cancelled' => 'ยกเลิกแล้ว',
            default => $this->status,
        };
    }
}
