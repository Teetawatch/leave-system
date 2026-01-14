<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'email' => $this->email,
            'role' => $this->role,
            'role_label' => $this->getRoleLabel(),
            'department' => $this->department,
            'position' => $this->position,
            'rank' => $this->rank,
            'avatar_url' => $this->avatar ? asset('storage/' . $this->avatar) : null,
            'supervisor' => $this->whenLoaded('supervisor', function () {
                return [
                    'id' => $this->supervisor->id,
                    'name' => $this->supervisor->name,
                ];
            }),
            'manager' => $this->whenLoaded('manager', function () {
                return [
                    'id' => $this->manager->id,
                    'name' => $this->manager->name,
                ];
            }),
        ];
    }

    /**
     * Get role label in Thai
     */
    private function getRoleLabel(): string
    {
        return match ($this->role) {
            'employee' => 'ข้าราชการ',
            'department_head' => 'หัวหน้าแผนก',
            'deputy_director' => 'รองผู้อำนวยการ',
            'director' => 'ผู้อำนวยการ',
            'admin' => 'ผู้ดูแลระบบ',
            default => $this->role,
        };
    }
}
