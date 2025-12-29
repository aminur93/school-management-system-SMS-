<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MediumRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                'max:100',
            ],
            'code' => [
                'required',
                'string',
                'max:50',
                'unique:mediums,code',
            ],
            'description' => [
                'nullable',
                'string',
                'max:255',
            ],
            'is_active' => [
                'required',
                'boolean',
            ],
        ];
    }

    /**
     * Validation rules for update request
     */
    protected function updateRules(): array
    {
        // Route parameter name: medium
        $mediumId = $this->route('medium');

        return [
            'name' => [
                'required',
                'string',
                'max:100',
            ],
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('mediums', 'code')->ignore($mediumId),
            ],
            'description' => [
                'nullable',
                'string',
                'max:255',
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
            'code.unique' => 'This medium code already exists.',
            'is_active.boolean' => 'Is active must be true or false.',
        ];
    }
}