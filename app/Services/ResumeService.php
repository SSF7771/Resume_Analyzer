<?php

namespace App\Services;

class ResumeService
{
    public function applyRewrites(string $rawText, array $rewrites): string
    {
        $result = $rawText;

        foreach ($rewrites as $r) {
            if (empty($r['original']) || empty($r['rewritten'])) {
                continue;
            }

            $original = trim($r['original']);
            $rewritten = trim($r['rewritten']);

            // Try exact match first
            if (str_contains($result, $original)) {
                $result = str_replace($original, $rewritten, $result);
                continue;
            }

            // Fallback: Build a flexible regex pattern that ignores 
            // line breaks, tabs, and variable spacing between words.
            $words = preg_split('/\s+/', $original);
            $escapedWords = array_map('preg_quote', $words);

            // Matches any sequence of whitespace (including newlines) between words
            $flexiblePattern = '/' . implode('\s+', $escapedWords) . '/ui';

            $result = preg_replace($flexiblePattern, $rewritten, $result, 1, $count);

            if ($count > 0)
                \Log::info("Rewrite matched via flexible whitespace fallback for: " . substr($original, 0, 30) . "...");
            else
                \Log::warning("Rewrite completely failed match for: " . $original);
        }

        return $result;
    }

    public function patchSections(array $sections, array $rewrites): array
    {
        if (empty($sections))
            return [];

        $patched = $sections;

        foreach ($rewrites as $r) {
            $original = trim($r['original'] ?? '');
            $rewritten = trim($r['rewritten'] ?? '');

            if (empty($original) || empty($rewritten))
                continue;

            if (isset($patched['experience']) && is_array($patched['experience'])) {
                foreach ($patched['experience'] as &$exp) {
                    if (isset($exp['bullet_points']) && is_array($exp['bullet_points'])) {
                        $exp['bullet_points'] = array_map(function ($bullet) use ($r, $original) {
                            $cleanBullet = preg_replace('/\s+/', ' ', trim($bullet));
                            $cleanOriginal = preg_replace('/\s+/', ' ', $original);

                            // Match normalized strings
                            if ($cleanBullet === $cleanOriginal) {
                                return $r['rewritten'];
                            }

                            // Fallback: case-insensitive or partial normalization check if needed
                            if (str_contains(strtolower($cleanBullet), strtolower($cleanOriginal)))
                                return $r['rewritten'];

                            return $bullet;
                        }, $exp['bullet_points']);
                    }
                }
            }
        }
        return $patched;
    }
}