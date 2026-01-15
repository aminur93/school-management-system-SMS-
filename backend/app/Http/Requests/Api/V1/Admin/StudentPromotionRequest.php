<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StudentPromotionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        if ($this->isMethod('post')) {
            return $this->storeRules();
        }

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            return $this->updateRules();
        }

        return [];
    }

    /**
     * Store (POST) validation rules
     */
    protected function storeRules(): array
    {
        return [
            // Student
            'student_id' => ['required', 'integer', 'exists:students,id'],

            // From Details
            'from_academic_year_id' => ['required', 'integer', 'exists:academic_years,id'],
            'from_class_id'         => ['required', 'integer', 'exists:school_classes,id'],
            'from_section_id'       => ['required', 'integer', 'exists:sections,id'],

            // To Details
            'to_academic_year_id' => ['required', 'integer', 'exists:academic_years,id'],
            'to_class_id'         => ['required', 'integer', 'exists:school_classes,id'],
            'to_section_id'       => ['nullable', 'integer', 'exists:sections,id'],

            // Promotion Info
            'promotion_date' => ['required', 'date'],
            'promotion_type' => ['nullable', 'in:Promoted,Detained,Double_Promoted'],
            'result_status'  => ['nullable', 'in:Pass,Fail,Conditional'],

            // Academic Performance
            'total_marks'    => ['nullable', 'numeric', 'min:0'],
            'obtained_marks' => ['nullable', 'numeric', 'min:0'],
            'percentage'     => ['nullable', 'numeric', 'min:0', 'max:100'],
            'grade'          => ['nullable', 'string', 'max:5'],
            'gpa'            => ['nullable', 'numeric', 'min:0', 'max:5'],

            // Other
            'remarks'      => ['nullable', 'string'],
            'is_processed' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Update (PUT / PATCH) validation rules
     */
    protected function updateRules(): array
    {
        return [
            // Student
            'student_id' => ['sometimes', 'integer', 'exists:students,id'],

            // From Details
            'from_academic_year_id' => ['sometimes', 'integer', 'exists:academic_years,id'],
            'from_class_id'         => ['sometimes', 'integer', 'exists:classes,id'],
            'from_section_id'       => ['sometimes', 'integer', 'exists:sections,id'],

            // To Details
            'to_academic_year_id' => ['sometimes', 'integer', 'exists:academic_years,id'],
            'to_class_id'         => ['sometimes', 'integer', 'exists:classes,id'],
            'to_section_id'       => ['sometimes', 'nullable', 'integer', 'exists:sections,id'],

            // Promotion Info
            'promotion_date' => ['sometimes', 'date'],
            'promotion_type' => ['sometimes', 'in:Promoted,Detained,Double_Promoted'],
            'result_status'  => ['sometimes', 'in:Pass,Fail,Conditional'],

            // Academic Performance
            'total_marks'    => ['sometimes', 'numeric', 'min:0'],
            'obtained_marks' => ['sometimes', 'numeric', 'min:0'],
            'percentage'     => ['sometimes', 'numeric', 'min:0', 'max:100'],
            'grade'          => ['sometimes', 'string', 'max:5'],
            'gpa'            => ['sometimes', 'numeric', 'min:0', 'max:5'],

            // Other
            'remarks'      => ['sometimes', 'nullable', 'string'],
            'is_processed' => ['sometimes', 'boolean'],
        ];
    }
}