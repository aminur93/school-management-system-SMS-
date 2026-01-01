<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SectionRequest extends FormRequest
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
            'school_class_id' => [
                'required',
                'integer',
                'exists:school_classes,id',
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'capacity' => [
                'required',
                'integer',
                'min:1',
            ],

            'room_number' => [
                'required',
                'string',
                'max:100',
            ],

            'is_active' => [
                'required',
                'boolean',
            ],

            'created_by' => [
                'required',
                'integer',
                'exists:users,id',
            ],
        ];
    }

    /**
     * Update validation rules
     */
    protected function updateRules(): array
    {
        // route parameter: section
        $sectionId = $this->route('section');

        return [
            'school_class_id' => [
                'sometimes',
                'integer',
                'exists:school_classes,id',
            ],

            'name' => [
                'sometimes',
                'string',
                'max:255',
            ],

            'capacity' => [
                'sometimes',
                'integer',
                'min:1',
            ],

            'room_number' => [
                'sometimes',
                'string',
                'max:100',
            ],

            'is_active' => [
                'sometimes',
                'boolean',
            ],

            'created_by' => [
                'sometimes',
                'integer',
                'exists:users,id',
            ],
        ];
    }

    /**
     * Custom validation messages
     */
    public function messages(): array
    {
        return [
            'school_class_id.required' => 'School class is required.',
            'school_class_id.exists' => 'Selected school class is invalid.',
            'name.required' => 'Section name is required.',
            'name.string' => 'Section name must be a string.',
            'name.max' => 'Section name may not be greater than 255 characters.',
            'capacity.required' => 'Capacity is required.',
            'capacity.integer' => 'Capacity must be an integer.',
            'capacity.min' => 'Capacity must be at least 1.',
            'room_number.required' => 'Room number is required.',
            'room_number.string' => 'Room number must be a string.',
            'room_number.max' => 'Room number may not be greater than 100 characters.',
            'is_active.boolean' => 'Is active must be true or false.',
            'created_by.required' => 'Created by is required.',
            'created_by.integer' => 'Created by must be an integer.',
            'created_by.exists' => 'Selected user is invalid.',
        ];
    }
}