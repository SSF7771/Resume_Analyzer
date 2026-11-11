<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreAnalysisRequest extends FormRequest
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
            'user_id' => 'required|string',
            'resume_id' => 'required|string',
            'version_id' => 'required|string',
            'ats_score' => 'required|integer|min:0|max:100',
            'model' => 'required|string',
            'summary' => 'nullable|string',

            // Score Breakdown
            'score_breakdown' => 'required|array',
            'score_breakdown.keywords' => 'required|integer|min:0|max:25',
            'score_breakdown.formatting' => 'required|integer|min:0|max:25',
            'score_breakdown.impact' => 'required|integer|min:0|max:25',
            'score_breakdown.clarity' => 'required|integer|min:0|max:25',

            // Issues
            'issues' => 'array',
            'issues.*.title' => 'required|string|max:255',
            'issues.*.severity' => 'required|in:low,medium,high',
            'issues.*.explanation' => 'nullable|string',
            'issues.*.fix' => 'nullable|string',

            // Strengths
            'strengths' => 'array',
            'strengths.*.title' => 'required|string|max:255',
            'strengths.*.evidence' => 'nullable|string',

            // Bullet Rewrites
            'bullet_rewrites' => 'array',
            'bullet_rewrites.*.section' => 'nullable|string',
            'bullet_rewrites.*.original' => 'required|string',
            'bullet_rewrites.*.rewritten' => 'required|string',
            'bullet_rewrites.*.rationale' => 'nullable|string',

            // Keyword arrays
            'keywords_present' => 'array',
            'keywords_present.*' => 'string',
            'keywords_missing' => 'array',
            'keywords_missing.*' => 'string',

            // Tokens
            'prompt_tokens' => 'nullable|integer',
            'response_tokens' => 'nullable|integer',
        ];
    }
}
