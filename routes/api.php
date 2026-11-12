<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\ResumeController;
use App\Http\Controllers\AnalysisController;
use App\Http\Controllers\RewriteController;
use App\Http\Controllers\DiffController;
use App\Http\Controllers\VersionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\InsightController;
use Gemini\Laravel\Facades\Gemini;
use Illuminate\Support\Facades\Route;

// Health Check
Route::get('/health', HealthController::class);

// Route::get('/test-models', function () {
//     $models = Gemini::models()->list();
//     return response()->json($models);
// });

// Auth Routes (Public)
Route::post('/auth/register', [UserController::class, 'register']);
Route::post('/auth/login', [UserController::class, 'login']);

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    // User Management
    Route::get('/me', [UserController::class, 'me']);
    Route::patch('/profile', [UserController::class, 'updateProfile']);
    Route::patch('/password', [UserController::class, 'updatePassword']);
    Route::post('/logout', [UserController::class, 'logout']);

    // Dashboard & History
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/history', [HistoryController::class, 'index']);
    Route::get('/insights', [InsightController::class, 'index']);

    // Resumes
    Route::post('/resumes', [ResumeController::class, 'store']);
    Route::get('/resumes', [ResumeController::class, 'index']);
    Route::get('/resumes/{resume}', [ResumeController::class, 'show']);
    Route::delete('/resumes/{resume}', [ResumeController::class, 'destroy']);
    Route::get('/search', [ResumeController::class, 'search']);

    // Resume Versions
    Route::get('/versions', [VersionController::class, 'index']);
    Route::get('/versions/search', [VersionController::class, 'search']);

    // Analyses & Rewrites
    Route::post('/resumes/{resume}/analyze', [AnalysisController::class, 'store']);
    Route::post('/resumes/{resume}/rewrite', [RewriteController::class, 'store']);
    Route::get('/resumes/{resume}/diff', [DiffController::class, 'show']);

    // Analysis for a specific version of the Resume
    Route::get('/resumes/{resumeId}/versions/{versionId}/analysis', [AnalysisController::class, 'show']);
});