<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Browsershot\Browsershot;

class ResumeHTMLExportController extends Controller
{
    /**
     * Export resume as PDF using Browsershot (captures exact template)
     */
    public function exportWithTemplate(Request $request)
    {
        $request->validate([
            'html' => 'required|string',
            'css' => 'required|string',
        ]);

        try {
            $html = $request->html;
            $css = $request->css;
            $filename = $request->filename ?? 'resume.pdf';

            // Create complete HTML document
            $fullHtml = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        
        @page {
            size: A4;
            margin: 0;
        }
        
        body {
            margin: 0;
            padding: 0;
        }
        
        {$css}
    </style>
</head>
<body>
    {$html}
</body>
</html>
HTML;

            // Generate PDF using Browsershot
            $pdf = Browsershot::html($fullHtml)
                ->showBackground()
                ->format('A4')
                ->margins(0, 0, 0, 0)
                ->emulateMedia('print')
                ->waitUntilNetworkIdle()
                ->setOption('printBackground', true)
                ->setOption('preferCSSPageSize', true)
                ->pages('1-99') // Capture up to 99 pages
                ->pdf();

            return response($pdf, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);

        } catch (\Exception $e) {
            \Log::error('PDF Export Error with Browsershot', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate PDF: ' . $e->getMessage()
            ], 500);
        }
    }
}
