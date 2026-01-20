<?php

namespace App\Http\Resources\Api\V1\Admin;

use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentDropoutResource extends JsonResource
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

            // Relations
            'student' => new StudentResource($this->whenLoaded('student')),
            'academic_year' => new AcademicYearResource($this->whenLoaded('academicYear')),
            'school_class' => new SchoolClassResource($this->whenLoaded('schoolClass')),
            'section' => new SectionResource($this->whenLoaded('section')),
            'createdBy' => new SectionResource($this->whenLoaded('createdBy')),
            'updatedBy' => new SectionResource($this->whenLoaded('updatedBy')),

            // Dropout Details
            'dropout_date' => $this->dropout_date,
            'reason' => $this->reason,
            'reason_details' => $this->reason_details,

            // Academic Status
            'last_attendance_date' => $this->last_attendance_date,
            'total_working_days' => $this->total_working_days,
            'total_present_days' => $this->total_present_days,
            'attendance_percentage' => $this->attendance_percentage,

            // Fees Status
            'fees_due' => $this->fees_due,
            'fees_cleared' => $this->fees_cleared,

            // Follow-up
            'contacted_for_return' => $this->contacted_for_return,
            'contact_date' => $this->contact_date,
            'willing_to_return' => $this->willing_to_return,

            'remarks' => $this->remarks,

            // Audit
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}