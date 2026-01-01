<?php

namespace App\Http\Resources\Api\V1\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SectionResource extends JsonResource
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
            'school_class' => new SchoolClassResource($this->whenLoaded('schoolClass')),
            'school_class_id' => $this->school_class_id,
            'name' => $this->name,
            'capacity' => $this->capacity,
            'room_number' => $this->room_number,
            'is_active' => $this->is_active,
            'created_by' => $this->created_by,
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}