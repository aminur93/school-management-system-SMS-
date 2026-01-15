<?php

namespace App\Http\Resources\Api\V1\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentPromotionResource extends JsonResource
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

            // Student
            'student' => new StudentResource(
                $this->whenLoaded('student')
            ),

            // From Details
            'from_academic_year' => new AcademicYearResource(
                $this->whenLoaded('fromAcademicYear')
            ),
            'from_class' => new SchoolClassResource(
                $this->whenLoaded('fromClass')
            ),
            'from_section' => new SectionResource(
                $this->whenLoaded('fromSection')
            ),

            // To Details
            'to_academic_year' => new AcademicYearResource(
                $this->whenLoaded('toAcademicYear')
            ),
            'to_class' => new SchoolClassResource(
                $this->whenLoaded('toClass')
            ),
            'to_section' => new SectionResource(
                $this->whenLoaded('toSection')
            ),

            // Promotion Info
            'promotion_date' => optional($this->promotion_date)->format('Y-m-d'),
            'promotion_type' => $this->promotion_type,
            'result_status'  => $this->result_status,

            // Academic Performance
            'total_marks'    => $this->total_marks,
            'obtained_marks' => $this->obtained_marks,
            'percentage'     => $this->percentage,
            'grade'          => $this->grade,
            'gpa'            => $this->gpa,

            // Meta
            'remarks'      => $this->remarks,
            'is_processed' => (bool) $this->is_processed,

            // Audit
            'created_at' => optional($this->created_at)->toDateTimeString(),
            'created_by' => new UserResource($this->whenLoaded('createdBy')),
        ];
    }
}