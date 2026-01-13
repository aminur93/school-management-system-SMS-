<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StudentEnrollmentRequest extends FormRequest
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
                Rule::unique('student_enrollments')
                    ->where(fn ($q) =>
                        $q->where('academic_year_id', $this->academic_year_id)
                    ),
            ],

            'academic_year_id' => [
                'required',
                'integer',
                'exists:academic_years,id',
            ],

            'school_class_id' => [
                'required',
                'integer',
                'exists:school_classes,id',
            ],

            'section_id' => [
                'required',
                'integer',
                'exists:sections,id',
            ],

            'roll_number' => [
                'nullable',
                'string',
                'max:20',
            ],

            'enrollment_date' => [
                'required',
                'date',
            ],

            'enrollment_status' => [
                'sometimes',
                Rule::in([
                    'Enrolled',
                    'Promoted',
                    'Detained',
                    'Transferred',
                    'Completed',
                ]),
            ],

            'total_fees' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'discount_amount' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'scholarship_amount' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'net_fees' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'remarks' => [
                'nullable',
                'string',
            ],
        ];
    }

    /**
     * Validation rules for UPDATE (PUT / PATCH)
     */
    protected function updateRules(): array
    {
        $enrollmentId = $this->route('student_enrollment');

        return [
            'student_id' => [
                'sometimes',
                'integer',
                'exists:students,id',
                Rule::unique('student_enrollments')
                    ->ignore($enrollmentId)
                    ->where(fn ($q) =>
                        $q->where('academic_year_id', $this->academic_year_id)
                    ),
            ],

            'academic_year_id' => [
                'sometimes',
                'integer',
                'exists:academic_years,id',
            ],

            'school_class_id' => [
                'sometimes',
                'integer',
                'exists:school_classes,id',
            ],

            'section_id' => [
                'sometimes',
                'integer',
                'exists:sections,id',
            ],

            'roll_number' => [
                'nullable',
                'string',
                'max:20',
            ],

            'enrollment_date' => [
                'sometimes',
                'date',
            ],

            'enrollment_status' => [
                'sometimes',
                Rule::in([
                    'Enrolled',
                    'Promoted',
                    'Detained',
                    'Transferred',
                    'Completed',
                ]),
            ],

            'total_fees' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'discount_amount' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'scholarship_amount' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'net_fees' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'remarks' => [
                'nullable',
                'string',
            ],
        ];
    }
}