<?php

namespace App\Services;

use Smalot\PdfParser\Parser;
use Illuminate\Validation\ValidationException;

class PdfService
{
    public function extract(string $filePath): array
    {
        $parser = new Parser();
        
        try {
            // Parse the PDF file
            $pdf = $parser->parseFile($filePath);
            $text = trim($pdf->getText());
            $details = $pdf->getDetails();

            // Heuristic: If text is too short, flag it
            if (empty($text) || strlen($text) < 50) {
                throw ValidationException::withMessages([
                    'file' => 'Could not extract readable text - is this a scanned or image-only PDF?'
                ]);
            }

            return [
                'text' => $text,
                'meta' => [
                    'numPages' => $details['Pages'] ?? null,
                ],
            ];

        } catch (\Exception $e) {
            // If it's already a ValidationException, rethrow it
            if ($e instanceof ValidationException) {
                throw $e;
            }

            // Otherwise, wrap the error
            throw ValidationException::withMessages([
                'file' => 'Failed to parse PDF: ' . $e->getMessage()
            ]);
        }
    }
}