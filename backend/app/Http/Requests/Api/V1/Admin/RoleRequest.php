<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoleRequest extends FormRequest
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
    public function storeRules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:100',
                'unique:roles,name',
            ],

            'level' => [
                'required',
                'integer',
                'min:1',
                'max:100',
            ],

            'permissions' => [
                'required',
                'array',
                'min:1',
            ],

            'permissions.*' => [
                'integer',
                'exists:permissions,id',
            ],
        ];
    }

    /**
     * Validation rules for update request
     */
    public function updateRules(): array
    {
        // Route model binding or ID
        $roleId = $this->route('role');

        return [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('roles', 'name')->ignore($roleId),
            ],

            'level' => [
                'required',
                'integer',
                'min:1',
                'max:100',
            ],

            'permissions' => [
                'required',
                'array',
                'min:1',
            ],

            'permissions.*' => [
                'integer',
                'exists:permissions,id',
            ],
        ];
    }

    /**
     * Custom validation messages
     */
    public function messages(): array
    {
        return [
            'name.unique' => 'This role name already exists.',
            'permissions.required' => 'At least one permission must be selected.',
            'permissions.array' => 'Permissions must be an array.',
            'permissions.*.exists' => 'One or more selected permissions are invalid.',
        ];
    }
}