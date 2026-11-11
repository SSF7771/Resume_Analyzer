<?php

namespace App\Services;

use Gemini\Enums\ResponseMimeType;
use Gemini\Laravel\Facades\Gemini;
use Gemini\Data\GenerationConfig;
use Illuminate\Support\Facades\Log;

class ResumeParserService
{
    private function getEmptyStructure(): array
    {
        return [
            'basics' => ['name' => '', 'title' => '', 'location' => '', 'email' => '', 'phone' => '', 'links' => []],
            'summary' => '', 'experience' => [], 'education' => [], 'skills' => [], 
            'projects' => [], 'certifications' => [], 'languages' => [], 'interests' => []
        ];
    }

    public function parse(string $rawText): array
    {
        $prompt = $this->buildPrompt($rawText);

        // Implement a simple retry loop
        for ($attempt = 1; $attempt <= 2; $attempt++) {
            try {
                $response = Gemini::generativeModel(config('services.gemini.model', 'gemini-2.5-flash'))
                    ->withGenerationConfig(new GenerationConfig(
                        responseMimeType: ResponseMimeType::APPLICATION_JSON,
                        temperature: 0.1,
                    ))->generateContent($prompt);

                $text = $response->text();

                $jsonString = preg_replace('/^```json\s*|\s*```$/', '', trim($text));
            
                return json_decode($jsonString, true) ?? $this->getEmptyStructure();

            } catch (\Exception $e) {
                Log::warning("AI Parse attempt $attempt failed: " . $e->getMessage());
                if ($attempt === 2)
                    return $this->getEmptyStructure();
            }
        }

        return $this->getEmptyStructure();
    }

    private function buildPrompt(string $rawText): string
    {
        return <<<EOT
        You are a resume parser. The input is text extracted from a PDF - lines may be jumbled or out of natural reading order.

        EXTRACT STRUCTURED DATA:
        - basics: name, professional title, location, email, phone, social links (LinkedIn / GitHub / portfolio etc.; label like "LinkedIn", full URL)
        - summary: the professional summary paragraph (rejoin if split across lines)
        - experience: jobs most recent first, with company, role, period (preserve original date format), location if available, and bullet points
        - education: degree, school, period, location, optional details
        - skills: flat array of technical skills
        - projects: name, one-sentence description, optional tech tags, optional links
        - certifications: name, issuer, year
        - languages: flat array
        - interests: flat array

        RULES:
        - Be conservative: omit fields that are not clearly present. Use empty strings/arrays where missing.
        - Do not invent or paraphrase - extract verbatim where possible.
        - Each experience bullet should read as a complete sentence.
        - Preserve original date formats (e.g. 'Jan 2022 - Dec 2023').

        RESUME TEXT:
        {$rawText}
        EOT;
    }
}