<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected string $apiKey;
    protected string $model;
    protected string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
        $this->model = config('services.gemini.model', 'gemini-pro');
    }

    public function analyzeResume(string $resumeContent, ?string $jobDescription = null): array
    {
        $prompt = $this->buildResumeAnalysisPrompt($resumeContent, $jobDescription);

        try {
            $response = Http::timeout(45)
                ->post("{$this->baseUrl}{$this->model}:generateContent?key={$this->apiKey}", [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.1,
                        'topK' => 10,
                        'topP' => 0.7,
                        'maxOutputTokens' => 8192,
                    ],
                    'safetySettings' => [
                        [
                            'category' => 'HARM_CATEGORY_HARASSMENT',
                            'threshold' => 'BLOCK_NONE'
                        ],
                        [
                            'category' => 'HARM_CATEGORY_HATE_SPEECH',
                            'threshold' => 'BLOCK_NONE'
                        ],
                        [
                            'category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT',
                            'threshold' => 'BLOCK_NONE'
                        ],
                        [
                            'category' => 'HARM_CATEGORY_DANGEROUS_CONTENT',
                            'threshold' => 'BLOCK_NONE'
                        ]
                    ]
                ]);

            if (!$response->successful()) {
                Log::error('Gemini API Error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                throw new \Exception('Failed to analyze resume with Gemini API');
            }

            $data = $response->json();
            
            // Log token usage
            if (isset($data['usageMetadata'])) {
                Log::info('Gemini Token Usage', [
                    'prompt_tokens' => $data['usageMetadata']['promptTokenCount'] ?? 0,
                    'candidates_tokens' => $data['usageMetadata']['candidatesTokenCount'] ?? 0,
                    'total_tokens' => $data['usageMetadata']['totalTokenCount'] ?? 0,
                    'model' => $this->model
                ]);
            }
            
            $aiResponse = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
            
            if (empty($aiResponse)) {
                Log::error('Empty AI response', ['finish_reason' => $data['candidates'][0]['finishReason'] ?? 'unknown']);
                throw new \Exception('Gemini API returned empty response');
            }

            return $this->parseAIResponse($aiResponse);
        } catch (\Exception $e) {
            Log::error('Resume Analysis Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    protected function buildResumeAnalysisPrompt(string $resumeContent, ?string $jobDescription): string
    {
        // Truncate resume content if too long to save tokens
        $maxResumeLength = 3000;
        if (strlen($resumeContent) > $maxResumeLength) {
            $resumeContent = substr($resumeContent, 0, $maxResumeLength) . '... [truncated]';
        }

        $prompt = "ATS resume analysis. Output ONLY compact JSON.\n\n";
        $prompt .= "RESUME:\n{$resumeContent}\n\n";

        if ($jobDescription) {
            $jobDescription = substr($jobDescription, 0, 800);
            $prompt .= "JOB:\n{$jobDescription}\n\n";
        }

        $prompt .= "Return compact JSON (no spaces/newlines):\n";
        $prompt .= '{"ats_score":75,"matched_keywords":["Laravel"],"missing_keywords":["Docker"],"experience_level":"Senior","word_count":500,"formatting_checks":[{"name":"Contact","status":"pass","detail":"OK","icon":"fa-check"}],"critical_issues":[],"content_analysis":{"action_verbs_percentage":80,"quantifiable_results_percentage":60,"avg_bullet_length":8,"reading_level":"Grade 11"},"improvement_suggestions":["Add metrics"]}' . "\n\n";
        $prompt .= "Rules: Score 0-100. Find 8-12 matched keywords, 5-8 missing. List 4-5 format checks (short details). Max 2 critical issues. Give 3-5 brief suggestions (under 50 chars each). Return minified JSON only.";

        return $prompt;
    }

    protected function parseAIResponse(string $response): array
    {
        // Clean up response more aggressively
        $response = preg_replace('/^```json\s*/i', '', $response);
        $response = preg_replace('/```\s*$/i', '', $response);
        $response = preg_replace('/^```\s*/i', '', $response);
        $response = trim($response);
        
        // Remove any text before first {
        $jsonStart = strpos($response, '{');
        if ($jsonStart !== false && $jsonStart > 0) {
            $response = substr($response, $jsonStart);
        }
        
        // Remove any text after last }
        $jsonEnd = strrpos($response, '}');
        if ($jsonEnd !== false && $jsonEnd < strlen($response) - 1) {
            $response = substr($response, 0, $jsonEnd + 1);
        }

        try {
            $data = json_decode($response, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('JSON Parse Error', [
                    'error' => json_last_error_msg(),
                    'response_length' => strlen($response),
                    'first_200_chars' => substr($response, 0, 200),
                    'last_100_chars' => substr($response, -100)
                ]);
                throw new \Exception('Invalid JSON response from AI');
            }

            // Ensure all required fields exist with defaults
            return [
                'ats_score' => $data['ats_score'] ?? 0,
                'matched_keywords' => $data['matched_keywords'] ?? [],
                'missing_keywords' => $data['missing_keywords'] ?? [],
                'experience_level' => $data['experience_level'] ?? 'Unknown',
                'word_count' => $data['word_count'] ?? 0,
                'formatting_checks' => $data['formatting_checks'] ?? [],
                'critical_issues' => $data['critical_issues'] ?? [],
                'content_analysis' => $data['content_analysis'] ?? [
                    'action_verbs_percentage' => 0,
                    'quantifiable_results_percentage' => 0,
                    'avg_bullet_length' => 0,
                    'reading_level' => 'Unknown'
                ],
                'improvement_suggestions' => $data['improvement_suggestions'] ?? [],
                'raw_response' => $response
            ];
        } catch (\Exception $e) {
            Log::error('Failed to parse AI response', ['error' => $e->getMessage()]);

            // Return minimal fallback
            return [
                'ats_score' => 0,
                'matched_keywords' => [],
                'missing_keywords' => [],
                'experience_level' => 'Unknown',
                'word_count' => 0,
                'formatting_checks' => [],
                'critical_issues' => [['title' => 'Analysis Error', 'description' => 'Unable to parse AI response']],
                'content_analysis' => [
                    'action_verbs_percentage' => 0,
                    'quantifiable_results_percentage' => 0,
                    'avg_bullet_length' => 0,
                    'reading_level' => 'Unknown'
                ],
                'improvement_suggestions' => ['Please try again'],
                'raw_response' => substr($response, 0, 500)
            ];
        }
    }

    public function extractTextFromPdf(string $filePath): string
    {
        try {
            if (!file_exists($filePath)) {
                throw new \Exception('File not found');
            }

            // Use Spatie PDF to Text for better extraction
            $text = \Spatie\PdfToText\Pdf::getText($filePath);
            
            return $text;
        } catch (\Exception $e) {
            Log::error('PDF extraction failed', ['error' => $e->getMessage()]);
            
            // Fallback to smalot/pdfparser if spatie fails
            try {
                $parser = new \Smalot\PdfParser\Parser();
                $pdf = $parser->parseFile($filePath);
                return $pdf->getText();
            } catch (\Exception $fallbackError) {
                Log::error('Fallback PDF extraction also failed', ['error' => $fallbackError->getMessage()]);
                return '';
            }
        }
    }

    public function extractTextFromDocx(string $filePath): string
    {
        // For DOCX files, use PhpOffice/PhpWord
        // Install: composer require phpoffice/phpword
        try {
            if (!file_exists($filePath)) {
                throw new \Exception('File not found');
            }

            $phpWord = \PhpOffice\PhpWord\IOFactory::load($filePath);
            $text = '';

            foreach ($phpWord->getSections() as $section) {
                foreach ($section->getElements() as $element) {
                    if (method_exists($element, 'getText')) {
                        $text .= $element->getText() . "\n";
                    }
                }
            }

            return $text;
        } catch (\Exception $e) {
            Log::error('DOCX extraction failed', ['error' => $e->getMessage()]);
            return '';
        }
    }
}
