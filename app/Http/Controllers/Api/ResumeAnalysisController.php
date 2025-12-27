<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreResumeAnalysisRequest;
use App\Models\ResumeAnalysis;
use App\Services\GeminiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ResumeAnalysisController extends Controller
{
    protected GeminiService $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    public function store(StoreResumeAnalysisRequest $request): JsonResponse
    {
        try {
            $resumeContent = '';
            $fileName = null;
            $filePath = null;
            $fileSize = null;
            $pastedContent = null;

            // Handle file upload
            if ($request->input_type === 'upload' && $request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = $file->getClientOriginalName();
                $fileSize = $file->getSize();
                
                // Store file
                $filePath = $file->store('resume-analyses', 'public');
                $fullPath = Storage::disk('public')->path($filePath);

                // Extract text based on file type
                $extension = $file->getClientOriginalExtension();
                
                switch (strtolower($extension)) {
                    case 'pdf':
                        $resumeContent = $this->geminiService->extractTextFromPdf($fullPath);
                        break;
                    case 'docx':
                        $resumeContent = $this->geminiService->extractTextFromDocx($fullPath);
                        break;
                    case 'doc':
                        $resumeContent = $this->geminiService->extractTextFromDocx($fullPath);
                        break;
                    case 'txt':
                        $resumeContent = file_get_contents($fullPath);
                        break;
                    default:
                        return response()->json(['error' => 'Unsupported file type'], 422);
                }

                if (empty($resumeContent)) {
                    return response()->json(['error' => 'Could not extract text from file'], 422);
                }
            }
            // Handle pasted content
            elseif ($request->input_type === 'paste') {
                $resumeContent = $request->input('content');
                $pastedContent = $resumeContent;
            }

            // Analyze with Gemini AI
            $aiAnalysis = $this->geminiService->analyzeResume(
                $resumeContent,
                $request->input('job_description')
            );

            // Calculate keyword match percentage
            $totalKeywords = count($aiAnalysis['matched_keywords']) + count($aiAnalysis['missing_keywords']);
            $keywordMatchPercentage = $totalKeywords > 0 
                ? round((count($aiAnalysis['matched_keywords']) / $totalKeywords) * 100) 
                : 0;

            // Create analysis record
            $analysis = ResumeAnalysis::create([
                'user_id' => Auth::id(),
                'session_id' => $request->hasSession() ? $request->session()->getId() : Str::uuid()->toString(),
                'input_type' => $request->input_type,
                'file_name' => $fileName,
                'file_path' => $filePath,
                'file_size' => $fileSize,
                'pasted_content' => $pastedContent,
                'ats_score' => $aiAnalysis['ats_score'],
                'matched_keywords' => $aiAnalysis['matched_keywords'],
                'missing_keywords' => $aiAnalysis['missing_keywords'],
                'formatting_checks' => $aiAnalysis['formatting_checks'],
                'content_analysis' => $aiAnalysis['content_analysis'],
                'critical_issues' => $aiAnalysis['critical_issues'],
                'experience_level' => $aiAnalysis['experience_level'],
                'word_count' => $aiAnalysis['word_count'],
                'keyword_match_percentage' => $keywordMatchPercentage,
                'improvement_suggestions' => implode("\n", $aiAnalysis['improvement_suggestions']),
                'raw_ai_response' => $aiAnalysis['raw_response'] ?? null,
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $analysis->id,
                    'ats_score' => $analysis->ats_score,
                    'atsScore' => $analysis->ats_score, // Alias for frontend consistency
                    'score_grade' => $analysis->score_grade,
                    'matched_keywords' => $analysis->matched_keywords,
                    'missing_keywords' => $analysis->missing_keywords,
                    'formatting_checks' => $analysis->formatting_checks,
                    'content_analysis' => $analysis->content_analysis,
                    'critical_issues' => $analysis->critical_issues,
                    'experience_level' => $analysis->experience_level,
                    'word_count' => $analysis->word_count,
                    'keyword_match_percentage' => $analysis->keyword_match_percentage,
                    'improvement_suggestions' => explode("\n", $analysis->improvement_suggestions),
                ]
            ], 201);

        } catch (\Exception $e) {
            \Log::error('Resume Analysis Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Failed to analyze resume. Please try again.',
                'message' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function index(Request $request): JsonResponse
    {
        $query = ResumeAnalysis::with('user');

        if (Auth::check()) {
            $query->where('user_id', Auth::id());
        } else {
            $query->where('session_id', $request->session()->getId());
        }

        $analyses = $query->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($analyses);
    }

    public function show(int $id): JsonResponse
    {
        $analysis = ResumeAnalysis::findOrFail($id);

        // Check authorization
        if (Auth::check() && $analysis->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $analysis
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $analysis = ResumeAnalysis::findOrFail($id);

        // Check authorization
        if (Auth::check() && $analysis->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Delete file if exists
        if ($analysis->file_path) {
            Storage::disk('public')->delete($analysis->file_path);
        }

        $analysis->delete();

        return response()->json([
            'success' => true,
            'message' => 'Analysis deleted successfully'
        ]);
    }
}

