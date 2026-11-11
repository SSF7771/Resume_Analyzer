<?php

namespace App\Http\Controllers;

use App\Models\Analysis;
use App\Models\Resume;
use App\Models\ResumeVersion;
use App\Http\Requests\StoreResumeRequest;
use App\Services\PdfService;
use App\Services\ResumeParserService;
use Cloudinary\Api\Upload\UploadApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Log;

class ResumeController extends Controller
{
    public function store(StoreResumeRequest $request)
    {
        $pdfService = new PdfService();
        $parserService = new ResumeParserService();

        // Store original file locally
        $originalPublicId = $request->file('file')->store('uploads', 'public');
        $localPath = storage_path('app/public/' . $originalPublicId);

        $uploadResult = (new UploadApi())->upload($localPath, [
            'resource_type' => 'raw',
            'folder' => 'resumes',
        ]);

        // When using UploadApi, the result is an array, NOT an object
        $cloudinaryUrl = $uploadResult['secure_url'];
        $cloudinaryPublicId = $uploadResult['public_id'];

        // Extract & Parse
        $extracted = $pdfService->extract($localPath);
        $parsedSections = $parserService->parse($extracted['text']);

        // DEBUG: See if the PDF library actually got text
        Log::info("Extracted Text Length: " . strlen($extracted['text']));

        return DB::transaction(function () use ($request, $originalPublicId, $cloudinaryUrl, $cloudinaryPublicId, $extracted, $parsedSections) {

            $resume = Resume::create([
                'user_id' => auth()->id(),
                'title' => $request->input('title') ?: 'Untitled Resume',
                'latest_version_number' => 1,
            ]);

            $version = ResumeVersion::create([
                'resume_id' => $resume->id,
                'version_number' => 1,
                'label' => 'V1',
                'raw_text' => $extracted['text'],
                'parsed_sections' => $parsedSections,
                'source_type' => 'upload',
                'file_url' => $cloudinaryUrl,
                'cloudinary_public_id' => $cloudinaryPublicId,
                'original_public_id' => $originalPublicId,
            ]);

            $resume->update(['current_version_id' => $version->id]);

            return response()->json([
                'message' => 'Resume uploaded and processed successfully',
                'data' => [
                    'resume' => $resume,
                    'version' => $version,
                    'url' => $cloudinaryUrl
                ]
            ], 201);
        });
    }

    public function index()
    {
        // Get resumes for logged-in user, sorted by update date
        $resumes = Resume::where('user_id', auth()->id())
            ->orderBy('updated_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'resumes' => $resumes
        ]);
    }

    public function show(Resume $resume)
    {
        // Authorization check
        if ($resume->user_id !== auth()->id())
            return response()->json(['message' => 'Forbidden'], 403);

        $versions = ResumeVersion::where('resume_id', $resume->id)
            ->orderBy('version_number', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'resume' => $resume,
            'versions' => $versions
        ]);
    }

    public function destroy(Resume $resume)
    {
        if ($resume->user_id !== auth()->id())
            return response()->json(['message' => 'Forbidden'], 403);

        // Delete files from Cloudinary
        $versions = ResumeVersion::where('resume_id', $resume->id)->get();
        $uploadApi = new UploadApi();

        foreach ($versions as $version) {
            if ($version->cloudinary_public_id) {
                try {
                    // Cloudinary 'destroy' expects the public_id
                    $uploadApi->destroy($version->cloudinary_public_id, [
                        'resource_type' => 'raw' // Essential for PDFs
                    ]);
                } catch (\Exception $e) {
                    // Log the error but don't stop the deletion process
                    \Log::error("Cloudinary delete failed: " . $e->getMessage());
                }
            }
        }

        ResumeVersion::where('resume_id', $resume->id)->delete();

        Analysis::where('resume_id', $resume->id)->delete();

        $resume->delete();

        return response()->json([
            'message' => 'Resume and all files deleted successfully',
        ]);
    }

    public function search(Request $request)
    {
        $q = $request->get('q');
        $resumes = Resume::where('user_id', auth()->id())
            ->where('title', 'like', "%{$q}%")
            ->get();
        
        return response()->json($resumes);
    }
}