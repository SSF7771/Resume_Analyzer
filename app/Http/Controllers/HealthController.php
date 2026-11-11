<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HealthController extends Controller
{
    public function __invoke()
    {
        try {
            return response()->json([
                'status' => 'connected',
                'database' => DB::connection('mongodb')->getDatabaseName(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'disconnected',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}