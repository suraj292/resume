<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateResumeRequest extends FormRequest
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
            'resumeContext' => 'nullable|string|max:20000',
            'jobDescription' => 'nullable|string|max:20000',
            'tone' => 'nullable|string|in:Professional,Creative,Direct',
            'currentData' => 'nullable|array',
            'force_refresh' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'resumeContext.max' => 'The resume context is too long (max 20,000 characters).',
            'jobDescription.max' => 'The job description is too long (max 20,000 characters).',
            'tone.in' => 'The selected tone is invalid.',
        ];
    }
}
