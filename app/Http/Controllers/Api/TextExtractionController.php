<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GeminiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TextExtractionController extends Controller
{
    protected GeminiService $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    public function extract(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx,txt|max:10240', // Max 10MB
        ]);

        try {
            $file = $request->file('file');
            $extension = strtolower($file->getClientOriginalExtension());
            $tempPath = $file->getRealPath();

            $text = '';

            switch ($extension) {
                case 'pdf':
                    $text = $this->geminiService->extractTextFromPdf($tempPath);
                    break;
                case 'docx':
                case 'doc':
                    $text = $this->geminiService->extractTextFromDocx($tempPath);
                    break;
                case 'txt':
                    $text = file_get_contents($tempPath);
                    break;
                default:
                    return response()->json(['error' => 'Unsupported file type'], 422);
            }

            if (empty($text)) {
                return response()->json(['error' => 'Could not extract text from file'], 422);
            }

            return response()->json([
                'success' => true,
                'text' => trim($text),
                'fileName' => $file->getClientOriginalName(),
                'fileSize' => $file->getSize(),
            ]);

        } catch (\Exception $e) {
            Log::error('Text extraction error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Failed to extract text from file',
                'message' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}
