<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GeminiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AIResumeController extends Controller
{
    protected GeminiService $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    public function generateResume(Request $request): JsonResponse
    {
        $request->validate([
            'resumeContext' => 'nullable|string',
            'jobDescription' => 'nullable|string',
            'tone' => 'nullable|string|in:Professional,Creative,Direct',
            'currentData' => 'nullable|array'
        ]);

        try {
            $resumeContext = $request->input('resumeContext', '');
            $jobDescription = $request->input('jobDescription', '');
            $tone = $request->input('tone', 'Professional');
            $currentData = $request->input('currentData', []);

            $prompt = $this->buildGenerateResumePrompt($resumeContext, $jobDescription, $tone, $currentData);
            
            $response = $this->geminiService->generateContent($prompt);

            $parsedData = $this->parseAIResponse($response);

            return response()->json([
                'success' => true,
                'data' => $parsedData,
                'atsScore' => $parsedData['atsScore'] ?? null,
                'message' => 'Resume generated successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('AI Resume Generation Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate resume',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function optimizeForATS(Request $request): JsonResponse
    {
        $request->validate([
            'currentResume' => 'required|array',
            'jobDescription' => 'nullable|string'
        ]);

        try {
            $currentResume = $request->input('currentResume');
            $jobDescription = $request->input('jobDescription', '');

            $prompt = $this->buildATSOptimizationPrompt($currentResume, $jobDescription);
            
            $response = $this->geminiService->generateContent($prompt);

            return response()->json([
                'success' => true,
                'suggestions' => $this->parseATSSuggestions($response),
                'message' => 'ATS optimization suggestions generated'
            ]);

        } catch (\Exception $e) {
            Log::error('ATS Optimization Error', [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to optimize for ATS',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function improveBulletPoints(Request $request): JsonResponse
    {
        $request->validate([
            'bulletPoints' => 'required|array',
            'tone' => 'nullable|string|in:Professional,Creative,Direct'
        ]);

        try {
            $bulletPoints = $request->input('bulletPoints');
            $tone = $request->input('tone', 'Professional');

            $prompt = $this->buildBulletPointsPrompt($bulletPoints, $tone);
            
            $response = $this->geminiService->generateContent($prompt);

            return response()->json([
                'success' => true,
                'improvedBullets' => $this->parseBulletPoints($response),
                'message' => 'Bullet points improved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Bullet Points Improvement Error', [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to improve bullet points',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function analyzeSkillGap(Request $request): JsonResponse
    {
        $request->validate([
            'currentSkills' => 'required|array',
            'jobDescription' => 'required|string'
        ]);

        try {
            $currentSkills = $request->input('currentSkills');
            $jobDescription = $request->input('jobDescription');

            $prompt = $this->buildSkillGapPrompt($currentSkills, $jobDescription);
            
            $response = $this->geminiService->generateContent($prompt);

            return response()->json([
                'success' => true,
                'analysis' => $this->parseSkillGapAnalysis($response),
                'message' => 'Skill gap analysis completed'
            ]);

        } catch (\Exception $e) {
            Log::error('Skill Gap Analysis Error', [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to analyze skill gap',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    private function buildGenerateResumePrompt(?string $resumeContext, ?string $jobDescription, string $tone, array $currentData): string
    {
        $resumeContext = $resumeContext ?? '';
        $jobDescription = $jobDescription ?? '';
        
        $toneInstructions = [
            'Professional' => 'formal, corporate, and achievement-focused',
            'Creative' => 'dynamic, engaging, and personality-driven',
            'Direct' => 'concise, impactful, and results-oriented'
        ];

        $toneStyle = $toneInstructions[$tone] ?? $toneInstructions['Professional'];

        return <<<PROMPT
You are an expert resume writer. Generate a complete, ATS-optimized resume based on the provided information.

**Current Resume Context:**
{$resumeContext}

**Target Job Description:**
{$jobDescription}

**Writing Tone:** {$tone} ({$toneStyle})

**Instructions:**
1. Extract and enhance key information from the resume context
2. Tailor content to match the job description keywords
3. Use strong action verbs and quantify achievements where possible
4. Generate professional summary, work experience bullets, and highlight relevant skills
5. Ensure ATS compatibility (avoid tables, graphics, use standard headings)

**Return response in this JSON format:**
{
    "summary": "Professional summary paragraph",
    "experience": [
        {
            "position": "Job Title",
            "company": "Company Name",
            "startDate": "Month Year",
            "endDate": "Month Year",
            "location": "City, State",
            "responsibilities": ["Bullet point 1", "Bullet point 2", "Bullet point 3"]
        }
    ],
    "skills": {
        "backend": ["skill1", "skill2"],
        "frontend": ["skill1", "skill2"],
        "devops": ["skill1", "skill2"],
        "other": ["skill1", "skill2"]
    },
    "atsScore": 85
}

Generate a compelling, tailored resume now.
PROMPT;
    }

    private function buildATSOptimizationPrompt(array $currentResume, ?string $jobDescription): string
    {
        $jobDescription = $jobDescription ?? '';
        $resumeJson = json_encode($currentResume, JSON_PRETTY_PRINT);

        return <<<PROMPT
You are an ATS (Applicant Tracking System) optimization expert. Analyze the current resume and provide specific suggestions to improve its ATS score.

**Current Resume:**
{$resumeJson}

**Job Description:**
{$jobDescription}

**Analyze and provide:**
1. Missing keywords from the job description
2. Formatting issues that could hurt ATS parsing
3. Suggested keyword additions with where to place them
4. Skills that should be emphasized or added
5. Overall ATS score (0-100)

**Return response in this JSON format:**
{
    "atsScore": 85,
    "missingKeywords": ["keyword1", "keyword2"],
    "suggestions": [
        {"type": "keyword", "action": "Add 'Docker' to DevOps skills"},
        {"type": "format", "action": "Use standard date format (Month Year)"}
    ],
    "skillsToAdd": ["skill1", "skill2"],
    "priorityChanges": ["Change 1", "Change 2"]
}

Analyze now.
PROMPT;
    }

    private function buildBulletPointsPrompt(array $bulletPoints, string $tone): string
    {
        $bullets = implode("\n", array_map(fn($b, $i) => ($i + 1) . ". {$b}", $bulletPoints, array_keys($bulletPoints)));

        return <<<PROMPT
You are an expert resume writer. Rewrite these bullet points to be more impactful, using strong action verbs and quantifiable achievements.

**Current Bullet Points:**
{$bullets}

**Tone:** {$tone}

**Instructions:**
1. Start each bullet with a strong action verb
2. Include metrics and quantifiable results where possible
3. Focus on impact and achievements, not just responsibilities
4. Keep bullets concise (1-2 lines max)
5. Use {$tone} language

**Return response in this JSON format:**
{
    "improved": [
        "Improved bullet point 1",
        "Improved bullet point 2",
        "Improved bullet point 3"
    ]
}

Rewrite now.
PROMPT;
    }

    private function buildSkillGapPrompt(array $currentSkills, ?string $jobDescription): string
    {
        $jobDescription = $jobDescription ?? '';
        $skillsJson = json_encode($currentSkills, JSON_PRETTY_PRINT);

        return <<<PROMPT
You are a career coach analyzing skill gaps between a candidate's current skills and a job description.

**Current Skills:**
{$skillsJson}

**Job Description:**
{$jobDescription}

**Analyze and provide:**
: Required skills from job description that are missing
2. Skills the candidate has that match the job
3. Nice-to-have skills that would strengthen the application
4. Priority order for learning missing skills

**Return response in this JSON format:**
{
    "matchingSkills": ["skill1", "skill2"],
    "missingSkills": ["skill1", "skill2"],
    "recommendedSkills": ["skill1", "skill2"],
    "matchPercentage": 75,
    "suggestions": [
        "Learn Docker and Kubernetes for DevOps role",
        "Add React.js experience to frontend skills"
    ]
}

Analyze now.
PROMPT;
    }

    private function parseAIResponse(string $response): array
    {
        // Try to extract JSON from response
        if (preg_match('/\{[\s\S]*\}/', $response, $matches)) {
            $decoded = json_decode($matches[0], true);
            if ($decoded) {
                return $decoded;
            }
        }

        // Fallback: return raw response
        return ['rawResponse' => $response];
    }

    private function parseATSSuggestions(string $response): array
    {
        return $this->parseAIResponse($response);
    }

    private function parseBulletPoints(string $response): array
    {
        $parsed = $this->parseAIResponse($response);
        return $parsed['improved'] ?? [];
    }

    private function parseSkillGapAnalysis(string $response): array
    {
        return $this->parseAIResponse($response);
    }
}
