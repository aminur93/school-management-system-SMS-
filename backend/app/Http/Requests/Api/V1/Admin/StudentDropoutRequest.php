<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StudentDropoutRequest extends FormRequest
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

     protected function storeRules(): array
    {
        return [
            'student_id' => ['required', 'exists:students,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'section_id' => ['nullable', 'exists:sections,id'],

            'dropout_date' => ['required', 'date'],
            'reason' => [
                'required',
                Rule::in([
                    'Financial',
                    'Personal',
                    'Health',
                    'Relocation',
                    'Poor_Performance',
                    'Other'
                ])
            ],
            'reason_details' => ['nullable', 'string'],

            'last_attendance_date' => ['nullable', 'date'],
            'total_working_days' => ['nullable', 'integer', 'min:0'],
            'total_present_days' => ['nullable', 'integer', 'min:0'],
            'attendance_percentage' => ['nullable', 'numeric', 'between:0,100'],

            'fees_due' => ['nullable', 'numeric', 'min:0'],
            'fees_cleared' => ['boolean'],

            'contacted_for_return' => ['boolean'],
            'contact_date' => ['nullable', 'date'],
            'willing_to_return' => ['nullable', 'boolean'],

            'remarks' => ['nullable', 'string'],
        ];
    }

    protected function updateRules(): array
    {
        return [
            'student_id' => ['sometimes', 'exists:students,id'],
            'academic_year_id' => ['sometimes', 'exists:academic_years,id'],
            'school_class_id' => ['sometimes', 'exists:school_classes,id'],
            'section_id' => ['sometimes', 'nullable', 'exists:sections,id'],

            'dropout_date' => ['sometimes', 'date'],
            'reason' => [
                'sometimes',
                Rule::in([
                    'Financial',
                    'Personal',
                    'Health',
                    'Relocation',
                    'Poor_Performance',
                    'Other'
                ])
            ],
            'reason_details' => ['sometimes', 'nullable', 'string'],

            'last_attendance_date' => ['sometimes', 'nullable', 'date'],
            'total_working_days' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'total_present_days' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'attendance_percentage' => ['sometimes', 'nullable', 'numeric', 'between:0,100'],

            'fees_due' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'fees_cleared' => ['sometimes', 'boolean'],

            'contacted_for_return' => ['sometimes', 'boolean'],
            'contact_date' => ['sometimes', 'nullable', 'date'],
            'willing_to_return' => ['sometimes', 'nullable', 'boolean'],

            'remarks' => ['sometimes', 'nullable', 'string'],
        ];
    }
}