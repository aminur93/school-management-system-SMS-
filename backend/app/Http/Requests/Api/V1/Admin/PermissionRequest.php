<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PermissionRequest extends FormRequest
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
                'regex:/^[a-z]+(\.[a-z]+)*$/',
                'unique:permissions,name',
            ],
            'title' => [
                'required',
                'string',
                'max:150',
            ],
        ];
    }

    /**
     * Validation rules for update request
     */
    public function updateRules(): array
    {
        // Get the current permission ID from route
        $permissionId = $this->route('permission');

        return [
            'name' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-z]+(\.[a-z]+)*$/',
                Rule::unique('permissions', 'name')->ignore($permissionId),
            ],
            'title' => [
                'required',
                'string',
                'max:150',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.regex' => 'Permission name must be lowercase and dot separated (example: user.create).',
            'name.unique' => 'This permission already exists.',
        ];
    }
}