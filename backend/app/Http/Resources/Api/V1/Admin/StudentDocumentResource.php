<?php

namespace App\Http\Resources\Api\V1\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentDocumentResource extends JsonResource
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

            'student' => new StudentResource($this->whenLoaded('student')),

            'document_type' => $this->document_type,
            'document_name' => $this->document_name,
            'document_path' => $this->document_path,
            'document_url' => $this->document_path,

            'uploader' => new UserResource($this->whenLoaded('uploader')),
            'uploaded_at' => $this->uploaded_at,

            'is_verified' => $this->is_verified,
            'verifier' => new UserResource($this->whenLoaded('verifier')),
            'verified_at' => $this->verified_at,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}