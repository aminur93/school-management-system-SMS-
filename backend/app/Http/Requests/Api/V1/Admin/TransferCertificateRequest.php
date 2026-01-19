<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TransferCertificateRequest extends FormRequest
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
     * Store (POST) rules
     */
    protected function storeRules(): array
    {
        return [
            // Student & Academic
            'student_id' => ['required', 'exists:students,id'],
            'tc_number' => ['required', 'string', 'max:50', 'unique:transfer_certificates,tc_number'],

            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'section_id' => ['nullable', 'exists:sections,id'],

            // Dates
            'issue_date' => ['required', 'date'],
            'leaving_date' => ['required', 'date', 'after_or_equal:issue_date'],

            // Reason
            'reason' => [
                'required',
                Rule::in(['Transfer', 'Migration', 'Personal', 'Financial', 'Other'])
            ],
            'reason_details' => ['nullable', 'string'],

            // Character & Conduct
            'persion_character' => ['nullable', Rule::in(['Good', 'Very Good', 'Excellent'])],
            'conduct' => ['nullable', 'string', 'max:50'],

            // Academic Performance
            'last_exam_passed' => ['nullable', 'string', 'max:100'],
            'last_exam_result' => ['nullable', 'string', 'max:50'],
            'total_working_days' => ['nullable', 'integer', 'min:0'],
            'total_present_days' => ['nullable', 'integer', 'min:0'],
            'attendance_percentage' => ['nullable', 'numeric', 'between:0,100'],

            // New School
            'new_school_name' => ['nullable', 'string', 'max:200'],
            'new_school_address' => ['nullable', 'string'],

            // Document
            'tc_document' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:4096'],

            // Status
            'status' => [
                'nullable',
                Rule::in(['Requested', 'Approved', 'Issued', 'Cancelled'])
            ],

            // Remarks
            'remarks' => ['nullable', 'string'],
        ];
    }

    /**
     * Update (PUT / PATCH) rules
     */
    protected function updateRules(): array
    {
        return [
            // Student & Academic
            'student_id' => ['sometimes', 'exists:students,id'],
            'tc_number' => [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('transfer_certificates', 'tc_number')->ignore($this->route('id'))
            ],

            'academic_year_id' => ['sometimes', 'exists:academic_years,id'],
            'school_class_id' => ['sometimes', 'exists:school_classes,id'],
            'section_id' => ['sometimes', 'nullable', 'exists:sections,id'],

            // Dates
            'issue_date' => ['sometimes', 'date'],
            'leaving_date' => ['sometimes', 'date'],

            // Reason
            'reason' => [
                'sometimes',
                Rule::in(['Transfer', 'Migration', 'Personal', 'Financial', 'Other'])
            ],
            'reason_details' => ['sometimes', 'nullable', 'string'],

            // Character & Conduct
            'persion_character' => ['sometimes', Rule::in(['Good', 'Very Good', 'Excellent'])],
            'conduct' => ['sometimes', 'nullable', 'string', 'max:50'],

            // Academic Performance
            'last_exam_passed' => ['sometimes', 'nullable', 'string', 'max:100'],
            'last_exam_result' => ['sometimes', 'nullable', 'string', 'max:50'],
            'total_working_days' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'total_present_days' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'attendance_percentage' => ['sometimes', 'nullable', 'numeric', 'between:0,100'],

            // New School
            'new_school_name' => ['sometimes', 'nullable', 'string', 'max:200'],
            'new_school_address' => ['sometimes', 'nullable', 'string'],

            // Document
            'tc_document' => ['sometimes', 'nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:4096'],

            // Status
            'status' => [
                'sometimes',
                Rule::in(['Requested', 'Approved', 'Issued', 'Cancelled'])
            ],

            // Remarks
            'remarks' => ['sometimes', 'nullable', 'string'],
        ];
    }
}