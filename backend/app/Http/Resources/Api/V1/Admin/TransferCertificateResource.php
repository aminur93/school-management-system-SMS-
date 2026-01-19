<?php

namespace App\Http\Resources\Api\V1\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransferCertificateResource extends JsonResource
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

            // Identifiers
            'tc_number' => $this->tc_number,
            'status' => $this->status,

            // Student
            'student' => new StudentResource($this->whenLoaded('student')),

            // Academic info
            'academic_year' => new AcademicYearResource($this->whenLoaded('academicYear')),
            'school_class' => new SchoolClassResource($this->whenLoaded('schoolClass')),
            'section' => new SectionResource($this->whenLoaded('section')),

            // TC Dates
            'issue_date' => optional($this->issue_date)->toDateString(),
            'leaving_date' => optional($this->leaving_date)->toDateString(),

            // Reason
            'reason' => $this->reason,
            'reason_details' => $this->reason_details,

            // Character & Conduct
            'persion_character' => $this->persion_character,
            'conduct' => $this->conduct,

            // Academic Performance
            'last_exam_passed' => $this->last_exam_passed,
            'last_exam_result' => $this->last_exam_result,
            'total_working_days' => $this->total_working_days,
            'total_present_days' => $this->total_present_days,
            'attendance_percentage' => $this->attendance_percentage,

            // New School Info
            'new_school_name' => $this->new_school_name,
            'new_school_address' => $this->new_school_address,

            // Document
            'tc_document_path' => $this->tc_document_path,
            'tc_document_url' => $this->tc_document_path
                ? asset('storage/' . $this->tc_document_path)
                : null,

            // Approvals
            'requested_by' => new UserResource($this->whenLoaded('requestedBy')),
            'approved_by' => new UserResource($this->whenLoaded('approvedBy')),
            'approved_at' => optional($this->approved_at)->toDateTimeString(),
            'issued_by' => new UserResource($this->whenLoaded('issuedBy')),

            // Remarks
            'remarks' => $this->remarks,

            'createdBy' => new UserResource($this->whenLoaded('createdBy')),
            'updatedBy' => new UserResource($this->whenLoaded('updatedBy')),

            // Audit
            'created_at' => optional($this->created_at)->toDateTimeString(),
            'updated_at' => optional($this->updated_at)->toDateTimeString(),
        ];
    }
}