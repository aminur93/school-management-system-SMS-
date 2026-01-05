<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StudentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
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
     * Validation rules for store (POST)
     */
    protected function storeRules(): array
    {
        return [
            'student_id' => ['required', 'string', 'max:20', 'unique:students,student_id'],
            'school_id' => ['required', 'integer', 'exists:schools,id'],
            'medium_id' => ['required', 'integer', 'exists:mediums,id'],
            'current_class_id' => ['nullable', 'integer', 'exists:classes,id'],
            'current_section_id' => ['nullable', 'integer', 'exists:sections,id'],
            'current_academic_year_id' => ['nullable', 'integer', 'exists:academic_years,id'],

            'admission_number' => ['required', 'string', 'max:50', 'unique:students,admission_number'],
            'admission_date' => ['required', 'date'],

            // Personal Information
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'date_of_birth' => ['required', 'date'],
            'gender' => ['required', Rule::in(['Male', 'Female', 'Other'])],
            'blood_group' => ['nullable', 'string', 'max:5'],
            'religion' => ['nullable', 'string', 'max:50'],
            'nationality' => ['nullable', 'string', 'max:50'],

            // Contact Information
            'email' => ['nullable', 'email', 'max:100'],
            'phone' => ['nullable', 'string', 'max:20'],
            'present_address' => ['required', 'string'],
            'permanent_address' => ['required', 'string'],

            // Academic
            'roll_number' => ['nullable', 'string', 'max:20'],

            // Previous School
            'previous_school_name' => ['nullable', 'string', 'max:200'],
            'previous_class' => ['nullable', 'string', 'max:50'],

            // Status
            'status' => [Rule::in(['Active', 'Transferred', 'Dropout', 'TC_Issued', 'Completed'])],

            // Profile
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'birth_certificate_no' => ['nullable', 'string', 'max:50'],
        ];
    }

    /**
     * Validation rules for update (PUT / PATCH)
     */
    protected function updateRules(): array
    {
        $studentId = $this->route('student');

        return [
            'student_id' => [
                'sometimes',
                'string',
                'max:20',
                Rule::unique('students', 'student_id')->ignore($studentId),
            ],

            'school_id' => ['sometimes', 'integer', 'exists:schools,id'],
            'medium_id' => ['sometimes', 'integer', 'exists:mediums,id'],
            'current_class_id' => ['nullable', 'integer', 'exists:classes,id'],
            'current_section_id' => ['nullable', 'integer', 'exists:sections,id'],
            'current_academic_year_id' => ['nullable', 'integer', 'exists:academic_years,id'],

            'admission_number' => [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('students', 'admission_number')->ignore($studentId),
            ],
            'admission_date' => ['sometimes', 'date'],

            // Personal Information
            'first_name' => ['sometimes', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['sometimes', 'string', 'max:100'],
            'date_of_birth' => ['sometimes', 'date'],
            'gender' => ['sometimes', Rule::in(['Male', 'Female', 'Other'])],
            'blood_group' => ['nullable', 'string', 'max:5'],
            'religion' => ['nullable', 'string', 'max:50'],
            'nationality' => ['nullable', 'string', 'max:50'],

            // Contact
            'email' => ['nullable', 'email', 'max:100'],
            'phone' => ['nullable', 'string', 'max:20'],
            'present_address' => ['sometimes', 'string'],
            'permanent_address' => ['sometimes', 'string'],

            // Academic
            'roll_number' => ['nullable', 'string', 'max:20'],

            // Previous School
            'previous_school_name' => ['nullable', 'string', 'max:200'],
            'previous_class' => ['nullable', 'string', 'max:50'],

            // Status
            'status' => [Rule::in(['Active', 'Transferred', 'Dropout', 'TC_Issued', 'Completed'])],

            // Profile
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'birth_certificate_no' => ['nullable', 'string', 'max:50'],
        ];
    }
}