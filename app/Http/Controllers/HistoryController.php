<?php

namespace App\Http\Controllers;

use App\Services\HistoryService;
use Illuminate\Support\Facades\Auth;

class HistoryController extends Controller
{
    public function index(HistoryService $historyService)
    {
        $events = $historyService->getEventTimeline(Auth::id(), 100); // Fetch all

        return response()->json([
            'success' => true,
            'events' => $events,
            'totals' => [
                'all' => $events->count(),
                'upload' => $events->where('type', 'upload')->count(),
                'analyze' => $events->where('type', 'analyze')->count(),
                'rewrite' => $events->where('type', 'rewrite')->count(),
            ]
        ]);
    }
}