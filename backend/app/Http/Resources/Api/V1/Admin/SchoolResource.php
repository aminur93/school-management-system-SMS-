<?php

namespace App\Http\Resources\Api\V1\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolResource extends JsonResource
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
            'code' => $this->code,
            'registration_number' => $this->registration_number,
            'address' => $this->address,
            'description' => $this->description,
            'status' => $this->status,
            'created_by' => $this->created_by,
            'createdBy' => new UserResource($this->whenLoaded('createdBy')),
        ];
    }
}