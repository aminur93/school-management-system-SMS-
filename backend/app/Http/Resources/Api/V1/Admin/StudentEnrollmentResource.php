<?php

namespace App\Http\Resources\Api\V1\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentEnrollmentResource extends JsonResource
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

            // Foreign keys
            'student_id' => new StudentResource($this->whenLoaded('student')),
            'academic_year_id' => new AcademicYearResource($this->whenLoaded('academicYear')),
            'school_class_id' => new SchoolClassResource($this->whenLoaded('schoolClass')),
            'section_id' => new SectionResource($this->whenLoaded('section')),

            // Academic info
            'roll_number' => $this->roll_number,
            'enrollment_date' => $this->enrollment_date,
            'enrollment_status' => $this->enrollment_status,

            // Fees
            'total_fees' => $this->total_fees,
            'discount_amount' => $this->discount_amount,
            'scholarship_amount' => $this->scholarship_amount,
            'net_fees' => $this->net_fees,

            // Others
            'remarks' => $this->remarks,
            'created_by' => new UserResource($this->whenLoaded('createdBy')),
        ];
    }
}