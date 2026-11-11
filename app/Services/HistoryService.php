<?php

namespace App\Services;

use App\Models\Resume;
use App\Models\ResumeVersion;
use App\Models\Analysis;

class HistoryService
{
    public function getEventTimeline(string $userId, int $limit = 8)
    {
        $resumes = Resume::where('user_id', $userId)->get();
        $versions = ResumeVersion::whereIn('resume_id', $resumes->pluck('id'))->get();
        $analyses = Analysis::where('user_id', $userId)->get();

        $events = collect();

        // Uploads
        foreach ($resumes as $r) {
            $events->push([
                'id' => "r-{$r->id}", 'type' => 'upload', 'title' => "{$r->title} uploaded",
                'subtitle' => 'Parsed and version V1 created', 'label' => 'V1',
                'at' => $r->created_at, 'resumeId' => $r->id, 'resumeTitle' => $r->title,
            ]);
        }

        // Rewrites
        foreach ($versions->where('source_type', 'rewrite') as $v) {
            $resume = $resumes->firstWhere('id', $v->resume_id);
            $events->push([
                'id' => "v-{$v->id}", 'type' => 'rewrite',
                'title' => "{$v->label} created for " . ($resume->title ?? 'Resume'),
                'subtitle' => 'AI Rewrites applied to previous version', 'label' => $v->label,
                'at' => $v->created_at, 'resumeId' => $v->resume_id, 'resumeTitle' => $resume->title ?? 'Resume',
            ]);
        }

        // Analyses
        foreach ($analyses as $a) {
            $resume = $resumes->firstWhere('id', $a->resume_id);
            $events->push([
                'id' => "a-{$a->id}", 'type' => 'analyze',
                'title' => "Analysis complete on " . ($resume->title ?? 'Resume'),
                'subtitle' => "ATS score {$a->ats_score} / 100", 'label' => (string) $a->ats_score,
                'at' => $a->created_at, 'resumeId' => $a->resume_id, 'resumeTitle' => $resume->title ?? 'Resume',
            ]);
        }

        return $events->sortByDesc('at')->values()->take($limit);
    }
}