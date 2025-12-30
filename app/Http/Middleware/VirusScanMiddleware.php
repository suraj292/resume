<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class VirusScanMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if request has file uploads
        if ($request->hasFile('file') || $request->hasFile('resume')) {
            $file = $request->file('file') ?? $request->file('resume');
            
            if ($file) {
                // Get file info
                $filename = $file->getClientOriginalName();
                $size = $file->getSize();
                $mimeType = $file->getMimeType();
                
                // Basic validation
                $maxSize = 10 * 1024 * 1024; // 10MB
                if ($size > $maxSize) {
                    return response()->json([
                        'success' => false,
                        'message' => 'File size exceeds maximum allowed size of 10MB'
                    ], 413);
                }
                
                // Allowed MIME types for resumes
                $allowedMimeTypes = [
                    'application/pdf',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'text/plain',
                ];
                
                if (!in_array($mimeType, $allowedMimeTypes)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid file type. Only PDF, DOC, DOCX, and TXT files are allowed.'
                    ], 415);
                }
                
                // TODO: Integrate with ClamAV or cloud-based virus scanner
                // For now, we'll use basic checks
                
                // Check for EICAR test file (standard virus test file)
                $tempPath = $file->getRealPath();
                $content = file_get_contents($tempPath, false, null, 0, 1024);
                
                if (str_contains($content, 'EICAR-STANDARD-ANTIVIRUS-TEST-FILE')) {
                    Log::warning('Virus detected in uploaded file', [
                        'filename' => $filename,
                        'ip' => $request->ip()
                    ]);
                    
                    return response()->json([
                        'success' => false,
                        'message' => 'File failed security scan. Please upload a clean file.'
                    ], 400);
                }
                
                // Log successful scan
                Log::info('File passed virus scan', [
                    'filename' => $filename,
                    'size' => $size,
                    'mime_type' => $mimeType
                ]);
            }
        }
        
        return $next($request);
    }
}
