<?php

namespace App\Http\Resources\Api\V1\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
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
            'student_id' => $this->student_id,

            // School & Academic Mapping
            'school_id' => $this->school_id,
            'school' => new SchoolResource($this->whenLoaded('school')),
            'medium_id' => $this->medium_id,
            'medium' => new MediumResource($this->whenLoaded('medium')),
            'current_class_id' => $this->current_class_id,
            'class' => new SchoolClassResource($this->whenLoaded('schoolClass')),
            'current_section_id' => $this->current_section_id,
            'section' => new SectionResource($this->whenLoaded('section')),
            'current_academic_year_id' => $this->current_academic_year_id,
            'academic' => new AcademicYearResource($this->whenLoaded('academic')),

            // Admission Info
            'admission_number' => $this->admission_number,
            'admission_date' => $this->admission_date,

            // Personal Information
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'date_of_birth' => $this->date_of_birth,
            'gender' => $this->gender,
            'blood_group' => $this->blood_group,
            'religion' => $this->religion,
            'nationality' => $this->nationality,

            // Contact Information
            'email' => $this->email,
            'phone' => $this->phone,
            'present_address' => $this->present_address,
            'permanent_address' => $this->permanent_address,

            // Academic Information
            'roll_number' => $this->roll_number,

            // Previous School Information
            'previous_school_name' => $this->previous_school_name,
            'previous_class' => $this->previous_class,

            // Status
            'status' => $this->status,

            // Profile
            'profile_photo' => $this->profile_photo,
            'profile_photo_url' => $this->profile_photo_url,
            'birth_certificate_no' => $this->birth_certificate_no,
            'birth_certificate_no_url' => $this->birth_certificate_no_url,

            // Audit
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ];
    }
}