<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ResumeExportController extends Controller
{
    /**
     * Export resume as PDF
     */
    public function exportPdf(Request $request)
    {
        $request->validate([
            'resume_data' => 'required|array',
            'template' => 'string|nullable',
        ]);

            $resumeData = $request->resume_data;
        $template = $request->template ?? 'default';
        $accentColor = $request->accent_color ?? '#6366f1';
        $removeBranding = $request->boolean('remove_branding');
        $achievements = $request->achievements ?? [];

        // Add achievements to resume data if not already included
        if (!isset($resumeData['achievements'])) {
            $resumeData['achievements'] = $achievements;
        }

        try {
            // Generate PDF from blade template
            $pdf = Pdf::loadView('pdf.resume', [
                'data' => $resumeData,
                'template' => $template,
                'accentColor' => $accentColor,
                'removeBranding' => $removeBranding
            ]);

            // Configure PDF settings
            $pdf->setPaper('a4', 'portrait');
            $pdf->setOption('margin-top', 10);
            $pdf->setOption('margin-bottom', 10);
            $pdf->setOption('margin-left', 15);
            $pdf->setOption('margin-right', 15);
            
            // Generate filename from user name or default
            $filename = 'resume.pdf';
            if (isset($resumeData['personal']['name'])) {
                $name = preg_replace('/[^A-Za-z0-9\-]/', '_', $resumeData['personal']['name']);
                $filename = $name . '_Resume.pdf';
            }
            
            // Return PDF download
            return $pdf->download($filename);

        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('PDF Export Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'resume_data_keys' => array_keys($resumeData),
                'has_achievements' => isset($resumeData['achievements']),
                'template' => $template
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate PDF: ' . $e->getMessage(),
                'error' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }
    }
}
