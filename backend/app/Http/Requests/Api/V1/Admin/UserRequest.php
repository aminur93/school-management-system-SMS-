<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
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

            'email' => [
                'required',
                'email',
                'max:150',
                'unique:users,email',
            ],

            'phone' => [
                'required',
                'string',
                'max:20',
                'unique:users,phone',
            ],

            'user_type' => [
                'required',
                'in:admin,owner,superadmin,subcription',
            ],

            'password' => [
                'required',
                'string',
                'min:6',
                'confirmed',
            ],

            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png',
                'max:2048',
            ],
        ];
    }

    /**
     * Validation rules for update request
     */
    protected function updateRules(): array
    {
        // Route model binding or id
        $userId = $this->route('user');

        return [
            'name' => [
                'sometimes',
                'string',
                'max:100',
            ],

            'email' => [
                'sometimes',
                'email',
                'max:150',
                Rule::unique('users', 'email')->ignore($userId),
            ],

            'phone' => [
                'sometimes',
                'string',
                'max:20',
                Rule::unique('users', 'phone')->ignore($userId),
            ],

            'user_type' => [
                'sometimes',
                'in:admin,owner,superadmin,subcription',
            ],

            'password' => [
                'nullable',
                'string',
                'min:6',
                'confirmed',
            ],

            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png',
                'max:2048',
            ],
        ];
    }

    /**
     * Custom validation messages
     */
    public function messages(): array
    {
        return [
            'email.unique' => 'This email address is already in use.',
            'phone.unique' => 'This phone number is already in use.',
            'password.confirmed' => 'Password confirmation does not match.',
            'user_type.in' => 'Invalid user type selected.',
            'image.image' => 'Uploaded file must be an image.',
            'image.mimes' => 'Image must be a JPG or PNG file.',
        ];
    }
}