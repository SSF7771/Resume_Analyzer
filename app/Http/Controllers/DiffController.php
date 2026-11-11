<?php

namespace App\Http\Controllers;

use App\Models\Resume;
use App\Models\ResumeVersion;
use App\Services\DiffService;
use Illuminate\Http\Request;

class DiffController extends Controller
{
    public function show($resumeId, Request $request, DiffService $diffService)
    {
        // Find the resume using the ID from the route
        $resume = Resume::where('_id', $resumeId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // Fetch the actual ResumeVersion models using the IDs
        $fromV = ResumeVersion::where('_id', $request->query('from'))
            ->where('resume_id', $resume->id)
            ->firstOrFail();

        $toV = ResumeVersion::where('_id', $request->query('to'))
            ->where('resume_id', $resume->id)
            ->firstOrFail();

        // Now we can access ->raw_text because these are model instances
        $parts = $diffService->diffText($fromV->raw_text, $toV->raw_text, $request->query('mode', 'words'));

        // \Log::info("Diff parts count: " . count($parts));

        return response()->json([
            'success' => true,
            'from' => ['id' => $fromV->id, 'label' => $fromV->label],
            'to' => ['id' => $toV->id, 'label' => $toV->label],
            'parts' => $parts,
            'stats' => $diffService->summarize($parts),
        ]);
    }
}