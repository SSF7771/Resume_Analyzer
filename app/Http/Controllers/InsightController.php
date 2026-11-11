<?php

namespace App\Http\Controllers;

use App\Models\Resume;
use App\Models\Analysis;
use Illuminate\Support\Facades\Auth;

class InsightController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $analyses = Analysis::where('user_id', $userId)->with('resume:id,title')->get();
        $resumes = Resume::where('user_id', $userId)->get();

        if ($analyses->isEmpty()) {
            return response()->json([
                'empty' => true,
                'totalAnalyses' => 0,
                'resumes' => $resumes->map(fn($r) => ['id' => $r->id, 'title' => $r->title])
            ]);
        }

        // Eloquent/Collection power:
        $avgScore = $analyses->avg('ats_score');
        $bestEntry = $analyses->sortByDesc('ats_score')->first();

        return response()->json([
            'empty' => false,
            'totalAnalyses' => $analyses->count(),
            'averageScore' => round($avgScore),
            'bestScore' => [
                'value' => $bestEntry->ats_score,
                'resumeId' => $bestEntry->resume_id,
                'resumeTitle' => $bestEntry->resume->title ?? 'Resume',
                'at' => $bestEntry->created_at,
            ],
            'scoreTrend' => $analyses->sortBy('created_at')->map(fn($a) => [
                'at' => $a->created_at,
                'score' => $a->ats_score,
                'resumeTitle' => $a->resume->title ?? 'Resume'
            ]),
            'topIssues' => $this->getTopIssues($analyses),
            'topMissingKeywords' => $this->getTopKeywords($analyses, 'keywords_missing'),
            'topPresentKeywords' => $this->getTopKeywords($analyses, 'keywords_present'),
            'resumePerformance' => $this->getResumePerformance($resumes, $analyses)
        ]);
    }

    private function getTopIssues($analyses)
    {
        return collect($analyses->flatMap->issues)
            ->groupBy(fn($i) => strtolower(trim($i['title'])))
            ->map(fn($group, $key) => [
                'title' => $group->first()['title'],
                'count' => $group->count(),
                'severity' => $group->first()['severity']
            ])
            ->sortByDesc('count')
            ->take(6)
            ->values();
    }

    private function getTopKeywords($analyses, $field)
    {
        return collect($analyses->flatMap->$field)
            ->countBy(fn($k) => strtolower(trim($k)))
            ->sortDesc()
            ->take(12)
            ->map(fn($count, $key) => ['keyword' => $key, 'count' => $count])
            ->values();
    }

    private function getResumePerformance($resumes, $analyses)
    {
        return $resumes->map(function ($resume) use ($analyses) {
            $resumeAnalyses = $analyses->where('resume_id', $resume->id);
            if ($resumeAnalyses->isEmpty()) return null;

            return [
                'resumeId' => $resume->id,
                'title' => $resume->title,
                'analysesCount' => $resumeAnalyses->count(),
                'latestScore' => $resumeAnalyses->last()->ats_score,
                'bestScore' => $resumeAnalyses->max('ats_score'),
                'improvement' => $resumeAnalyses->last()->ats_score - $resumeAnalyses->first()->ats_score,
            ];
        })->filter()->sortByDesc('latestScore')->values();
    }
}