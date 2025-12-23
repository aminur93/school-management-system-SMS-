<?php

namespace App\Http\Requests\Api\V1\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
            'login' => [
            'required',
            'string',
            'max:255',
            function ($attribute, $value, $fail) {
                    if (!filter_var($value, FILTER_VALIDATE_EMAIL) && !preg_match('/^\d{10,11}$/', $value)) {
                        $fail('The login field must be a valid email address or phone number.');
                    }
                },
            ],
            'password' => ['required', 'string', 'min:8', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'login.required' => 'The login field is required.',
            'login.string' => 'The login field must be a valid string.',
            'login.max' => 'The login field must not exceed 255 characters.',
            'password.required' => 'The password field is required.',
            'password.string' => 'The password must be a valid string.',
            'password.min' => 'The password must be at least 8 characters long.',
            'password.max' => 'The password must not exceed 255 characters.',
        ];
    }
}