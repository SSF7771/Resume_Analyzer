<?php

use App\Http\Controllers\HealthController;
use Illuminate\Support\Facades\Route;

// Public Blade pages
Route::view('/', 'pages.landing')->name('home');
Route::view('/login', 'pages.login')->name('login');
Route::view('/register', 'pages.register')->name('register');

// Protected Blade pages
Route::view('/dashboard', 'pages.dashboard')->name('dashboard');
Route::view('/resumes', 'pages.resumes')->name('resumes.index');
Route::view('/resumes/{id}', 'pages.resume-detail')->name('resumes.show');
Route::view('/resumes/{id}/export', 'pages.export')->name('resumes.export');
Route::view('/insights', 'pages.insights')->name('insights');
Route::view('/versions', 'pages.versions')->name('versions');
Route::view('/history', 'pages.history')->name('history');
Route::view('/settings', 'pages.settings')->name('settings');

Route::get('/health', HealthController::class)->name('health');

// FOR UNKNOWN ROUTES -> redirects to home
Route::fallback(fn () => redirect('/'));

