<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected string $apiKey;
    protected string $model;
    protected string $textPhrasesModel;
    protected string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
        $this->model = config('services.gemini.model', 'gemini-pro');
        $this->textPhrasesModel = config('services.gemini.text_phrases_model', 'gemini-flash-latest');

    }

    /**
     * Generic method to generate content from Gemini API
     */
    public function generateContent(string $prompt, array $config = []): string
    {
        try {
            $defaultConfig = [
                'temperature' => 0.7,
                'topK' => 40,
                'topP' => 0.95,
                'maxOutputTokens' => 8192,
            ];

            $generationConfig = array_merge($defaultConfig, $config);

            $response = Http::timeout(60)
                ->post("{$this->baseUrl}{$this->model}:generateContent?key={$this->apiKey}", [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ],
                    'generationConfig' => $generationConfig,
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

            if ($response->failed()) {
                Log::error('Gemini API Error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                throw new \Exception('Gemini API request failed: ' . $response->body());
            }

            $data = $response->json();
            
            if (!isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                Log::error('Unexpected Gemini API response structure', ['data' => $data]);
                throw new \Exception('Unexpected API response structure');
            }

            return $data['candidates'][0]['content']['parts'][0]['text'];

        } catch (\Exception $e) {
            Log::error('Gemini generateContent error', [
                'message' => $e->getMessage(),
                'prompt_length' => strlen($prompt)
            ]);
            throw $e;
        }
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

            // Try smalot/pdfparser first (works locally without external dependencies)
            try {
                $parser = new \Smalot\PdfParser\Parser();
                $pdf = $parser->parseFile($filePath);
                $text = $pdf->getText();
                
                if (!empty(trim($text))) {
                    Log::info('PDF extracted successfully using smalot/pdfparser');
                    return $text;
                }
            } catch (\Exception $smalotError) {
                Log::warning('Smalot PDF parser failed, trying Spatie', ['error' => $smalotError->getMessage()]);
            }

            // Fallback to Spatie PDF to Text (requires pdftotext binary)
            try {
                $text = \Spatie\PdfToText\Pdf::getText($filePath);
                
                if (!empty(trim($text))) {
                    Log::info('PDF extracted successfully using Spatie PDF to Text');
                    return $text;
                }
            } catch (\Exception $spatieError) {
                Log::warning('Spatie PDF extraction failed', ['error' => $spatieError->getMessage()]);
            }
            
            // If both methods failed or returned empty text
            Log::error('All PDF extraction methods failed or returned empty text');
            return '';
            
        } catch (\Exception $e) {
            Log::error('PDF extraction failed', ['error' => $e->getMessage()]);
            return '';
        }
    }

    public function extractTextFromDocx(string $filePath): string
    {
        try {
            if (!file_exists($filePath)) {
                throw new \Exception('File not found');
            }

            $phpWord = \PhpOffice\PhpWord\IOFactory::load($filePath);
            $text = '';
            $elementCount = 0;

            foreach ($phpWord->getSections() as $section) {
                foreach ($section->getElements() as $element) {
                    $elementCount++;
                    
                    // Handle TextRun elements
                    if ($element instanceof \PhpOffice\PhpWord\Element\TextRun) {
                        foreach ($element->getElements() as $textElement) {
                            if (method_exists($textElement, 'getText')) {
                                $text .= $textElement->getText() . ' ';
                            }
                        }
                        $text .= "\n";
                    }
                    // Handle Text elements
                    elseif ($element instanceof \PhpOffice\PhpWord\Element\Text) {
                        $text .= $element->getText() . "\n";
                    }
                    // Handle Table elements
                    elseif ($element instanceof \PhpOffice\PhpWord\Element\Table) {
                        foreach ($element->getRows() as $row) {
                            foreach ($row->getCells() as $cell) {
                                foreach ($cell->getElements() as $cellElement) {
                                    if (method_exists($cellElement, 'getText')) {
                                        $text .= $cellElement->getText() . ' ';
                                    }
                                }
                            }
                            $text .= "\n";
                        }
                    }
                    // Handle ListItem elements
                    elseif ($element instanceof \PhpOffice\PhpWord\Element\ListItem) {
                        if (method_exists($element, 'getText')) {
                            $text .= '• ' . $element->getText() . "\n";
                        }
                    }
                    // Generic fallback for any element with getText method
                    elseif (method_exists($element, 'getText')) {
                        $text .= $element->getText() . "\n";
                    }
                }
            }

            Log::info('DOCX extracted successfully', [
                'elements_processed' => $elementCount,
                'text_length' => strlen($text)
            ]);

            return trim($text);
        } catch (\Exception $e) {
            Log::error('DOCX extraction failed', ['error' => $e->getMessage()]);
            return '';
        }
    }

    /**
     * Parse resume text into structured JSON data using Gemini AI
     */
    public function parseResumeToStructuredData(string $resumeText): array
    {
        try {
            $prompt = $this->buildResumeParsingPrompt($resumeText);
            
            $response = Http::timeout(60)
                ->post("{$this->baseUrl}{$this->textPhrasesModel}:generateContent?key={$this->apiKey}", [
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
                Log::error('Gemini API Error in parseResumeToStructuredData', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                throw new \Exception('Failed to parse resume with Gemini API');
            }

            $data = $response->json();
            $aiResponse = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
            
            if (empty($aiResponse)) {
                throw new \Exception('Gemini API returned empty response');
            }

            return $this->parseStructuredResumeResponse($aiResponse);
            
        } catch (\Exception $e) {
            Log::error('Resume parsing error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    protected function buildResumeParsingPrompt(string $resumeText): string
    {
        // Truncate if too long
        if (strlen($resumeText) > 4000) {
            $resumeText = substr($resumeText, 0, 4000) . '... [truncated]';
        }

        $prompt = "Extract and structure resume information from the following text. Return ONLY valid JSON.\n\n";
        $prompt .= "RESUME TEXT:\n{$resumeText}\n\n";
        $prompt .= "Extract the following information and return as JSON:\n\n";
        $prompt .= json_encode([
            'fullName' => 'Full name of the person',
            'title' => 'Professional title or current role',
            'email' => 'Email address',
            'phone' => 'Phone number',
            'location' => 'City/Location',
            'linkedin' => 'LinkedIn profile URL (if available)',
            'github' => 'GitHub profile URL (if available)',
            'portfolio' => 'Portfolio website URL (if available)',
            'summary' => 'Professional summary or objective (2-3 sentences)',
            'atsScore' => 'A score from 0-100 indicating overall resume quality/completeness without a job description',
            'skills' => [
                'backend' => ['skill1', 'skill2'],
                'frontend' => ['skill1', 'skill2'],
                'devops' => ['skill1', 'skill2'],
                'other' => ['skill1', 'skill2']
            ],
            'experience' => [
                [
                    'position' => 'Job title',
                    'company' => 'Company name',
                    'location' => 'City, State',
                    'startDate' => 'Month Year',
                    'endDate' => 'Month Year or Present',
                    'current' => false,
                    'responsibilities' => ['Achievement 1', 'Achievement 2', 'Achievement 3']
                ]
            ],
            'education' => [
                [
                    'degree' => 'Degree name',
                    'institution' => 'University/College name',
                    'year' => 'Graduation year',
                    'percentage' => 'GPA or percentage (if available)'
                ]
            ],
            'achievements' => ['Achievement 1', 'Achievement 2']
        ], JSON_PRETTY_PRINT);
        
        $prompt .= "\n\nRULES:\n";
        $prompt .= "1. Extract ALL available information from the resume\n";
        $prompt .= "2. Categorize skills appropriately (backend: PHP/Laravel/Python, frontend: React/Vue/JS, devops: Docker/AWS/CI-CD, other: everything else)\n";
        $prompt .= "3. For experience, extract 3-5 key responsibilities/achievements per role\n";
        $prompt .= "4. Use 'Present' for current positions\n";
        $prompt .= "5. If information is not available, use empty string or empty array\n";
        $prompt .= "6. Return ONLY the JSON object, no markdown formatting or extra text\n";
        $prompt .= "7. Ensure all JSON is properly formatted and valid\n";

        return $prompt;
    }

    protected function parseStructuredResumeResponse(string $response): array
    {
        // Clean up response
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
                Log::error('JSON Parse Error in resume parsing', [
                    'error' => json_last_error_msg(),
                    'response_preview' => substr($response, 0, 500)
                ]);
                throw new \Exception('Invalid JSON response from AI');
            }

            // Ensure structure with defaults
            return [
                'fullName' => $data['fullName'] ?? '',
                'title' => $data['title'] ?? '',
                'email' => $data['email'] ?? '',
                'phone' => $data['phone'] ?? '',
                'location' => $data['location'] ?? '',
                'linkedin' => $data['linkedin'] ?? '',
                'github' => $data['github'] ?? '',
                'portfolio' => $data['portfolio'] ?? '',
                'summary' => $data['summary'] ?? '',
                'atsScore' => $data['atsScore'] ?? 0,
                'skills' => [
                    'backend' => $data['skills']['backend'] ?? [],
                    'frontend' => $data['skills']['frontend'] ?? [],
                    'devops' => $data['skills']['devops'] ?? [],
                    'other' => $data['skills']['other'] ?? []
                ],
                'experience' => $data['experience'] ?? [],
                'education' => $data['education'] ?? [],
                'achievements' => $data['achievements'] ?? []
            ];
            
        } catch (\Exception $e) {
            Log::error('Failed to parse structured resume response', [
                'error' => $e->getMessage()
            ]);
            
            // Return empty structure
            return [
                'fullName' => '',
                'title' => '',
                'email' => '',
                'phone' => '',
                'location' => '',
                'linkedin' => '',
                'github' => '',
                'portfolio' => '',
                'summary' => '',
                'atsScore' => 0,
                'skills' => [
                    'backend' => [],
                    'frontend' => [],
                    'devops' => [],
                    'other' => []
                ],
                'experience' => [],
                'education' => [],
                'achievements' => []
            ];
        }
    }
}
