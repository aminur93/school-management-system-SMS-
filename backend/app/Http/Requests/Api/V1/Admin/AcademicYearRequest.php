<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AcademicYearRequest extends FormRequest
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
            'year_name' => [
                'required',
                'string',
                'max:20',
                'regex:/^\d{4}-\d{4}$/',
                'unique:academic_years,year_name',
            ],
            'start_date' => [
                'required',
                'date',
            ],
            'end_date' => [
                'required',
                'date',
                'after:start_date',
            ],
            'is_current' => [
                'sometimes',
                'boolean',
            ],
            'is_active' => [
                'sometimes',
                'boolean',
            ],
        ];
    }

    /**
     * Validation rules for UPDATE (PUT/PATCH)
     */
    protected function updateRules(): array
    {
        $academicYearId = $this->route('academic_year');

        return [
            'year_name' => [
                'sometimes',
                'string',
                'max:20',
                'regex:/^\d{4}-\d{4}$/',
                Rule::unique('academic_years', 'year_name')->ignore($academicYearId),
            ],
            'start_date' => [
                'sometimes',
                'date',
            ],
            'end_date' => [
                'sometimes',
                'date',
                'after:start_date',
            ],
            'is_current' => [
                'sometimes',
                'boolean',
            ],
            'is_active' => [
                'sometimes',
                'boolean',
            ],
        ];
    }

    /**
     * Custom validation messages
     */
    public function messages(): array
    {
        return [
            'year_name.regex' => 'Academic year format must be like 2024-2025.',
            'year_name.unique' => 'This academic year already exists.',
            'end_date.after' => 'End date must be after the start date.',
        ];
    }
}