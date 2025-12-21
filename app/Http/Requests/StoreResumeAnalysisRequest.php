<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreResumeAnalysisRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Allow both authenticated and guest users
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'input_type' => 'required|in:upload,paste',
            'file' => 'required_if:input_type,upload|file|mimes:pdf,doc,docx,txt|max:5120', // Max 5MB
            'content' => 'required_if:input_type,paste|string|min:50|max:50000',
            'job_description' => 'nullable|string|max:10000',
        ];
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'file.required_if' => 'Please upload a resume file.',
            'file.mimes' => 'Resume must be a PDF, DOC, DOCX, or TXT file.',
            'file.max' => 'Resume file size must not exceed 5MB.',
            'content.required_if' => 'Please paste your resume content.',
            'content.min' => 'Resume content must be at least 50 characters.',
            'content.max' => 'Resume content is too long (maximum 50,000 characters).',
        ];
    }
}

