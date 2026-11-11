<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PageController extends Controller
{
    public function landing()
    {
        return view('pages.landing');
    }
    public function login()
    {
        return view('pages.login');
    }
    public function register()
    {
        return view('pages.register');
    }
    public function dashboard()
    {
        return view('pages.dashboard');
    }
    public function resumes()
    {
        return view('pages.resumes');
    }
    public function resumeDetail($id)
    {
        return view('pages.resume-detail', ['resumeId' => $id]);
    }
    public function export($id)
    {
        return view('pages.export', ['resumeId' => $id]);
    }
    public function insights()
    {
        return view('pages.insights');
    }
    public function versions()
    {
        return view('pages.versions');
    }
    public function history()
    {
        return view('pages.history');
    }
    public function settings()
    {
        return view('pages.settings');
    }
}
