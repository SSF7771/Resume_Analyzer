<?php

namespace App\Services;

use Gemini\Laravel\Facades\Gemini;
use Gemini\Data\GenerationConfig;
use Gemini\Enums\ResponseMimeType;
use Illuminate\Support\Facades\Log;

class AiAnalysisService
{
    public function analyze(string $rawText, ?string $targetRole): array
    {
        if (!config('gemini.api_key'))
            throw new \Exception("Gemini API Key is not loaded in config/gemini.php!");

        $prompt = $this->buildPrompt($rawText, $targetRole);

        for ($attempt = 1; $attempt <= 2; $attempt++) {
            try {
                $response = Gemini::generativeModel(config('services.gemini.model', 'gemini-2.5-flash'))
                    ->withGenerationConfig(new GenerationConfig(
                        responseMimeType: ResponseMimeType::APPLICATION_JSON,
                        temperature: 0.4,
                    ))->generateContent($prompt);

                $result = json_decode($response->text(), true);

                return [
                    'analysis' => $result,
                    'model' => config('services.gemini.model', 'gemini-2.5-flash'),
                    'promptTokens' => $response->usageMetadata?->promptTokenCount ?? 0,
                    'responseTokens' => $response->usageMetadata?->candidatesTokenCount ?? 0,
                ];

            } catch (\Exception $e) {
                if ($attempt === 2)
                    throw $e;
                Log::warning("AI Analysis attempt $attempt failed: " . $e->getMessage());
            }
        }

        throw new \Exception("AI analysis failed after 2 attempts.");
    }

    private function buildPrompt(string $rawText, ?string $targetRole): string
    {

        $roleContext = $targetRole
            ? "Target role: {$targetRole}."
            : "No specific target role was provided - assess for the role the candidate appears to be aiming for.";

        return <<<EOT
        You are a senior technical recruiter and ATS expert. 
        {$roleContext}
        Score the resume from 0-100 based on ATS readiness (keyword match, parseable formatting, quantified impact, clarity).
        Return exactly 5 prioritized issues, 5 standout strengths, and 5-10 weak bullets rewritten to be stronger, quantified, and ATS-friendly.
        Rewrites must preserve the original meaning. Each rewrite needs a one-line rationale.
        Identify keywords clearly present and notable keywords missing for the apparent target role.
        Be specific and evidence-based - cite phrasing from the resume in explanations.
    
        Return the response as a single valid JSON object with the following structure:
        {
        "atsScore": number (0-100),
        "scoreBreakdown": { "keywords": number, "formatting": number, "impact": number, "clarity": number },
        "issues": [{ "title": string, "severity": "low"|"medium"|"high", "explanation": string, "fix": string }],
        "strengths": [{ "title": string, "evidence": string }],
        "bulletRewrites": [{ "section": string, "original": string, "rewritten": string, "rationale": string }],
        "keywordsPresent": [string],
        "keywordsMissing": [string],
        "summary": string
        }

        RULES:
        - Return ONLY valid JSON. Do not include any introductory or concluding text.
        - All keys must be in camelCase as shown above.
        - Be evidence-based - cite specific phrasing from the resume.

        RESUME TEXT:
        {$rawText}
        EOT;
    }
}