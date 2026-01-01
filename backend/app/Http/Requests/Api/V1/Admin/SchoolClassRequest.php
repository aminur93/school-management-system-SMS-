<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SchoolClassRequest extends FormRequest
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
     * Store validation rules
     */
    protected function storeRules(): array
    {
        return [
            'medium_id' => [
                'required',
                'integer',
                'exists:mediums,id',
            ],

            'name' => [
                'nullable',
                'string',
                'max:100',
            ],

            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('school_classes', 'code')
                    ->where('medium_id', $this->medium_id),
            ],

            'order_number' => [
                'nullable',
                'integer',
                'min:1',
            ],

            'is_active' => [
                'required',
                'boolean',
            ],
        ];
    }

    /**
     * Update validation rules
     */
    protected function updateRules(): array
    {
        // route parameter: school_class
        $classId = $this->route('school_class');

        return [
            'medium_id' => [
                'sometimes',
                'integer',
                'exists:mediums,id',
            ],

            'name' => [
                'sometimes',
                'string',
                'max:100',
            ],

            'code' => [
                'sometimes',
                'string',
                'max:50',
            ],

            'order_number' => [
                'nullable',
                'integer',
                'min:1',
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
            'medium_id.required' => 'Medium is required.',
            'medium_id.exists' => 'Selected medium is invalid.',
            'code.unique' => 'This class code already exists for this medium.',
            'is_active.boolean' => 'Is active must be true or false.',
            'order_number.integer' => 'Order number must be an integer.',
        ];
    }
}