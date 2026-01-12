<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ParentGuardianRequest extends FormRequest
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
     * Validation rules for STORE (POST)
     */
    protected function storeRules(): array
    {
        return [
            'student_id' => [
                'required',
                'integer',
                'exists:students,id',
            ],

            'relation_type' => [
                'required',
                Rule::in(['Father', 'Mother', 'Guardian', 'Other']),
            ],

            'is_primary' => [
                'sometimes',
                'boolean',
            ],

            // Personal Information
            'first_name' => [
                'required',
                'string',
                'max:100',
            ],
            'middle_name' => [
                'nullable',
                'string',
                'max:100',
            ],
            'last_name' => [
                'required',
                'string',
                'max:100',
            ],
            'nid_number' => [
                'nullable',
                'string',
                'max:20',
            ],
            'date_of_birth' => [
                'nullable',
                'date',
                'before:today',
            ],

            // Contact Information
            'email' => [
                'nullable',
                'email',
                'max:100',
            ],
            'phone' => [
                'required',
                'string',
                'max:20',
            ],
            'alternate_phone' => [
                'nullable',
                'string',
                'max:20',
            ],
            'address' => [
                'nullable',
                'string',
            ],

            // Professional Information
            'occupation' => [
                'nullable',
                'string',
                'max:100',
            ],
            'organization' => [
                'nullable',
                'string',
                'max:200',
            ],
            'designation' => [
                'nullable',
                'string',
                'max:100',
            ],
            'annual_income' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            'office_address' => [
                'nullable',
                'string',
            ],

            // Emergency Contact
            'is_emergency_contact' => [
                'sometimes',
                'boolean',
            ],

            // Documents
            'photo' => [
                'nullable',
                'string',
                'max:255',
            ],
            'nid_photo' => [
                'nullable',
                'string',
                'max:255',
            ],
        ];
    }

     /**
     * Validation rules for UPDATE (PUT/PATCH)
     */
    protected function updateRules(): array
    {
        return [
            'student_id' => [
                'sometimes',
                'integer',
                'exists:students,id',
            ],

            'relation_type' => [
                'sometimes',
                Rule::in(['Father', 'Mother', 'Guardian', 'Other']),
            ],

            'is_primary' => [
                'sometimes',
                'boolean',
            ],

            // Personal Information
            'first_name' => [
                'sometimes',
                'string',
                'max:100',
            ],
            'middle_name' => [
                'nullable',
                'string',
                'max:100',
            ],
            'last_name' => [
                'sometimes',
                'string',
                'max:100',
            ],
            'nid_number' => [
                'nullable',
                'string',
                'max:20',
            ],
            'date_of_birth' => [
                'nullable',
                'date',
                'before:today',
            ],

            // Contact Information
            'email' => [
                'nullable',
                'email',
                'max:100',
            ],
            'phone' => [
                'sometimes',
                'string',
                'max:20',
            ],
            'alternate_phone' => [
                'nullable',
                'string',
                'max:20',
            ],
            'address' => [
                'nullable',
                'string',
            ],

            // Professional Information
            'occupation' => [
                'nullable',
                'string',
                'max:100',
            ],
            'organization' => [
                'nullable',
                'string',
                'max:200',
            ],
            'designation' => [
                'nullable',
                'string',
                'max:100',
            ],
            'annual_income' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            'office_address' => [
                'nullable',
                'string',
            ],

            // Emergency Contact
            'is_emergency_contact' => [
                'sometimes',
                'boolean',
            ],

            // Documents
            'photo' => [
                'nullable',
                'string',
                'max:255',
            ],
            'nid_photo' => [
                'nullable',
                'string',
                'max:255',
            ],
        ];
    }

    /**
     * Custom validation messages
     */
    public function messages(): array
    {
        return [
            'student_id.required' => 'Student is required.',
            'student_id.exists' => 'Selected student does not exist.',
            'relation_type.in' => 'Relation type must be Father, Mother, Guardian or Other.',
            'date_of_birth.before' => 'Date of birth must be a past date.',
            'phone.required' => 'Phone number is required.',
            'email.email' => 'Please provide a valid email address.',
        ];
    }
}