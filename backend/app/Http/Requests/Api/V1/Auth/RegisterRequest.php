<?php

namespace App\Http\Requests\Api\V1\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
                'string',
                'in:superadmin,admin,subcription,owner', // if need change it
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed', // password_confirmation required
            ],

            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048', // 2MB
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'This email is already registered.',
            'phone.unique' => 'This phone number is already registered.',
            'password.confirmed' => 'Password confirmation does not match.',
            'user_type.in' => 'Invalid user type provided.',
        ];
    }
}