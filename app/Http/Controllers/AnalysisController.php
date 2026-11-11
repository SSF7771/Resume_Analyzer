<?php

namespace App\Http\Controllers;

use App\Models\Resume;
use App\Models\Analysis;
use App\Services\AiAnalysisService; // Assume you moved your Gemini logic here
use App\Services\ResumeParserService;
use Illuminate\Http\Request;
use Log;

class AnalysisController extends Controller
{
    public function store(Resume $resume, Request $request, AiAnalysisService $aiService)
    {
        $versionId = $request->input('version_id') ?? $resume->current_version_id;
        $version = $resume->versions()->find($versionId);

        if (!$version) {
            return response()->json(['message' => 'Version not found'], 404);
        }

        $result = $aiService->analyze($version->raw_text, $request->input('target_role'));

        // TESTING
        // Log::info('Gemini AI Result:', $result);

        $analysis = Analysis::create([
            'user_id' => auth()->id(),
            'resume_id' => $resume->id,
            'version_id' => $version->id,
            'ats_score' => $result['analysis']['atsScore'],
            'score_breakdown' => $result['analysis']['scoreBreakdown'],
            'issues' => $result['analysis']['issues'],
            'strengths' => $result['analysis']['strengths'],
            'bullet_rewrites' => $result['analysis']['bulletRewrites'],
            'keywords_present' => $result['analysis']['keywordsPresent'],
            'keywords_missing' => $result['analysis']['keywordsMissing'],
            'summary' => $result['analysis']['summary'],
            'model' => $result['model'],
            'prompt_tokens' => $result['promptTokens'],
            'response_tokens' => $result['responseTokens'],
        ]);

        $version->update(['latest_analysis_id' => $analysis->id]);

        return response()->json([
            'success' => true, 
            'analysis' => $analysis
        ], 201);
    }

    public function show($resumeId, $versionId)
    {
        // Verify the resume exists and belongs to the user
        $resume = Resume::where('id', $resumeId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // Find the analysis linked to this resume and version
        $analysis = Analysis::where('resume_id', $resume->id)
            ->where('version_id', $versionId)
            ->orderBy('created_at', 'desc')
            ->first();

        return response()->json([
            'success' => true,
            'analysis' => $analysis
        ]);
    }
}