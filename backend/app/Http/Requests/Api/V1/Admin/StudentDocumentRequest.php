<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StudentDocumentRequest extends FormRequest
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
        $documentTypes = [
            'Birth_Certificate',
            'Previous_School_TC',
            'Medical_Certificate',
            'Photo',
            'Parent_NID',
            'Address_Proof',
            'Other'
        ];

        // Rules for creating (POST)
        $storeRules = [
            'student_id'     => ['required', 'exists:students,id'],
            'document_type'  => ['required', Rule::in($documentTypes)],
            'document_name'  => ['required', 'string', 'max:200'],
            'document_path'  => ['required', 'string', 'max:255'],
            'is_verified'    => ['sometimes', 'boolean'],
        ];

        // Rules for updating (PUT/PATCH)
        $updateRules = [
            'student_id'     => ['sometimes', 'exists:students,id'],
            'document_type'  => ['sometimes', Rule::in($documentTypes)],
            'document_name'  => ['sometimes', 'string', 'max:200'],
            'document_path'  => ['sometimes', 'string', 'max:255'],
            'is_verified'    => ['sometimes', 'boolean'],
        ];

        return match ($this->method()) {
            'POST' => $storeRules,
            'PUT', 'PATCH' => $updateRules,
            default => [],
        };
    }
}