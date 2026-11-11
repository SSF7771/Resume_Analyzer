<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreResumeVersionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'parsed_sections.basics.name' => 'string|max:255',
            'parsed_sections.experience.*.company' => 'string',
            'parsed_sections.experience.*.role' => 'string',
            'source_type' => 'required|in:upload,rewrite',
        ];
    }
}
