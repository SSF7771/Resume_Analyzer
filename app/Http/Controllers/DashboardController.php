<?php

namespace App\Http\Controllers;

use App\Models\Resume;
use App\Models\ResumeVersion;
use App\Models\Analysis;
use App\Services\HistoryService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(protected HistoryService $historyService)
    {
    }

    public function index()
    {
        try {
            $userId = Auth::id();
            $resumes = Resume::where('user_id', $userId)->orderBy('updated_at', 'desc')->get();
            $allAnalyses = Analysis::where('user_id', $userId)->orderBy('created_at', 'asc')->get();

            // Totals
            $totals = [
                'resumes' => $resumes->count(),
                'rewrites' => ResumeVersion::whereIn('resume_id', $resumes->pluck('id'))->where('source_type', 'rewrite')->count(),
                'analyses' => $allAnalyses->count(),
                'exports' => 0,
            ];

            $latestResume = $resumes->first();
            $data = ['totals' => $totals, 'latestResume' => $latestResume];

            if ($latestResume) {
                $versions = ResumeVersion::where('resume_id', $latestResume->id)->orderBy('version_number')->get();
                $data['scoreSeries'] = $this->getScoreSeries($versions, $allAnalyses);
                $data['versionStack'] = $this->getVersionStack($versions, $allAnalyses);
            }

            $data['kpi'] = $this->calculateKpis($allAnalyses, $resumes);
            $data['activity'] = $this->getActivity($userId);

            return response()->json(array_merge(['success' => true], $data));

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    private function getScoreSeries($versions, $allAnalyses)
    {
        return $versions->map(function ($v) use ($allAnalyses) {
            $analysis = $allAnalyses->where('version_id', $v->id)->last();
            return $analysis ? ['label' => $v->label, 'score' => $analysis->ats_score, 'at' => $analysis->created_at] : null;
        })->filter();
    }

    private function getVersionStack($versions, $allAnalyses)
    {
        // Add $allAnalyses to the 'use' clause
        return $versions->slice(-3)->values()->map(function ($v, $i) use ($versions, $allAnalyses) {

            $score = $allAnalyses->where('version_id', $v->id)->last()?->ats_score ?? 0;

            // Ensure $versions->get($i - 1) is used correctly to get the previous item
            $prevVersion = $versions->get($i - 1);
            $prevScore = $prevVersion
                ? ($allAnalyses->where('version_id', $prevVersion->id)->last()?->ats_score ?? 0)
                : 0;

            return [
                'id' => $v->id,
                'label' => $v->label,
                'title' => $v->source_type === 'upload' ? 'Upload' : ($v->source_type === 'rewrite' ? 'Rewrite pass' : $v->label),
                'score' => $score,
                'delta' => ($i > 0) ? $score - $prevScore : 0
            ];
        });
    }

    private function calculateKpis($analyses, $resumes)
    {
        $latest = $analyses->last();

        // Get the second-to-last item safely
        $prev = ($analyses->count() > 1) ? $analyses->slice(-2, 1)->first() : null;

        $getKeywordsCount = fn($a) => is_array($a?->keywords_present) ? count($a->keywords_present) : 0;

        return [
            'atsScore' => [
                'value' => $latest?->ats_score,
                'delta' => $latest && $prev ? $latest->ats_score - $prev->ats_score : null,
                'spark' => $analyses->take(-10)->map(fn($a) => ['v' => $a->ats_score])
            ],
            'versions' => [
                'value' => $resumes->sum('latest_version_number'),
                'delta' => null,
                'spark' => $resumes->take(10)->reverse()->map(fn($r) => ['v' => $r->latest_version_number])
            ],
            'issuesIdentified' => [
                // Ensure $latest->issues is treated as an array even if it's null/empty
                'value' => $latest ? count($latest->issues ?? []) : 0,
                'delta' => ($latest && $prev) ? count($latest->issues ?? []) - count($prev->issues ?? []) : null,
                'spark' => $analyses->take(-10)->map(fn($a) => ['v' => count($a->issues ?? [])])
            ],
            'keywordsMatched' => [
                'value' => $getKeywordsCount($latest),
                'delta' => ($latest && $prev) ? $getKeywordsCount($latest) - $getKeywordsCount($prev) : null,
                'spark' => $analyses->take(-10)->map(fn($a) => ['v' => $getKeywordsCount($a)])
            ],
        ];
    }

    private function getActivity($userId)
    {
        // Just call the service and ask for 8 items
        return $this->historyService->getEventTimeline($userId, 8);
    }
}