<?php

namespace App\Services;

use Jfcherng\Diff\Differ;
use Jfcherng\Diff\DiffHelper;

class DiffService
{
    public function diffText(string $oldText, string $newText, string $mode = 'words'): array
    {
        // Calculate diff using 'Unified' renderer (returns a string)
        $diffString = DiffHelper::calculate($oldText, $newText, 'Unified');

        $mapped = [];
        $lines = explode("\n", $diffString);

        foreach ($lines as $line) {
            // Skip headers
            if (str_starts_with($line, '---') || str_starts_with($line, '+++') || str_starts_with($line, '@@')) {
                continue;
            }

            if (str_starts_with($line, '+')) {
                $mapped[] = ['value' => substr($line, 1), 'added' => true, 'removed' => false];
            } elseif (str_starts_with($line, '-')) {
                $mapped[] = ['value' => substr($line, 1), 'added' => false, 'removed' => true];
            } elseif (str_starts_with($line, ' ')) {
                // Lines starting with a space are unchanged content
                $mapped[] = ['value' => substr($line, 1), 'added' => false, 'removed' => false];
            } else {
                // Handle lines without +/-/space (like newlines or trailing info)
                // Skip them if they are just empty
                if (empty(trim($line)))
                    continue;
                $mapped[] = ['value' => $line, 'added' => false, 'removed' => false];
            }
        }

        return $mapped;
    }

    public function summarize(array $parts): array
    {
        $added = 0;
        $removed = 0;

        foreach ($parts as $p) {
            if ($p['added'])
                $added += strlen($p['value']);
            if ($p['removed'])
                $removed += strlen($p['value']);
        }

        return ['added' => $added, 'removed' => $removed];
    }
}