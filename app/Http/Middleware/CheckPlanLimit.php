<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPlanLimit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'error' => 'Unauthenticated',
                'message' => 'Please log in to continue'
            ], 401);
        }

        if (!$user->plan) {
            return response()->json([
                'error' => 'No plan assigned',
                'message' => 'Please contact support'
            ], 403);
        }

        // Check feature-specific limits
        if ($feature === 'resume' && !$user->canCreateResume()) {
            return response()->json([
                'error' => 'Resume limit reached',
                'message' => 'You have reached your resume limit. Upgrade your plan to create more resumes.',
                'upgrade_url' => '/pricing',
                'current_limit' => $user->plan->resume_limit,
                'used' => $user->resumes_created
            ], 403);
        }

        if ($feature === 'ats' && !$user->canUseATS()) {
            return response()->json([
                'error' => 'ATS scan limit reached',
                'message' => 'You have reached your ATS scan limit. Upgrade your plan for more scans.',
                'upgrade_url' => '/pricing',
                'current_limit' => $user->plan->ats_scan_limit,
                'used' => $user->ats_scans_used
            ], 403);
        }

        return $next($request);
    }
}
