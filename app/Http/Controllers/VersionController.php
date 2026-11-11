<?php

namespace App\Http\Controllers;

use App\Models\Resume;
use App\Models\ResumeVersion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VersionController extends Controller
{
    public function index()
    {
        // Get all versions for resumes belonging to the user
        $versions = ResumeVersion::query()
            ->whereHas('resume', fn($query) => $query->where('user_id', Auth::id()))
            ->with(['resume:id,title', 'latestAnalysis:id,version_id,ats_score'])
            ->orderBy('created_at', 'desc')
            ->get();

        $items = $versions->map(function ($v) {
            return [
                'id' => $v->id,
                'label' => $v->label,
                'version_number' => $v->version_number,
                'source_type' => $v->source_type,
                'created_at' => $v->created_at,
                'score' => $v->latestAnalysis->ats_score ?? null,
                'resume_id' => $v->resume_id,
                'resume_title' => $v->resume->title ?? 'Resume',
                'parent_version_id' => $v->parent_version_id,
            ];
        });

        $totals = [
            'all' => $items->count(),
            'uploads' => $items->where('source_type', 'upload')->count(),
            'rewrites' => $items->where('source_type', 'rewrite')->count(),
        ];

        return response()->json([
            'success' => true,
            'versions' => $items,
            'totals' => $totals,
        ]);
    }

    public function search(Request $request)
    {
        $q = $request->get('q');
        $userId = auth()->id();

        $versions = ResumeVersion::whereHas('resume', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
            ->where(function ($query) use ($q) {
                $query->where('label', 'like', "%{$q}%")
                    ->orWhereHas('resume', function ($qSub) use ($q) {
                        $qSub->where('title', 'like', "%{$q}%");
                    });
            })
            ->with('resume:id,title')
            ->orderBy('created_at', 'desc')
            ->get();

        $results = $versions->map(function ($v) {
            return [
                'id' => $v->id,
                'label' => $v->label,
                'resume_id' => $v->resume_id,
                'resume_title' => $v->resume->title ?? 'Resume',
                'created_at' => $v->created_at,
                'sourceType' => $v->source_type,
                'score' => $v->latestAnalysis->ats_score ?? null,
            ];
        });

        return response()->json($results);
    }
}