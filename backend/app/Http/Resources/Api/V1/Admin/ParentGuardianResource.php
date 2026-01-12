<?php

namespace App\Http\Resources\Api\V1\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ParentGuardianResource extends JsonResource
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
            'student_id' => new StudentResource($this->whenLoaded('student')),

            // Relation info
            'relation_type' => $this->relation_type,
            'is_primary' => (bool) $this->is_primary,

            // Personal Information
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'full_name' => trim(
                $this->first_name . ' ' .
                ($this->middle_name ?? '') . ' ' .
                $this->last_name
            ),
            'nid_number' => $this->nid_number,
            'date_of_birth' => $this->date_of_birth,

            // Contact Information
            'email' => $this->email,
            'phone' => $this->phone,
            'alternate_phone' => $this->alternate_phone,
            'address' => $this->address,

            // Professional Information
            'occupation' => $this->occupation,
            'organization' => $this->organization,
            'designation' => $this->designation,
            'annual_income' => $this->annual_income,
            'office_address' => $this->office_address,

            // Emergency Contact
            'is_emergency_contact' => (bool) $this->is_emergency_contact,

            // Documents
            'photo' => $this->photo,
            'photo_url' => $this->photo_url,
            'nid_photo' => $this->nid_photo,
            'nid_photo_url' => $this->nid_photo_url,

            'created_by' => new UserResource($this->whenLoaded('createdBy')),
        ];
    }
}