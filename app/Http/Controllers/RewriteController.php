<?php

namespace App\Http\Controllers;

use App\Models\Resume;
use App\Models\Analysis;
use App\Models\ResumeVersion;
use App\Services\ResumeParserService;
use App\Services\ResumeService;
use Illuminate\Http\Request;

class RewriteController extends Controller
{
    public function store(Resume $resume, Request $request, ResumeService $logic, ResumeParserService $parser)
    {
        $analysis = Analysis::findOrFail($request->analysis_id);
        $currentVersion = $resume->currentVersion;

        $allRewrites = $analysis->bullet_rewrites;
        $rewrites = $request->has('rewrite_ids')
            ? collect($allRewrites)->filter(fn($r, $i) => in_array((string) ($r['id'] ?? $r['_id'] ?? $i), $request->rewrite_ids))->toArray()
            : $allRewrites;

        // Use currentVersion->raw_text instead of baseVersion
        $newRaw = $logic->applyRewrites($currentVersion->raw_text, $rewrites);

        // Patch structure manually (The Fallback)
        $patchedFromCurrent = $logic->patchSections($currentVersion->parsed_sections, $rewrites);

        // AI Re-parse for structure
        $reparsed = $parser->parse($newRaw);

        // Check if the AI parser returned an empty/junk structure
        $isParsedEmpty = empty($reparsed['experience']) && empty($reparsed['summary']);
        $baseParsed = $isParsedEmpty ? $patchedFromCurrent : $reparsed;

        // Ensure the final JSON structure actually reflects the rewrites, 
        // even if the AI re-parser missed them or reverted them.
        $finalParsed = $logic->patchSections($baseParsed, $rewrites);

        $newVersionNumber = $resume->latest_version_number + 1;

        $newVersion = ResumeVersion::create([
            'resume_id' => $resume->id,
            'version_number' => $newVersionNumber,
            'label' => $request->label ?? "V{$newVersionNumber}",
            'raw_text' => $newRaw,
            'parsed_sections' => $finalParsed,
            'source_type' => 'rewrite',
            'parent_version_id' => $currentVersion->id,
        ]);

        $resume->update(['latest_version_number' => $newVersionNumber, 'current_version_id' => $newVersion->id]);

        return response()->json(['success' => true, 'version' => $newVersion], 201);
    }
}