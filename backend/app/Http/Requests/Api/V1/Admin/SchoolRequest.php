<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SchoolRequest extends FormRequest
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
     * Validation rules for store request
     */
    protected function storeRules(): array
    {
        return [
            'name' => ['nullable', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:schools,code'],
            'registration_number' => ['required', 'string', 'max:100', 'unique:schools,registration_number'],
            'address' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'status' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Validation rules for update request
     */
    protected function updateRules(): array
    {
        // Get school id from route
        $schoolId = $this->route('school');

        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'code' => [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('schools', 'code')->ignore($schoolId),
            ],
            'registration_number' => [
                'sometimes',
                'string',
                'max:100',
                Rule::unique('schools', 'registration_number')->ignore($schoolId),
            ],
            'address' => ['sometimes', 'string'],
            'description' => ['nullable', 'string'],
            'status' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Custom validation messages
     */
    public function messages(): array
    {
        return [
            'code.required' => 'School code is required.',
            'code.unique' => 'This school code is already in use.',
            'registration_number.required' => 'Registration number is required.',
            'registration_number.unique' => 'This registration number is already in use.',
            'address.required' => 'School address is required.',
            'status.boolean' => 'Status must be true or false.',
        ];
    }
}