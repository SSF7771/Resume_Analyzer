<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreResumeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;    // Sanctum handles auth
    } 

    public function rules(): array
    {
        return [
            'file' => 'required|file|mimes:pdf|max:5120', // 5MB limit
            'title' => 'nullable|string|max:120',
        ];
    }
}