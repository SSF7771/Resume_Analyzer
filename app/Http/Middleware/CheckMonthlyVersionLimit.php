<?php

namespace App\Http\Middleware;

use App\Models\Resume;
use App\Models\ResumeVersion;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMonthlyVersionLimit
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public const MONTHLY_LIMIT = 5;

    public const MONTHLY_LIMIT_PER_RESUME = 5;

    public function handle(Request $request, Closure $next): Response
    {
        // Retrieve the resume from the route (handles both Model binding and raw string ID)
        $resumeParam = $request->route('resume') ?? $request->route('id');
        $resumeId = $resumeParam instanceof Resume ? $resumeParam->id : $resumeParam;

        if (!$resumeId) {
            return $next($request);
        }

        // Verify the resume belongs to the authenticated user
        $resume = $resumeParam instanceof Resume 
            ? $resumeParam 
            : Resume::where('_id', $resumeId)->where('user_id', auth()->id())->first();

        if (!$resume || (string)$resume->user_id !== (string)auth()->id())
            return response()->json(['message' => 'Unauthorized or resume not found.'], 403);

        // Count versions created THIS MONTH specifically for THIS resume
        $versionsThisMonth = ResumeVersion::where('resume_id', (string)$resume->id)
            ->where('created_at', '>=', now()->startOfMonth())
            ->count();

        // If the specific resume has reached 5 versions this month, block the request
        if ($versionsThisMonth >= self::MONTHLY_LIMIT_PER_RESUME) {
            $resetDate = now()->addMonth()->startOfMonth()->toFormattedDateString();

            return response()->json([
                'success' => false,
                'message' => "This resume has reached its limit of 5 versions this month. Quota resets on {$resetDate}.",
                'limit'   => self::MONTHLY_LIMIT_PER_RESUME,
                'used'    => $versionsThisMonth,
            ], 429);
        }

        return $next($request);
    }
}
