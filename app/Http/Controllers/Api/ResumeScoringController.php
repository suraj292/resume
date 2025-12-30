<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ResumeScoringService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ResumeScoringController extends Controller
{
    protected ResumeScoringService $scoringService;

    public function __construct(ResumeScoringService $scoringService)
    {
        $this->scoringService = $scoringService;
    }

    /**
     * Score a resume based on provided data.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function score(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'resume_data' => 'required|array',
            'job_description' => 'nullable|string|max:10000',
        ]);

        $result = $this->scoringService->calculate(
            $validated['resume_data'],
            $validated['job_description'] ?? null
        );

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }
}
